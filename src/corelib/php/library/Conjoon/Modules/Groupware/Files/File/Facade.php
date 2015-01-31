<?php
/**
 * conjoon
 * (c) 2007-2015 conjoon.org
 * licensing@conjoon.org
 *
 * conjoon
 * Copyright (C) 2014 Thorsten Suckow-Homberg/conjoon.org
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
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
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
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
     * Creates the file with the specified name, content and type for the specified user
     *in the given folder.
     *
     * @param $folderId
     * @param $name
     * @param $content
     * @param $mimeType
     * @param $userId
     *
     * @return Conjoon_Modules_Groupware_Files_File
     */
    public function createFileInFolderForUser($folderId, $name, $content, $mimeType, $userId) {

        $data = array(
            'groupwareFilesFoldersId' => $folderId,
            'userId'                  => $userId,
            'name'                    => $name,
            'mimeType'                => $mimeType
        );

        Conjoon_Argument_Check::check(array(
            'groupwareFilesFoldersId' => array('type' => 'int'),
            'userId' => array('type' => 'int'),
            'name' => array('type' => 'string'),
            'mimeType' => array('type' => 'string'),
        ), $data);

        $data['resource'] = $content;

        $lob = $this->_createLob($data, false, false);

        return $this->_createFileObject(
            $lob['dbResult'],
            $lob['key'],
            $lob['name'],
            $lob['mimeType'],
            $lob['groupwareFilesFoldersId']
        );
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
        )->files->storage->filesystem->enabled;
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
        )->files->storage->filesystem->dir);

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
