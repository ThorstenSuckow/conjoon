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
     * Renames the folder found under the specified path.
     * This method does automatically determine whether the related account
     * is an IMAP account and invokes server communications accordingly.
     *
     * @param string $name The new name for the folder
     * @param string $path The path to the folder that should get renamed.
     * @param integer $userId The id of the user the renamed folder should
     * belong to
     *
     * @return mixed A Conjoon_Modules_Groupware_Email_Account_Dto with the
     * data for the renamed folder, or false if the operation did not succeed
     *
     * @throws Exception
     */
    public function renameFolderForPathAndUserId($name, $path, $userId)
    {
        $name   = trim((string)$name);
        $path   = trim((string)$path);
        $userId = (int)$userId;

        if ($userId <= 0) {
            throw new InvalidArgumentException(
                "Invalid argument supplied, userId was \"$userId\""
            );
        }
        if ($path == "") {
            throw new InvalidArgumentException(
                "Invalid argument supplied, path was \"$path\""
            );
        }
        if ($name == "") {
            throw new InvalidArgumentException(
                "Invalid argument supplied, name was \"$name\""
            );
        }

        $pathInfo = $this->_extractPathInfo($path);

        if (empty($pathInfo)) {
            throw new InvalidArgumentException(
                "Could not operate on path - path was \"$path\""
            );
        }

        if ($this->isPopAccountMappedToFolderIdForUserId($pathInfo['rootId'], $userId)) {

            // pop account mapped to folder
            $result = $this->_getFolderModel()->renameFolder(
                $pathInfo['nodeId'], $name
            );

            if ($result == 0) {
                return false;
            }

            $folderDto = $this->getLocalFolderForIdAndUserId($pathInfo['nodeId'], $userId);

            if ($folderDto == null) {
                return false;
            }

            return $folderDto;
        } else {
            $account = $this->getImapAccountForFolderIdAndUserId($pathInfo['rootId'], $userId);

            if ($account === false) {
                return false;
            }

            $result = $this->renameImapFolderForPath(
                $name, $pathInfo['path'], $account, $userId
            );

            if ($result === false) {
                return false;
            }

            // we have a string
            // return the dto for the folder

            /**
             * @see Conjoon_Modules_Groupware_Email_ImapHelper
             */
            require_once 'Conjoon/Modules/Groupware/Email/ImapHelper.php';

            /**
             * @see Zend_Mail_Storage_Imap
             */
            require_once 'Zend/Mail/Storage/Imap.php';

            $protocol = Conjoon_Modules_Groupware_Email_ImapHelper::reuseImapProtocolForAccount(
                $account
            );

            $imap = new Zend_Mail_Storage_Imap($protocol);
            $iFolders = $imap->getFolders($result);

            foreach  ($iFolders as $localName => $iFold) {
                return $this->_transformImapFolder($iFold, $account, $protocol, false);
            }
        }

        return false;
    }

    /**
     * Checks whether one or more POP accounts are mapped to this folder.
     *
     * @param integer $folderId
     * @param integer $userId
     *
     * @return boolean true if there is one ore more pop accounts mapped to
     * this id, otherwise false
     *
     * @throws InvalidArgumentException
     */
    public function isPopAccountMappedToFolderIdForUserId($folderId, $userId)
    {
        $folderId = (int)$folderId;
        $userId   = (int)$userId;

        if ($folderId <= 0) {
            throw new InvalidArgumentException(
                "Invalid argument supplied for - folderId was \"$folderId\""
            );
        }

        $accounts = $this->getAccountsForFolderIdAndUserId($folderId, $userId);

        if (empty($accounts)) {
            return false;
        }

        $account = & $accounts[0];

        if ($account->protocol === 'IMAP') {
            return false;
        }

        return true;
    }



    /**
     * Returns a Conjoon_Modules_Groupware_Email_Folder_Dto for the
     * given folder and userId. This queries the database and does not
     * consider IMAP accaounts!
     *
     * @param integer $folderId
     * @param integer $userId
     *
     * @return Conjoon_Modules_Groupware_Email_Folder_Dto or null
     *
     * @throws InvalidArgumentException
     */
    public function getLocalFolderForIdAndUserId($folderId, $userId)
    {
        $folderId = (int)$folderId;
        $userId   = (int)$userId;

        if ($userId <= 0) {
            throw new InvalidArgumentException(
                "Invalid argument supplied, userId was \"$userId\""
            );
        }

        if ($folderId <= 0) {
            throw new InvalidArgumentException(
                "Invalid argument supplied, userId was \"$userId\""
            );
        }

        $folder = $this->_getFolderDecorator()->getFolderForIdAsDto(
            $folderId, $userId
        );

        if (!$folder) {
            return null;
        }

        return $folder;
    }

    /**
     * Checks whether there is a single imap account mapped to the given
     * folder id. Returns the Conjoon_Modules_Groupware_Email_Account_Dto
     * of the account as found in the database, otherwise null.
     *
     * @param integer $folderId
     * @param integer $userId
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    public function getImapAccountForFolderIdAndUserId($folderId, $userId)
    {
        $userId   = (int)$userId;
        $folderId = (int)$folderId;

        if ($userId <= 0) {
            throw new InvalidArgumentException(
                "Invalid argument supplied, userId was \"$userId\""
            );
        }

        if ($folderId <= 0) {
            throw new InvalidArgumentException(
                "Invalid argument supplied, folderId was \"$folderId\""
            );
        }

        $accounts = $this->getAccountsForFolderIdAndUserId($folderId, $userId);

        if (empty($accounts) || count($accounts) > 1) {
            return null;
        }

        if ($accounts[0]->protocol === 'IMAP') {
            return $accounts[0];
        }

        return null;

    }

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
        $userId = (int)$userId;

        if ($userId <= 0) {
            throw new InvalidArgumentException(
                "Invalid argument supplied, userId was \"$userId\""
            );
        }

        $pathInfo = $this->_extractPathInfo($path);

        if (empty($pathInfo)) {
            // load root folders
            return $this->_getFolderDecorator()->getFoldersAsDto(0, $userId);
        }

        if ($this->isPopAccountMappedToFolderIdForUserId($pathInfo['rootId'], $userId)) {
            return $this->_getFolderDecorator()->getFoldersAsDto(
                $pathInfo['nodeId'], $userId
            );
        }

        $account = $this->getImapAccountForFolderIdAndUserId($pathInfo['rootId'], $userId);

        if ($account) {
            // we have an IMAP account. Query IMAP server for folders
            return $this->getImapFoldersForPath($account, $pathInfo['path']);
        }

        return array();
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
    public function getAccountsForFolderIdAndUserId($folderId, $userId)
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
     * Queries the IMAP server and renames the folder found in the deepest level
     * of the path.
     *
     * @param string $name
     * @param string $path
     * @param Conjoon_Modules_Groupware_Email_Account_Dto $account
     * @param integer $userId
     *
     * @return the global name of the folder is renaming was successfull,
     * otherwise false
     *
     * @throws Exception
     */
    public function renameImapFolderForPath(
        $name, $path, Conjoon_Modules_Groupware_Email_Account_Dto $account, $userId
    )
    {
        $userId   = (int)$userId;
        $name     = trim((string)$name);
        $path     = trim((string)$path);

        if ($userId <= 0) {
            throw new InvalidArgumentException(
                "Invalid argument supplied, userId was \"$userId\""
            );
        }
        if ($name == "") {
            throw new InvalidArgumentException(
                "Invalid argument supplied, name was \"$name\""
            );
        }
        if ($path == "") {
            throw new InvalidArgumentException(
                "Invalid argument supplied, path was \"$path\""
            );
        }

        /**
         * @see Conjoon_Modules_Groupware_Email_ImapHelper
         */
        require_once 'Conjoon/Modules/Groupware/Email/ImapHelper.php';

        $delim = Conjoon_Modules_Groupware_Email_ImapHelper::getFolderDelimiterForImapAccount(
            $account
        );

        if (strpos($name, $delim) !== false) {
            throw new InvalidArgumentException(
                "Sorry, it seems that \"$delim\" is reserved and may not be used within the name"
            );
        }

        if ($path == "/" || $path == $delim) {
            return false;
        } else {
            $path = ltrim(str_replace('/', $delim, $path), $delim);
        }

        $parts = explode($delim, $path);
        array_pop($parts);

        $newPath = (implode($delim, $parts) ? implode($delim, $parts) . $delim : "" ) . $name;

        $protocol = Conjoon_Modules_Groupware_Email_ImapHelper::reuseImapProtocolForAccount(
            $account
        );

        if ($protocol->rename($path, $newPath) !== true) {
            return false;
        }

        return $newPath;
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
         * @see Conjoon_Modules_Groupware_Email_ImapHelper
         */
        require_once 'Conjoon/Modules/Groupware/Email/ImapHelper.php';

        $delim = Conjoon_Modules_Groupware_Email_ImapHelper::getFolderDelimiterForImapAccount(
            $account
        );

        if ($path == "/" || $path == $delim) {
            $path = null;
        } else {
            $path = ltrim(str_replace('/', $delim, $path), $delim);
        }

        $protocol = Conjoon_Modules_Groupware_Email_ImapHelper::reuseImapProtocolForAccount(
            $account
        );

        /**
         * @see Zend_Mail_Storage_Imap
         */
        require_once 'Zend/Mail/Storage/Imap.php';

        $imap = new Zend_Mail_Storage_Imap($protocol);

        $isRootLevel = false;

        if (!$path) {
            $iFolders    = $imap->getFolders();
            $isRootLevel = true;
        } else {
            $iFolders = $imap->getFolders($path)->getChildren();
        }

        $folders = array();

        /**
         * @see Conjoon_Modules_Groupware_Email_ImapHelper
         */
        require_once 'Conjoon/Modules/Groupware/Email/ImapHelper.php';

        foreach  ($iFolders as $localName => $iFold) {
            $folders[] = $this->_transformImapFolder(
                $iFold, $account, $protocol, $isRootLevel
            );
        }

        return $folders;
    }


// -------- api

    /**
     * Gathers all needed information to tranform an imap folder to a
     * Conjoon_Modules_Groupware_Email_Folder_Dto obejct.
     *
     * @param Zend_Mail_Storage_Folder $folder
     * @param Conjoon_Modules_Groupware_Email_Account_Dto $account
     * @param Zend_Mail_Protocol_Imap $protocol
     * @param boolean $isRootLevel Whether the folder is on the first level of
     * the mailbox hierarchy
     *
     * @return Conjoon_Modules_Groupware_Email_Folder_Dto
     */
    private function _transformImapFolder(
        Zend_Mail_Storage_Folder $folder,
        Conjoon_Modules_Groupware_Email_Account_Dto $account,
        Zend_Mail_Protocol_Imap $protocol,
        $isRootLevel = false
    )
    {
            $delim = Conjoon_Modules_Groupware_Email_ImapHelper::
                     getFolderDelimiterForImapAccount($account);

            $globalName = $folder->getGlobalName();
            $path = explode($delim, $globalName);
            $path = $path[count($path)-1];

            $pendingCount = 0;

            if ($folder->isSelectable()) {
                try{
                    $protocol->select($globalName);
                    $res = $protocol->requestAndResponse('SEARCH', array('UNSEEN'));
                    if (is_array($res)) {
                        $res = $res[0];
                        if ($res[0] === 'SEARCH') {
                            array_shift($res);
                        }
                        $pendingCount = count($res);
                    }
                } catch (Exception $e) {
                    // ignore
                }
            }

            return Conjoon_Modules_Groupware_Email_ImapHelper::transformToFolderDto(
                $folder, $isRootLevel, array(
                'id'           => $account->id.'_'.$globalName,
                'idForPath'    => $path,
                'pendingCount' => $pendingCount
            ));
    }

    /**
     * Function extracts information from a given path.
     *
     * @param string $path
     *
     * @return array An array with the following key value pairs:
     *
     * If the array is empty, no further path information is available.
     * This indicates that a possible operation on all root folders
     * is requested. Otherwise, if existing, the following information
     * will be returned:
     *    'nodeId' => The id of the node, i.e. the last id found in the path
     *    'rootId' => The id of the root node of the path
     *    'path'   => The sanitized path, without "/root" and the numeric id
     *                of the root folder
     */
    private function _extractPathInfo($path)
    {
        $path = ltrim(trim((string)$path), '/root');

        if ($path == "") {
            return array();
        }

        $result = array();

        $parts = explode('/', $path);

        $result['nodeId'] = $parts[count($parts)-1];
        $result['rootId'] = array_shift($parts);
        $result['path']   = '/' . implode($parts, '/');

        return $result;
    }

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