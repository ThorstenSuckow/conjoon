<?php
/**
 * conjoon
 * (c) 2002-2012 siteartwork.de/conjoon.org
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
 * @see Conjoon_Argument_Check
 */
require_once 'Conjoon/Argument/Check.php';

/**
 * @see Conjoon_Modules_Groupware_Email_Item_ItemListRequestFacade
 */
require_once 'Conjoon/Modules/Groupware/Email/Item/ItemListRequestFacade.php';

/**
 * This facade eases the access to often neededoperations on local folders/
 * mailboxes. It provides an interface to automatically establish connections
 * to either the local database or other servers for manipulation folder/
 * mailbox information.
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
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

    /**
     * @var Conjoon_Modules_Groupware_Email_Folder_FolderRootTypeBuilder $_folderRootTypeBuilder
     */
    private $_folderRootTypeBuilder = null;

    /**
     * @var Conjoon_Modules_Groupware_Email_Item_ItemListRequestFacade
     */
    private $itemListRequestFacade;

    /**
     * Enforce singleton.
     *
     */
    private function __construct()
    {
    }

    /**
     * Enforce singleton.
     *
     */
    private function __clone()
    {
    }

    /**
     *
     * @return Conjoon_Modules_Groupware_Email_Folder_Facade
     */
    public static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }


// -------- public api

    /**
     * Deletes the local folder for the user after checking if all of the
     * folders belong to the specify user. Otherwise, no folder will be deleted.
     *
     * @param integer $folderId
     * @param integer $userId
     *
     * @return boolean true if all folders, including subfolders have been
     * deleted, otherwise false
     *
     * @throws Conjoon_Argument_Exception
     */
    public function deleteLocalFolderForUser($folderId, $userId)
    {
        $data = array('folderId' => $folderId, 'userId' => $userId);

        Conjoon_Argument_Check::check(array(
            'folderId' => array(
                'type'       => 'int',
                'allowEmpty' => false
            ),
            'userId' => array(
                'type'       => 'int',
                'allowEmpty' => false
            ),
        ), $data);

        $folderId = $data['folderId'];
        $userId   = $data['userId'];

        $folderModel = $this->_getFolderModel();

        $ids = $folderModel->getChildFolderIdsAsFlatArray($folderId);

        if (!is_array($ids)) {
            return false;
        }

        $ids[] = $folderId;

        $deletable = true;

        for ($i = 0, $len = count($ids); $i < $len; $i++) {
            if (!$folderModel->isFolderDeletable($ids[$i], $userId)) {
                $deletable = false;
                break;
            }
        }

        if (!$deletable) {
            return false;
        }

        // remove from cache in any case
        /**
         * @see Conjoon_Builder_Factory
         */
        require_once 'Conjoon/Builder/Factory.php';

        /**
         * @see Conjoon_Keys
         */
        require_once 'Conjoon/Keys.php';

        $rootTypeBuilder = Conjoon_Builder_Factory::getBuilder(
            Conjoon_Keys::CACHE_EMAIL_FOLDERS_ROOT_TYPE,
            Zend_Registry::get(Conjoon_Keys::REGISTRY_CONFIG_OBJECT)->toArray(),
            $folderModel
        );

        for ($i = 0, $len = count($ids); $i < $len; $i++) {
            $rootTypeBuilder->remove(array(
                'folderId' => $ids[$i]
            ));
        }

        $folderModel->getAdapter()->beginTransaction();

        try {
            for ($i = 0, $len = count($ids); $i < $len; $i++) {
                $folderModel->deleteFolder($ids[$i], $userId, false);
            }
        } catch (Exception $e) {
            $folderModel->getAdapter()->rollBack();
            throw($e);
        }

        $folderModel->getAdapter()->commit();


        return true;
    }

    /**
     * Returns true if the folder represents a remote folder, i.e. if the folder
     * is not part of a accounts_root or root hierarchy, otherwise true.
     *
     * @param integer $folderId
     *
     * @return boolean
     *
     * @throws Conjoon_Argument_Exception, RuntimeException
     */
    public function isRemoteFolder($folderId)
    {
        $data = array('folderId' => $folderId);

        Conjoon_Argument_Check::check(array(
            'folderId' => array(
                'allowEmpty' => false,
                'type'       => 'int'
            )
        ), $data);

        $folderId = $data['folderId'];

        $ret = $this->_getFolderRootTypeBuilder()->get(array(
            'folderId' => $folderId
        ));

        if ($ret == 'accounts_root' || $ret == 'root') {
            return false;
        }

        if ($ret != 'root_remote') {
            throw new RuntimeException("Unexpected result.");
        }

        return true;
    }

    /**
     * Adds a folder with the specified name to the specified path for
     * the specified user.
     *
     * @param string $name
     * @param array $pathParts paths parts as parsed by
     * Conjoon_Text_Parser_Mail_MailboxFolderPathJsonParser
     * @param integer $userId
     *
     * @return mixed Conjoon_Modules_Groupware_Email_Folder_Dto or false
     *
     */
    public function addFolderToPathForUserId($name, Array $pathParts, $userId)
    {
        Conjoon_Argument_Check::check(array(
            'path' => array(
                'type'       => 'array',
                'allowEmpty' => false
            ),
            'nodeId' => array(
                'type'       => 'string',
                'allowEmpty' => false
            ),
            'rootId' => array(
                'type'       => 'string',
                'allowEmpty' => false
            )
        ), $pathParts);


        $userId = $this->_checkParam($userId, 'userId');
        $name   = $this->_checkParam($name, 'name');

        if (!$this->isRemoteFolder($pathParts['rootId'])) {

            $result = $this->_getFolderModel()->addFolder(
                $pathParts['nodeId'], $name, $userId
            );

            if (!$result || $result < 0) {
                return false;
            }

            $folderDto = $this->getLocalFolderForIdAndUserId($result, $userId);

            if ($folderDto == null) {
                return false;
            }

            return $folderDto;

        } else {

            $account = $this->getImapAccountForFolderIdAndUserId(
                $pathParts['rootId'], $userId
            );

            if ($account === false) {
                return false;
            }

            $result = $this->addImapFolderToPath(
                $name, $pathParts['path'], $account
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

            $protocol = Conjoon_Modules_Groupware_Email_ImapHelper
                        ::reuseImapProtocolForAccount(
                            $account
                        );

            $imap = new Zend_Mail_Storage_Imap($protocol);
            $iFolders = $imap->getFolders($result);

            foreach  ($iFolders as $localName => $iFold) {
                return $this->_transformImapFolder(
                    $iFold, $account, $protocol, false
                );
            }

        }

        return false;
    }

    /**
     * Returns the path assembled as a global imap name for the specified
     * account. This considers the delimiter as read out from the specified
     * IMAP account.
     *
     * @param Conjoon_Modules_Groupware_Email_Account_Dto $accountDto
     * @param array $pathParts
     *
     * @return string
     */
    public function getAssembledGlobalNameForAccountAndPath(
        Conjoon_Modules_Groupware_Email_Account_Dto $accountDto,
        Array $pathParts)
    {

        /**
         * @see Conjoon_Modules_Groupware_Email_ImapHelper
         */
        require_once 'Conjoon/Modules/Groupware/Email/ImapHelper.php';

        $delim = Conjoon_Modules_Groupware_Email_ImapHelper
        ::getFolderDelimiterForImapAccount($accountDto);

        return implode($delim, $pathParts);
    }

    /**
     * Creates a new folder/mailbox for the specified account at the specified
     * path.
     *
     * @param string $name
     * @param string Array $pathParts the path parts which have to be assembled
     * again using the remote storage's delimiter
     * @param Conjoon_Modules_Groupware_Email_Account_Dto $account
     *
     * @return mixed The path to the newly created folder, or false
     */
    public function addImapFolderToPath(
        $name, Array $pathParts,
        Conjoon_Modules_Groupware_Email_Account_Dto $account
    )
    {
        $data = array('pathParts' => $pathParts);

        Conjoon_Argument_Check::check(array(
            'pathParts' => array(
                'type'       => 'array',
                'allowEmpty' => false
            )
        ), $data);

        $pathParts = $data['pathParts'];

        $name = $this->_checkParam($name, 'name');

        $this->_checkParam($account, 'checkForImap');

        $path = $this->getAssembledGlobalNameForAccountAndPath(
            $account, $pathParts
        );

        /**
         * @see Conjoon_Modules_Groupware_Email_ImapHelper
         */
        require_once 'Conjoon/Modules/Groupware/Email/ImapHelper.php';

        $delim = Conjoon_Modules_Groupware_Email_ImapHelper
                 ::getFolderDelimiterForImapAccount(
            $account
        );

        if (strpos($name, $delim) !== false) {
            throw new InvalidArgumentException(
                "Sorry, it seems that \"$delim\" is reserved and may not "
                ."be used within the name"
            );
        }

        if ($path === null) {
            return false;
        }

        $protocol = Conjoon_Modules_Groupware_Email_ImapHelper
                    ::reuseImapProtocolForAccount($account);

        $newFolder = $path . $delim .$name;

        $result = $protocol->create($newFolder);

        if (empty($result)) {
            return false;
        }

        return $newFolder;
    }

    /**
     * Moves the folder found under the specified path as a new child to
     * the new path for the specified user.
     * This method does autmatically determine whether the path belongs to
     * an IMAP or POP account.
     *
     * @param string Array $pathParts the path parts which have to be assembled
     * again using the remote storage's delimiter
     * @param string Array $parentPathParts the path parts which have to be
     * assembled again using the remote storage's delimiter
     * @param integer $userId
     *
     * @return Conjoon_Modules_Groupware_Email_Folder_Dto
     *
     * @throws InvalidArgumentException
     */
    public function moveFolderFromPathToPathForUserId(
        Array $pathParts, Array $parentPathParts, $userId)
    {
        $userId = $this->_checkParam($userId, 'userId');

        Conjoon_Argument_Check::check(array(
            'path' => array(
                'type'       => 'array',
                'allowEmpty' => false
            ),
            'nodeId' => array(
                'type'       => 'string',
                'allowEmpty' => false
            ),
            'rootId' => array(
                'type'       => 'string',
                'allowEmpty' => false
            )
        ), $parentPathParts);

        Conjoon_Argument_Check::check(array(
            'path' => array(
                'type'       => 'array',
                'allowEmpty' => false
            ),
            'nodeId' => array(
                'type'       => 'string',
                'allowEmpty' => false
            ),
            'rootId' => array(
                'type'       => 'string',
                'allowEmpty' => false
            )
        ), $pathParts);


        if (!$this->isRemoteFolder($pathParts['rootId'])) {
            // pop account mapped to folder
            $result = $this->_getFolderModel()->moveFolder(
                $pathParts['nodeId'], $parentPathParts['nodeId']
            );

            if ($result == 0) {
                return false;
            }

            $folderDto = $this->getLocalFolderForIdAndUserId($pathParts['nodeId'], $userId);

            if ($folderDto == null) {
                return false;
            }

            return $folderDto;

        } else {

            $account = $this->getImapAccountForFolderIdAndUserId(
                $pathParts['rootId'], $userId
            );

            if ($account === false) {
                return false;
            }

            $result = $this->moveImapFolderFromPathToPath(
                $pathParts['path'], $parentPathParts['path'],
                $account, $userId
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
                return $this->_transformImapFolder(
                    $iFold, $account, $protocol, false
                );
            }
        }

        return false;
    }

    /**
     * Moves the IMAP folder specified in $path as a new child to the
     * path found in $parentPath.
     *
     * @param array $pathParts The path parts representing the source
     * global name, they need to be assembled using the storage's delimiter
     * @param array $parentPathParts The path parts representing the target
     * global name, they need to be assembled using the storage's delimiter

     * @praram Conjoon_Modules_Groupware_Email_Account_Dto $account
     * @param integer $userId
     *
     * @return mixed the new path for the moved folder, or false on failure
     */
    public function moveImapFolderFromPathToPath(
        Array $pathParts, Array $parentPathParts,
        Conjoon_Modules_Groupware_Email_Account_Dto $account, $userId
    )
    {
        $data = array(
            'pathParts'       => $pathParts,
            'parentPathParts' => $parentPathParts
        );

        Conjoon_Argument_Check::check(array(
            'pathParts' => array(
                'type'       => 'array',
                'allowEmpty' => false
            ),
            'parentPathParts' => array(
                'type'       => 'array',
                'allowEmpty' => false
            )
        ), $data);

        $pathParts       = $data['pathParts'];
        $parentPathParts = $data['parentPathParts'];

        $userId = $this->_checkParam($userId, 'userId');

        $this->_checkParam($account, 'checkForImap');

        /**
         * @see Conjoon_Modules_Groupware_Email_ImapHelper
         */
        require_once 'Conjoon/Modules/Groupware/Email/ImapHelper.php';

        $delim = Conjoon_Modules_Groupware_Email_ImapHelper
                 ::getFolderDelimiterForImapAccount(
            $account
        );

        $path = $this->getAssembledGlobalNameForAccountAndPath(
            $account, $pathParts);
        $parentPath = $this->getAssembledGlobalNameForAccountAndPath(
            $account, $parentPathParts
        );

        if ($path === null || $parentPath === null) {
            return false;
        }

        $name  = $pathParts[count($pathParts) - 1];

        $newPath = $parentPath ? $parentPath . $delim . $name : $name;

        $protocol = Conjoon_Modules_Groupware_Email_ImapHelper
                    ::reuseImapProtocolForAccount(
                        $account
                    );

        if ($protocol->rename($path, $newPath) !== true) {
            return false;
        }

        return $newPath;
    }

    /**
     * Renames the folder found under the specified path.
     * This method does automatically determine whether the related account
     * is an IMAP account and invokes server communications accordingly.
     *
     * @param string $name The new name for the folder
     * @param array $pathParts paths parts as parsed by
     * Conjoon_Text_Parser_Mail_MailboxFolderPathJsonParser
     * @param integer $userId The id of the user the renamed folder should
     * belong to
     *
     * @return mixed A Conjoon_Modules_Groupware_Email_Account_Dto with the
     * data for the renamed folder, or false if the operation did not succeed
     *
     * @throws Exception
     */
    public function renameFolderForPathAndUserId(
        $name, Array $pathParts, $userId)
    {
        Conjoon_Argument_Check::check(array(
            'path' => array(
                'type'       => 'array',
                'allowEmpty' => false
            ),
            'nodeId' => array(
                'type'       => 'string',
                'allowEmpty' => false
            ),
            'rootId' => array(
                'type'       => 'string',
                'allowEmpty' => false
            )
        ), $pathParts);


        $name   = $this->_checkParam($name,   'name');
        $userId = $this->_checkParam($userId, 'userId');


        if (!$this->isRemoteFolder($pathParts['rootId'])) {

            // pop account mapped to folder
            $result = $this->_getFolderModel()->renameFolder(
                $pathParts['nodeId'], $name
            );

            if ($result == 0) {
                return false;
            }

            $folderDto = $this->getLocalFolderForIdAndUserId(
                $pathParts['nodeId'], $userId
            );

            if ($folderDto == null) {
                return false;
            }

            return $folderDto;

        } else {
            $account = $this->getImapAccountForFolderIdAndUserId(
                $pathParts['rootId'], $userId);

            if ($account === false) {
                return false;
            }

            $result = $this->renameImapFolderForPath(
                $name, $pathParts['path'], $account, $userId
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

            $protocol = Conjoon_Modules_Groupware_Email_ImapHelper
                        ::reuseImapProtocolForAccount(
                            $account
                        );

            $imap = new Zend_Mail_Storage_Imap($protocol);
            $iFolders = $imap->getFolders($result);

            foreach  ($iFolders as $localName => $iFold) {
                return $this->_transformImapFolder(
                    $iFold, $account, $protocol, false
                );
            }
        }

        return false;
    }

    /**
     * Checks whether one or more POP accounts are mapped to this folder.
     *
     * Note: This might not return the expected results if one or more accounts
     * related to the folder are flagged as deleted. See isRemoteFolder instead.
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
        $folderId = $this->_checkParam($folderId, 'folderId');
        $userId   = $this->_checkParam($userId,   'userId');

        $accounts = $this->getAccountsForFolderIdAndUserId($folderId, $userId);

        if (empty($accounts)) {
            return false;
        }

        $account =& $accounts[0];

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
        $folderId = $this->_checkParam($folderId, 'folderId');
        $userId   = $this->_checkParam($userId,   'userId');

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
        $userId   = $this->_checkParam($userId,   'userId');
        $folderId = $this->_checkParam($folderId, 'folderId');

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
     * Returns the root folder for the accoutn and user id.
     *
     * @param $accountId
     * @param $userId
     *
     * @return Conjoon_Modules_Groupware_Email_Folder_Dto
     *
     * @throws InvalidArgumentException|RuntimeException
     */
    public function getRootFolderForAccountId(
        Conjoon_Modules_Groupware_Email_Account_Dto $accountId, $userId)
    {
        return $this->getFoldersForPathAndUserId(
            array('path' => array(), 'rootId' => "", "nodeId" => ""),
            $userId, $accountId->id
        );
    }

    /**
     * Returns the folder for the user based upon the passed parameter.
     * This method does automatically determine if the connected account
     * is an IMAP account, and if thatis the case, queries the specified
     * IMAP server for the folders.
     *
     * @param array $pathParts paths parts as parsed by
     * Conjoon_Text_Parser_Mail_MailboxFolderPathJsonParser
     * @param integer $userId The id of the user
     * @param null|integer $accountId Optional, if specified only the folders
     *                     for the specified account id will be read out
     *
     * @return Conjoon_Modules_Groupware_Email_Folder_Dto
     *
     * @throws Conjoon_ArgumentException
     */
    public function getFoldersForPathAndUserId(Array $pathParts, $userId, $accountId = -1)
    {
        Conjoon_Argument_Check::check(array(
            'path' => array(
                'type'       => 'array',
                'allowEmpty' => false
            ),
            'nodeId' => array(
                'type'       => 'string',
                'allowEmpty' => true
            ),
            'rootId' => array(
                'type'       => 'string',
                'allowEmpty' => true
            )
        ), $pathParts);

        $userId = $this->_checkParam($userId, 'userId');
        $path   = $pathParts['path'];

        if (empty($pathParts['rootId']) && $accountId < 1) {
            // load root folders
            return $this->_getFolderDecorator()->getFoldersAsDto(0, $userId);
        } else if (empty($pathParts['rootId'])) {
            // get root folder for accot id
            $folderIds = $this->_getFoldersAccountsModel()->getFolderIdsForAccountId($accountId);

            if (empty($folderIds)) {
                throw new RuntimeException(
                    "No fodlers for account found"
                );
            }

            if (count($folderIds) > 1) {
                throw new RuntimeException(
                    "Unexpected multiple folder ids for account returned"
                );
            }

            if (!$this->isRemoteFolder($folderIds[0])) {
                throw new RuntimeException(
                    "Anything but remote folders not supported"
                );
            }

            return array($this->_getFolderDecorator()->getFolderForIdAsDto(
                $folderIds[0], $userId
            ));
        }

        if (!$this->isRemoteFolder($pathParts['rootId'])) {
            return $this->_getFolderDecorator()->getFoldersAsDto(
                (empty($path) ? $pathParts['rootId'] : $pathParts['nodeId']),
                $userId
            );
        }

        $account = $this->getImapAccountForFolderIdAndUserId(
            $pathParts['rootId'], $userId
        );

        if ($account) {
            // we have an IMAP account. Query IMAP server for folders
            return $this->getImapFoldersForPath($account, $path);
        }

        return array();
    }

    /**
     * Returns an array of Conjoon_Groupware_Email_Account_Dto which are mapped
     * to the specified folder id.
     *
     * NOTE:
     * This will not return accounts which are flagged as deleted.
     *
     * @param integer $folderId The id of the folder for which all accounts
     * should be queried.
     * @param integer $userId The id of the user this folder should belong.
     *
     * @return Array
     */
    public function getAccountsForFolderIdAndUserId($folderId, $userId)
    {
        $folderId = $this->_checkParam($folderId, 'folderId');
        $userId   = $this->_checkParam($userId,   'userId');

        $accountIds = $this->_getFoldersAccountsModel()
                           ->getAccountIdsMappedToFolderIds(array($folderId));

        $accountIds = $accountIds[$folderId];

        $len = count($accountIds);

        if ($len == 0) {
            return array();
        }

        $accounts = array();
        for ($i = 0; $i < $len; $i++) {

            $tmpAccount = $this->_getAccountDecorator()->getAccountAsDto(
                $accountIds[$i], $userId
            );
            if (!$tmpAccount) {
                continue;
            }
            $accounts[] = $tmpAccount;
        }

        return $accounts;
    }

    /**
     * Queries the IMAP server and renames the folder found in the deepest level
     * of the path.
     *
     * @param string $name
     * @param array $path The parts of the path which has to be assembled using
     * the delimiter of the target storage
     * @param Conjoon_Modules_Groupware_Email_Account_Dto $account
     * @param integer $userId
     *
     * @return the global name of the folder is renaming was successfull,
     * otherwise false
     *
     * @throws Exception
     */
    public function renameImapFolderForPath(
        $name, Array $pathParts,
        Conjoon_Modules_Groupware_Email_Account_Dto $account, $userId
    )
    {
        $data = array('pathParts' => $pathParts);

        Conjoon_Argument_Check::check(array(
            'pathParts' => array(
                'type'       => 'array',
                'allowEmpty' => false
            )
        ), $data);

        $pathParts = $data['pathParts'];

        $userId = $this->_checkParam($userId, 'userId');
        $name   = $this->_checkParam($name,   'name');


        $this->_checkParam($account, 'checkForImap');

        /**
         * @see Conjoon_Modules_Groupware_Email_ImapHelper
         */
        require_once 'Conjoon/Modules/Groupware/Email/ImapHelper.php';

        $delim = Conjoon_Modules_Groupware_Email_ImapHelper
                 ::getFolderDelimiterForImapAccount(
            $account
        );

        if (strpos($name, $delim) !== false) {
            throw new InvalidArgumentException(
                "Sorry, it seems that \"$delim\" is reserved and may not be "
                ."used within the name"
            );
        }


        $path = $this->getAssembledGlobalNameForAccountAndPath(
            $account, $pathParts
        );

        array_pop($pathParts);
        $newPath = implode($delim, $pathParts);

        if ($path === null || $newPath === null) {
            return false;
        }

        $newPath = $newPath ? $newPath . $delim . $name : $name;

        $protocol = Conjoon_Modules_Groupware_Email_ImapHelper
                    ::reuseImapProtocolForAccount(
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
     * @param array $pathParts The path parts of the folder to query. Must be
     * assembled using the delimiter of the remote storage
     *
     * @return Conjoon_Modules_Groupware_Email_Folder_Dto
     */
    public function getImapFoldersForPath(
        Conjoon_Modules_Groupware_Email_Account_Dto $account, Array $pathParts
    )
    {
        $this->_checkParam($account, 'checkForImap');

        /**
         * @see Conjoon_Modules_Groupware_Email_ImapHelper
         */
        require_once 'Conjoon/Modules/Groupware/Email/ImapHelper.php';

        $delim = Conjoon_Modules_Groupware_Email_ImapHelper::getFolderDelimiterForImapAccount(
            $account
        );

        if (empty($pathParts)) {
            $path = null;
        } else {
            $path = implode($delim, $pathParts);
        }

        $protocol = Conjoon_Modules_Groupware_Email_ImapHelper
                    ::reuseImapProtocolForAccount(
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
            /**
             * @ticket CN-595
             */
            if ($path === "INBOX") {
                $path = null;
            }
            $iFolders = $imap->getFolders($path)->getChildren();
        }

        $folders = array();

        /**
         * @see Conjoon_Modules_Groupware_Email_ImapHelper
         */
        require_once 'Conjoon/Modules/Groupware/Email/ImapHelper.php';

        $em = Zend_Registry::get(Conjoon_Keys::DOCTRINE_ENTITY_MANAGER);

        $rep = $em->getRepository('\Conjoon\Data\Entity\Mail\DefaultMailAccountEntity');

        $accountEntity = $rep->findById($account->id);
        $map = array();

        if ($accountEntity) {
            $mappings = $accountEntity->getFolderMappings();

            for ($i = 0, $len = count($mappings); $i < $len; $i++) {
                $map[$mappings[$i]->getGlobalName()] =
                    strtolower($mappings[$i]->getType());
            }
        }

        foreach  ($iFolders as $localName => $iFold) {
            $gb = $iFold->getGlobalName();
            $folders[] = $this->_transformImapFolder(
                $iFold, $account, $protocol, $isRootLevel,
                (isset($map[$gb])
                 ? $map[$gb] : 'folder')
            );
        }

        return $folders;
    }


// -------- api

    /**
     * Function for often needed param checks.
     *
     * @throws InvalidArgumentException
     *
     * @param mixed $value
     * @param string $type
     *
     * @return mixed
     */
    private function _checkParam($value, $type)
    {
        switch ($type) {
            case 'userId':
            case 'accountId':
            case 'folderId':
                $value = (int)$value;
                if ($value <= 0) {
                   throw new InvalidArgumentException(
                        "Invalid argument supplied, $type was \"$value\""
                    );
                }
            break;


            case 'path':
            case 'parentPath':
            case 'name':
                $value = trim((string)$value);
                if ($value == "") {
                   throw new InvalidArgumentException(
                        "Invalid argument supplied, $type was \"$value\""
                    );
                }
            break;

            case 'pathInfo':
            case 'parentPathInfo':
                if (empty($value)) {
                   throw new InvalidArgumentException(
                       "Could not operate on parentPath - $type was \"$value\""
                    );
                }
            break;

            case 'checkForImap':
                if ($value->protocol !== 'IMAP') {
                    throw new InvalidArgumentException(
                        "Invalid argument supplied, account does use the "
                        ."\"".$value->protocol."\"protocol, but not the "
                        ."IMAP protocol"
                    );
                }
            break;

            default:
                throw new InvalidArgumentException(
                    "No rule defined for $type"
                );
            break;

        }

        return $value;
    }

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
        $isRootLevel = false,
        $type = 'folder'
    )
    {
        $delim = Conjoon_Modules_Groupware_Email_ImapHelper::
                 getFolderDelimiterForImapAccount($account);

        $globalName = $folder->getGlobalName();
        $path = explode($delim, $globalName);
        $path = $path[count($path)-1];

        $pendingCount = 0;


        if (!$this->itemListRequestFacade) {
            $this->itemListRequestFacade = Conjoon_Modules_Groupware_Email_Item_ItemListRequestFacade::getInstance();
        }

        if ($folder->isSelectable()) {
            try{
                $pendingCount = $this->itemListRequestFacade
                                     ->getPendingCountForGlobalName(
                                         $account, $globalName
                                    );
            } catch (Exception $e) {
                // ignore
            }
        }

        return Conjoon_Modules_Groupware_Email_ImapHelper::transformToFolderDto(
            $folder, $isRootLevel, array(
            'id'           => $account->id.'_'.$globalName,
            'idForPath'    => $path,
            'pendingCount' => $pendingCount,
            'type'         => $type
        ));
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


    /**
     * @return Conjoon_Modules_Groupware_Email_Folder_FolderRootTypeBuilder
     */
    private function _getFolderRootTypeBuilder()
    {
        if (!$this->_folderRootTypeBuilder) {
            /**
             * @see Conjoon_Builder_Factory
             */
            require_once 'Conjoon/Builder/Factory.php';

            /**
             * @see Conjoon_Keys
             */
            require_once 'Conjoon/Keys.php';

            $this->_folderRootTypeBuilder = Conjoon_Builder_Factory::getBuilder(
                Conjoon_Keys::CACHE_EMAIL_FOLDERS_ROOT_TYPE,
                Zend_Registry::get(Conjoon_Keys::REGISTRY_CONFIG_OBJECT)->toArray(),
                $this->_getFolderModel()
            );
        }

        return $this->_folderRootTypeBuilder;
    }

}