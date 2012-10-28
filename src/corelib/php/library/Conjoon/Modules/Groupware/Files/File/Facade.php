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
 * @see Conjoon_Db_LobAccess_StorageMediator
 */
require_once 'Conjoon/Db/LobAccess/StorageMediator.php';

/**
 * @see Conjoon_File_LobAccess
 */
require_once 'Conjoon/File/LobAccess.php';

/**
 * @see Conjoon_Argument_Check
 */
require_once 'Conjoon/Argument/Check.php';

/**
 *
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Conjoon_Modules_Groupware_Files_File_Facade extends Conjoon_Db_LobAccess_StorageMediator {

    /**
     * @var Conjoon_Modules_Groupware_Files_File_Facade $_instance
     */
    private static $_instance = null;

    /**
     * @var Conjoon_Modules_Groupware_Files_File_Model_File
     */
    private $_fileModel = null;

    private function __construct()
    {
        $this->setAccessors(
            new Conjoon_File_LobAccess(),
            $this->_getFileModel()
        );
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
     * Tries to copy the contents of the specified file found under $path
     * to the specified folder id.
     *
     * @param string $path
     * @param int $folderId
     * @param int $userId
     * @param string $name
     * @param string $type
     *
     * @return Conjoon_Modules_Groupware_Files_File if the file was successfully
     * created, otherwise null
     *
     * @throws InvalidArgumentException
     */
    public function moveFileToFolderForUserId($path, $folderId, $userId, $name, $type)
    {
        $data = array(
            'groupwareFilesFoldersId' => $folderId,
            'resource'                => $path,
            'userId'                  => $userId,
            'name'                    => $name,
            'mimeType'                => $type
        );

        Conjoon_Argument_Check::check(array(
            'groupwareFilesFoldersId' => array('type' => 'int'),
            'userId' => array('type' => 'int'),
            'name' => array('type' => 'string'),
            'mimeType' => array('type' => 'string'),
        ), $data);

        $lob = $this->_createLob($data, true, true);

        $obj = $this->_createFileObject(
            $lob['dbResult'],
            $lob['key'],
            $lob['name'],
            $lob['mimeType'],
            $lob['groupwareFilesFoldersId']
        );

        return $obj;
    }

    /**
     * Returns download data for the required file, i.e. mime type,
     * name and content. The data will be returned in an associative array
     * and is already prepared for sending to the client, i.e. the content to
     * send has already been decoded if necessary.
     *
     * @param integer $id
     * @param mixed   $key
     * @param inter   $userId
     *
     * @return array an assoc array with the keys "name", "mimeType" and
     * "content", or null if item is not available.
     *
     * @throws InvalidArgumentException
     */
    public function getFileDownloadDataForUserId($id, $key, $userId)
    {
        $data = array(
            'id'              => $id,
            'key'             => $key,
            'includeResource' => true,
            'userId'          => $userId
        );

        Conjoon_Argument_Check::check(array(
            'userId' => array('type' => 'int')
        ), $data);


        if (!$this->isFileDownloadableForUserId($data['id'], $data['userId'])) {
            return null;
        }

        return parent::getLobContentWithData($data);
    }

    /**
     * Returns true if the user may download the file with the specified
     * id, otherwise false.
     *
     * @param integer $id The id of the file to download
     * @param integer $userId The id of the user who requests the file
     * being downloaded
     *
     * @return boolean true if he may download the file, otherwise false
     *
     * @throws InvalidArgumentException
     */
    public function isFileDownloadableForUserId($id, $userId)
    {
        $data = array(
            'id'     => $id,
            'userId' => $userId
        );

        Conjoon_Argument_Check::check(array(
            'id'     => array('type' => 'int'),
            'userId' => array('type' => 'int')
        ), $data);

        $model = $this->_getFileModel();

        $allowed = $model->isFileInFolderForUser($data['id'], $data['userId']);

        if (!$allowed) {
            return false;
        }

        return true;
    }

// -------- StorageMediator

    /**
     * Returns true if LOBs should be stored in the filesystem.
     *
     * @return bool
     */
    protected function _isFileSystemUsedForLobs()
    {
        /**
         * @see Zend_Registry
         */
        require_once 'Zend/Registry.php';

        /**
         * @see Conjoon_Keys
         */
        require_once 'Conjoon/Keys.php';

        return (bool)Zend_Registry::get(
            Conjoon_Keys::REGISTRY_CONFIG_OBJECT
        )->files->storage->use_filesystem;
    }

    /**
     * Returns the file storage base path
     *
     * @return string|null
     */
    protected function _getLobStorageBasePath()
    {
        /**
         * @see Zend_Registry
         */
        require_once 'Zend/Registry.php';

        /**
         * @see Conjoon_Keys
         */
        require_once 'Conjoon/Keys.php';

        $dir = trim((string)@Zend_Registry::get(
            Conjoon_Keys::REGISTRY_CONFIG_OBJECT
        )->files->storage->files->default->dir);

        if ($dir && @is_dir($dir)) {
            return $dir;
        }

        return null;
    }

    /**
     * Computes a file name based on the given data in $data
     *
     * @param array $data
     *
     * @return string
     */
    protected function _generateFileNameStringForLob(Array $data)
    {
        Conjoon_Argument_Check::check(array(
            'id'  =>  array('type' => 'int'),
            'key' => array('type' => 'string')
        ), $data);

        return $data['id'] . '-' . $data['key'];
    }

// -------- api

    /**
     * Helper function for creating a Conjoon_Modules_Groupware_Files_File
     *
     * @param int    $id
     * @param string $key
     * @param string $name
     * @param string $mimeType
     * @param int    $groupwareFilesFoldersId
     *
     * @return Conjoon_Modules_Groupware_Files_File
     */
    private function _createFileObject($id, $key, $name, $mimeType,
        $groupwareFilesFoldersId)
    {
        /**
         * @see Conjoon_Modules_Groupware_Files_File
         */
        require_once 'Conjoon/Modules/Groupware/Files/File.php';

        $file = new Conjoon_Modules_Groupware_Files_File();
        $file->setName($name);
        $file->setKey($key);
        $file->setMimeType($mimeType);
        $file->setId($id);
        $file->setMetaType('file');
        $file->setGroupwareFilesFoldersId($groupwareFilesFoldersId);

        return $file;
    }

    /**
     *
     * @return Conjoon_Modules_Groupware_Files_File_Model_File
     */
    private function _getFileModel()
    {
        if (!$this->_fileModel) {
             /**
             * @see Conjoon_Modules_Groupware_Files_File_Model_File
             */
            require_once 'Conjoon/Modules/Groupware/Files/File/Model/File.php';

            $this->_fileModel = new Conjoon_Modules_Groupware_Files_File_Model_File();
        }

        return $this->_fileModel;
    }

}