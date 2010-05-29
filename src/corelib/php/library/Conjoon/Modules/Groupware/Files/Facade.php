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
class Conjoon_Modules_Groupware_Files_Facade {

    /**
     * @var Conjoon_Modules_Groupware_Files_Facade $_instance
     */
    private static $_instance = null;

    /**
     * @var Conjoon_Modules_Groupware_Files_Folder_Model_Folder
     */
    private $_folderModel = null;

    /**
     * @var Conjoon_Modules_Groupware_Files_File_Model_File
     */
    private $_fileModel = null;

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
     * Generates a unique id for a file. This id is a string with a length
     * of 32 chars, alphanumeric.
     *
     * @param integer $id The user id for which the unique id should be generated.
     *
     * @return string
     */
    public function generateFileKey($userId)
    {
        return md5(uniqid(mt_rand(), true));
    }

    /**
     * Returns download data for the required file, i.e. mime type,
     * name and content. The data will be returned in an associative array
     * and is already prepared for sending to the client, i.e. the content to
     * send has already been decoded if necessary.
     *
     * @param mixed   $key
     * @param integer $id
     * @param inter   $userId
     *
     * @return array an assoc array with the keys "name", "mimeType" and
     * "content", or null if item is not available.
     *
     * @throws InvalidArgumentException
     */
    public function getFileDownloadDataForUserId($key, $id, $userId)
    {
        $key    = trim((string)$key);
        $id     = (int)$id;
        $userId = (int)$userId;

        if ($key == "") {
            throw new InvalidArgumentException(
                "Invalid argument supplied for key - $key"
            );
        }

        if ($id <= 0) {
            throw new InvalidArgumentException(
                "Invalid argument supplied for id - $id"
            );
        }
        if ($userId <= 0) {
            throw new InvalidArgumentException(
                "Invalid argument supplied for userId - $userId"
            );
        }

        if (!$this->isFileDownloadableForUserId(
            $key, $id, $userId)) {
            return null;
        }

        $fileModel = $this->_getFileModel();

        $data = $fileModel->getFileForKeyAndId($key, $id);

        if (empty($data)) {
            return null;
        }

        return array(
            'name'     => $data['name'],
            'content'  => $data['content'],
            'mimeType' => $data['mime_type']
                          ? $data['mime_type'] : 'text/plain'
        );
    }

    /**
     * Returns true if the user may download the file with the specified
     * id, otherwise false.
     *
     * @param string  $key The key of the file to download
     * @param integer $id The id of the file to download
     * @param integer $userId The id of the user who requests the file
     * being downloaded
     *
     * @return boolean true if he may download the file, otherwise false
     *
     * @throws InvalidArgumentException
     */
    public function isFileDownloadableForUserId($key, $id, $userId)
    {
        $key    = trim((string)$key);
        $id     = (int)$id;
        $userId = (int)$userId;

        if ($key == "") {
            throw new InvalidArgumentException(
                "Invalid argument supplied for key - $key"
            );
        }

        if ($id <= 0) {
            throw new InvalidArgumentException(
                "Invalid argument supplied for id - $id"
            );
        }
        if ($userId <= 0) {
            throw new InvalidArgumentException(
                "Invalid argument supplied for userId - $userId"
            );
        }

        $model = $this->_getFileModel();

        $allowed = $model->isFileInFolderForUser($id, $userId);

        if (!$allowed) {
            return false;
        }

        return true;
    }

    /**
     * Saves the file into the temp folder for the specified user and returns
     * a Dto with further information about the file.
     *
     * @param string $name
     * @param resource|string $content
     * @param string $type
     *
     * @return mixed null if an error occured, otherwise an instance of
     * Conjoon_Groupware_Files_File_Dto
     *
     * @throws InvalidArgumentException
     */
    public function addFileDataToTempFolderForUser($name, $content, $type, $userId)
    {
        $name   = trim((string)$name);
        $type   = trim((string)$type);
        $userId = (int)$userId;

        if ($name == "") {
            throw new InvalidArgumentException(
                "Invalid argument supplied for name - $name"
            );
        }

        if ($type == "") {
            throw new InvalidArgumentException(
                "Invalid argument supplied for type - $type"
            );
        }

        if ($userId <= 0) {
            throw new InvalidArgumentException(
                "Invalid argument supplied for userId - $userId"
            );
        }

        // first of, retrieve the id for the tmp folder of the specified user
        // in the db
        $tmpFolderId = $this->createTempFolderForUser($userId);

        if ($tmpFolderId === 0) {
            return null;
        }

        $key = $this->generateFileKey($userId);
        $id  = $this->_getFileModel()->addFileToFolder(
            $tmpFolderId, $name, $content, $type, $key
        );

        if ($id === 0) {
            return null;
        }

        /**
         * @see Conjoon_Modules_Groupware_Files_File
         */
        require_once 'Conjoon/Modules/Groupware/Files/File.php';

        $file = new Conjoon_Modules_Groupware_Files_File();
        $file->setName($name);
        $file->setKey($key);
        $file->setMimeType($type);
        $file->setId($id);
        $file->setMetaType('file');
        $file->setGroupwareFilesFoldersId($tmpFolderId);

        return $file->getDto();

    }

    /**
     * Returns the id of the temp folder for the specified userId
     *
     * @param int $userId
     *
     * @return int
     *
     * @throws InvalidArgumentException
     */
    public function getIdForTempFolderForUser($userId)
    {
        $userId = (int)$userId;

        if ($userId <= 0) {
            throw new InvalidArgumentException(
                "Invalid argument supplied for userId - $userId"
            );
        }

        return $this->_getFolderModel()->getTempFolderIdForUser($userId);
    }

    /**
     * Creates the temp folder if it does not already exists and returns the id
     * of the temp folder.
     *
     * @param int $userId
     *
     * @return int
     *
     * @throws InvalidArgumentException
     */
    public function createTempFolderForUser($userId)
    {
        $userId = (int)$userId;

        if ($userId <= 0) {
            throw new InvalidArgumentException(
                "Invalid argument supplied for userId - $userId"
            );
        }

        $id = $this->_getFolderModel()->getTempFolderIdForUser($userId);

        if ($id !== 0) {
            return $id;
        }

        return $this->_getFolderModel()->createTempFolderForUser($userId);
    }

// -------- api

    /**
     *
     * @return Conjoon_Modules_Groupware_Files_Folder_Model_Folder
     */
    private function _getFolderModel()
    {
        if (!$this->_folderModel) {
             /**
             * @see Conjoon_Modules_Groupware_Files_Folder_Model_Folder
             */
            require_once 'Conjoon/Modules/Groupware/Files/Folder/Model/Folder.php';

            $this->_folderModel = new Conjoon_Modules_Groupware_Files_Folder_Model_Folder();
        }

        return $this->_folderModel;
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