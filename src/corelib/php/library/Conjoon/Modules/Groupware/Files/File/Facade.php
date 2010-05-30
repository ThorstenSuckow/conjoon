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
class Conjoon_Modules_Groupware_Files_File_Facade {

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
        $this->_checkArguments(array(
            'path' => array('value' => &$path, 'type' => 'string')
        ));

        return $this->_createFileLob($folderId, $name, $path, $type, $userId, true);
    }

    /**
     * Creates a file in the specified folder for the user.
     *
     * @param integer $folderId
     * @param string $name
     * @param string|resource $content
     * @param string $type
     * @param integer $userId
     *
     * @return Conjoon_Modules_Groupware_Files_File if the file was successfully
     * created, otherwise null
     *
     * @throws InvalidArgumentException
     */
    public function createFileInFolderForUser($folderId, $name, $content, $type, $userId)
    {
        return $this->_createFileLob($folderId, $name, $content, $type, $userId);
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
        $this->_checkArguments(array(
            'id'     => array('value' => &$id, 'type' => 'int'),
            'key'    => array('value' => &$key, 'type' => 'string'),
            'userId' => array('value' => &$userId,   'type' => 'int')
        ));

        if (!$this->isFileDownloadableForUserId(
            $id, $key, $userId)) {
            return null;
        }

        $fileModel = $this->_getFileModel();

        $data = $fileModel->getFileDataForKeyAndId($key, $id);

        if (!$data || empty($data)) {
            return null;
        }

        if ($data['storage_container']) {
            $storageBasePath = $this->_getFileStorageBasePath();
            if ($storageBasePath === null) {
                return array(
                    'name'     => $data['name'],
                    'content'  => "",
                    'mimeType' => $data['mime_type']
                                  ? $data['mime_type'] : 'text/plain'
                );
            }

            $assumedDir = $this->_getBasePathForUserId($storageBasePath, $userId)
                          .'/' . $data['storage_container'];

            $file = $assumedDir . '/' . $this->_generateFileName($id, $key);

            if (!@is_dir($assumedDir) || !@file_exists($file)) {
                return array(
                    'name'     => $data['name'],
                    'content'  => "",
                    'mimeType' => $data['mime_type']
                                  ? $data['mime_type'] : 'text/plain'
                );
            }

            $fc = @file_get_contents($file);

            return array(
                'name'     => $data['name'],
                'content'  => ($fc !== false ? $fc : ""),
                'mimeType' => $data['mime_type']
                              ? $data['mime_type'] : 'text/plain'
            );
        }

        $data = $fileModel->getFileForKeyAndId($id, $key);

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
     * @param integer $id The id of the file to download
     * @param string  $key The key of the file to download
     * @param integer $userId The id of the user who requests the file
     * being downloaded
     *
     * @return boolean true if he may download the file, otherwise false
     *
     * @throws InvalidArgumentException
     */
    public function isFileDownloadableForUserId($id, $key, $userId)
    {
        $this->_checkArguments(array(
            'id'     => array('value' => &$id, 'type' => 'int'),
            'key'    => array('value' => &$key, 'type' => 'string'),
            'userId' => array('value' => &$userId,   'type' => 'int')
        ));


        $model = $this->_getFileModel();

        $allowed = $model->isFileInFolderForUser($id, $userId);

        if (!$allowed) {
            return false;
        }

        return true;
    }

// -------- api

    /**
     * Helper for creating a file in the database with its contents in
     * either the database or the local file storage.
     *
     * @param integer $folderId
     * @param string $name
     * @param string|resource $content
     * @param string $type
     * @param integer $userId
     * @param bool $isPath Whether $content is to be treated as a file path
     *
     * @return Conjoon_Modules_Groupware_Files_File
     */
    protected function _createFileLob($folderId, $name, $content, $type, $userId,
        $isPath = false)
    {
        $this->_checkArguments(array(
            'folderId' => array('value' => &$folderId, 'type' => 'int'),
            'name'     => array('value' => &$name,     'type' => 'string'),
            'type'     => array('value' => &$type,     'type' => 'string'),
            'userId'   => array('value' => &$userId,   'type' => 'int')
        ));

        $useFileSystem       = $this->_isFileSystemUsedForStoring();
        $fileStorageBasePath = $this->_getFileStorageBasePath();
        $key                 = $this->_generateFileKey($userId);
        $path                = null;

        // try to create the folders first if filestorage is used,
        // so we still have the db as a fallback
        if ($fileStorageBasePath !== null && $useFileSystem === true) {
            $storageIndicator = $this->_generateStorageIndicator();
            $path = $this->_createFolder(
                $fileStorageBasePath, $userId, $storageIndicator
            );
        }

        // first off, create the needed data in the database
        if ($path === null) {
            if (!is_resource($content) && $isPath === true) {
                $fp = @fopen($content, 'rb');
                if (!$fp) {
                    return null;
                }
            }

            $id = $this->_getFileModel()->addFileToFolder(
                $folderId, $name, $fp, $type, $key
            );

            fclose($fp);

            if ($id <= 0) {
                return null;
            }

            return $this->_createFileObject($id, $key, $name, $type,
                $folderId);
        }

        // create needed data for storing file in file system
        $id = (int)$this->_getFileModel()->addFileToFolder(
            $folderId, $name, "", $type, $key, $storageIndicator
        );

        if ($id <= 0) {
            return null;
        }

        $fileName = $path . '/' . $this->_generateFileName($id, $key);

        // does the file already exist?
        if (@file_exists($fileName)) {
            $this->_getFileModel()->removeFile($id);
            return null;
        }

        // and finally, add the file contents to the file system
        if (is_resource($content)) {
            $fp = @fopen($fileName, 'wb');
            if (!$fp) {
                $this->_getFileModel()->removeFile($id);
                return null;
            }

            while (!feof($content)) {
                fwrite($fp, fread($content, 1024));
            }
            fclose($fp);
        } else if ($isPath) {

            $succ = @rename($content, $fileName);
            if (!$succ) {
                $this->_getFileModel()->removeFile($id);
                return null;
            }

        } else {
            $succ = file_put_contents($fileName, $content);
            if ($succ === false) {
                $this->_getFileModel()->removeFile($id);
                return null;
            }
        }

        return $this->_createFileObject($id, $key, $name, $type, $folderId);
    }

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
     * Returns true if the filesystem should be used for storing file-data,
     * false if the DB shoudl be used. Note, thet this only tells if the
     * file contents are being stored in the filesystem. If you need to
     * determine whether file-contents need to be returned from the filesystem,
     * an appropriate indicator should have been stored along with the data
     * in the file table.
     *
     * @return bool
     */
    protected function _isFileSystemUsedForStoring()
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
     * Returns the base path to the folder where files should be stored under.
     * Returns null if the path is not available, i.e. if file storage is not
     * used or the directory not exists.
     *
     * @return string
     */
    protected function _getFileStorageBasePath()
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
     * Generates a unique id for a file. This id is a string with a length
     * of 32 chars, alphanumeric.
     *
     * @param integer $id The user id for which the unique id should be generated.
     *
     * @return string
     */
    protected function _generateFileKey($userId)
    {
        return md5(uniqid(mt_rand(), true));
    }

    /**
     * Generates a storage indicator to be used as a folder in the native
     * file-system.
     *
     * @return string
     */
    protected function _generateStorageIndicator()
    {
        return date("Y-W", time());
    }

    /**
     * Generates a filename.
     *
     * @param integer $id
     * @param string $key
     *
     * @return string
     */
    protected function _generateFileName($id, $key)
    {
        return $id . '-' . $key;
    }

    /**
     * Helper function for creating a base path for a user id.
     *
     * @param string $basePath
     * @param int $userId
     *
     * @return string
     */
    protected function _getBasePathForUserId($basePath, $userId)
    {
        return str_replace("\\", "/", rtrim($basePath, "\\/")) . '/' . $userId;
    }

    /**
     * Helper function for creating a folder. $basePath must already exist.
     *
     * @param string $basePath
     * @param int    $userId
     * @param string $storageindicator
     *
     * @return string|null the full path name if creating the dir was successfull
     * (or the file already existed), otherwise null
     */
    protected function _createFolder($basePath, $userId, $storageIndicator)
    {
        if (!@is_dir($basePath)) {
            return null;
        }

        $finalPath = $this->_getBasePathForUserId($basePath, $userId)
                     . '/'
                     . $storageIndicator;

        if (@is_dir($final_path)) {
            return $finalPath;
        }

        $t1 = $basePath . '/' . $userId;
        if (!@is_dir($t1)) {
            $succ = @mkdir($t1);
            if (!$succ) {
                return null;
            }
        }

        $t1 .= '/' . $storageIndicator;
        if (!@is_dir($t1)) {
            $succ = @mkdir($t1);
            if (!$succ) {
                return null;
            }
        }

        return $finalPath;
    }

    /**
     *
     *
     */
    protected function _checkArguments(Array $data)
    {
        foreach ($data as $argumentName => $config) {
            switch ($config['type']) {
                case 'string':
                    $config['value'] = trim((string)$config['value']);
                    if ($config['value'] == "") {
                        throw new InvalidArgumentException(
                            "Invalid argument supplied for $argumentName - "
                            .$config['value']
                        );
                    }
                break;

                case 'int':
                    $config['value'] = (int)$config['value'];
                    if ($config['value'] <= 0) {
                        throw new InvalidArgumentException(
                            "Invalid argument supplied for $argumentName - "
                            .$config['value']
                        );
                    }
                break;
            }
        }
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