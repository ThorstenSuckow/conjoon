<?php
/**
 * conjoon
 * (c) 2002-2010 siteartwork.de/conjoon.org
 * licensing@conjoon.org
 *
 * $Author$
 * $Id$
 * $Date$
 * $Revision$
 * $LastChangedDate$
 * $LastChangedBy$
 * $URL$
 */

/**
 * @see Conjoon_Log
 */
require_once 'Conjoon/Log.php';

/**
 *
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Conjoon_Modules_Groupware_Email_Folder_Facade {

    /**
     * @var Conjoon_Modules_Groupware_Email_Folder_Facade
     */
    private static $_instance = null;

    /**
     * @var Conjoon_Modules_Groupware_Email_Folder_Model_Folder
     */
    private $_folderModel = null;

    /**
     * @var Conjoon_Modules_Groupware_Email_Folder_Model_FoldersAccounts
     */
    private $_foldersAccountsModel = null;

    /**
     * @var Conjoon_Modules_Groupware_Email_Account_Model_Account
     */
    private $_accountModel = null;

    /**
     * @var Conjoon_BeanContext_Decorator $_accountDecorator
     */
    private $_accountDecorator = null;


    /**
     * @var Conjoon_BeanContext_Decorator $_folderDecorator
     */
    private $_folderDecorator = null;

    private function __construct()
    {

    }

    private function __clone()
    {

    }

    public static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }


// -------- public api

    /**
     * Returns the folder for the user based upon the passed parameter.
     * This method does automatically determine if the connected account
     * is an IMAP account, and if thatis the case, queries the specified
     * IMAP server for the folders.
     *
     * @param mixed  $path The path to the folder that should be opened, delimited
     * by a slash. The last token is the id of the folder that should be expanded, i.e.
     * which child folders should be loaded. The very first token should equal to "root".
     * If the first token equals to "root", all root folders for the user will be loaded.
     * @param integer $userId The id of the user
     *
     *
     * @return Conjoon_Modules_Groupware_Email_Folder_Dto
     */
    public function getFoldersForPathAndUserId($path, $userId)
    {
        $path   = ltrim(trim((string)$path), '/root');
        $userId = (int)$userId;

        if ($userId <= 0) {
            throw new InvalidArgumentException(
                "Invalid argument supplied, userId was \"$userId\""
            );
        }

        if ($path == "") {
            // load root folders
            return $this->_getFolderDecorator()->getFoldersAsDto(0, $userId);
        }

        $parts = explode('/', $path);

        $parentNodeId = $parts[count($parts)-1];
        $rootFolderId = array_shift($parts);

        $accounts = $this->getAccountsMappedToFolderId(
            $rootFolderId, $userId
        );

        $len = count($accounts);

        if ($len == 0) {
            // no accounts found. Return an empty array, i.e. no child
            // folders available
            return array();
        }

        $account =& $accounts[0];

        if ($len > 1 || ($len == 1 && $account->protocol !== 'IMAP')) {
            // multiple accounts mapped to folder. This folder should be
            // of the type accounts root. @todo Make check for folder type
            // prior to read out accounts.
            return $this->_getFolderDecorator()->getFoldersAsDto(
                $parentNodeId, $userId
            );
        }

        // we have an IMAP account. Query IMAP server for folders
        // adjust path
        $path = '/' . implode($parts, '/');

        return $this->getImapFoldersForPath($account, $path);
    }

    /**
     * Returns an array of Conjoon_Groupware_Email_Account_Dto which are mapped
     * to the specified folder id.
     *
     * @param integer $folderId The id of the folder for which all accounts
     * should be queried.
     * @param integer $userId The id of the user this folder should belong.
     *
     * @return Array
     */
    public function getAccountsMappedToFolderId($folderId, $userId)
    {
        $folderId = (int)$folderId;
        $userId   = (int)$userId;

        if ($folderId <= 0) {
            throw new InvalidArgumentException(
                "Invalid argument supplied, folderId was \"$folderId\""
            );
        }
        if ($userId <= 0) {
            throw new InvalidArgumentException(
                "Invalid argument supplied, userId was \"$userId\""
            );
        }

        $accountIds = $this->_getFoldersAccountsModel()
                           ->getAccountIdsMappedToFolderIds(array($folderId));

        $accountIds = $accountIds[$folderId];

        $len = count($accountIds);

        if ($len == 0) {
            return array();
        }

        $accounts = array();
        for ($i = 0; $i < $len; $i++) {
            $accounts[] = $this->_getAccountDecorator()->getAccountAsDto(
                $accountIds[$i], $userId
            );
        }

        return $accounts;
    }

    /**
     * Queries the IMAP server as specified in $account for child folders
     * for the specified path.
     *
     * @param Conjoon_Groupware_Email_Account_Dto $account
     * @param string $path
     *
     * @return Conjoon_Modules_Groupware_Email_Folder_Dto
     */
    public function getImapFoldersForPath(Conjoon_Modules_Groupware_Email_Account_Dto $account, $path = "")
    {
        /**
         * @see Zend_Mail_Storage_Imap
         */
        require_once 'Zend/Mail/Storage/Imap.php';

        $config = array(
            'user'     => $account->usernameInbox,
            'host'     => $account->serverInbox,
            'password' => $account->passwordInbox,
            'port'     => $account->portInbox
        );

        $cType = $account->inboxConnectionType;

        if ($cType == 'SSL' || $cType == 'TLS') {
            $config['ssl'] = $cType;
        }

        $imap = new Zend_Mail_Storage_Imap($config);

        if ($path == "" || $path == "/") {
            $iFolders = $imap->getFolders($path);
        } else {
            $iFolders = $imap->getFolders($path)->getChildren();
        }

        $folders = array();

        /**
         * @see Conjoon_Modules_Groupware_Email_Folder_Dto
         */
        require_once 'Conjoon/Modules/Groupware/Email/Folder/Dto.php';

        foreach  ($iFolders as $localName => $iFold) {

            $path = explode('/', $iFold->getGlobalName());
            $path = $path[count($path)-1];

            $tmpFolder = new Conjoon_Modules_Groupware_Email_Folder_Dto();
            $tmpFolder->id             = $account->id.'_'.$iFold->getGlobalName();
            $tmpFolder->idForPath      = $path;
            $tmpFolder->name           = $iFold->getLocalName();
            $tmpFolder->isChildAllowed = $iFold->isSelectable();
            $tmpFolder->isLocked       = !$iFold->isSelectable();
            $tmpFolder->type           = 'folder';
            //hasChildren doesnt seem to work
            $tmpFolder->childCount     = $iFold->isLeaf() ? 0 : 1;
            $tmpFolder->pendingCount   = 0;

            $folders[] = $tmpFolder;
        }

        return $folders;
    }

// -------- api

    /**
     *
     * @return Conjoon_BeanContext_Decorator
     */
    private function _getFolderDecorator()
    {
        if (!$this->_folderDecorator) {

            /**
             * @see Conjoon_BeanContext_Decorator
             */
            require_once 'Conjoon/BeanContext/Decorator.php';

            $this->_folderDecorator = new Conjoon_BeanContext_Decorator(
                $this->_getFolderModel()
            );
        }

        return $this->_folderDecorator;
    }

    /**
     *
     * @return Conjoon_BeanContext_Decorator
     */
    private function _getAccountDecorator()
    {
        if (!$this->_accountDecorator) {

            /**
             * @see Conjoon_BeanContext_Decorator
             */
            require_once 'Conjoon/BeanContext/Decorator.php';

            $this->_accountDecorator = new Conjoon_BeanContext_Decorator(
                $this->_getAccountModel()
            );
        }

        return $this->_accountDecorator;
    }

    /**
     *
     * @return Conjoon_Modules_Groupware_Email_Folder_Model_Folder
     */
    private function _getFolderModel()
    {
        if (!$this->_folderModel) {
             /**
             * @see Conjoon_Modules_Groupware_Email_Folder_Model_Folder
             */
            require_once 'Conjoon/Modules/Groupware/Email/Folder/Model/Folder.php';

            $this->_folderModel = new Conjoon_Modules_Groupware_Email_Folder_Model_Folder();
        }

        return $this->_folderModel;
    }

    /**
     *
     * @return Conjoon_Modules_Groupware_Email_Account_Model_Account
     */
    private function _getAccountModel()
    {
        if (!$this->_accountModel) {
             /**
             * @see Conjoon_Modules_Groupware_Email_Account_Model_Account
             */
            require_once 'Conjoon/Modules/Groupware/Email/Account/Model/Account.php';

            $this->_accountModel = new Conjoon_Modules_Groupware_Email_Account_Model_Account();
        }

        return $this->_accountModel;
    }

    /**
     *
     * @return Conjoon_Modules_Groupware_Email_Folder_Model_FoldersAccounts
     */
    private function _getFoldersAccountsModel()
    {
        if (!$this->_foldersAccountsModel) {
             /**
             * @see Conjoon_Modules_Groupware_Email_Folder_Model_FoldersAccounts
             */
            require_once 'Conjoon/Modules/Groupware/Email/Folder/Model/FoldersAccounts.php';

            $this->_foldersAccountsModel = new Conjoon_Modules_Groupware_Email_Folder_Model_FoldersAccounts();
        }

        return $this->_foldersAccountsModel;
    }

}