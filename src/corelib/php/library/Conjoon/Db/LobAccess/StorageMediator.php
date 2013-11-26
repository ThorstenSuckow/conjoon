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
 * @see Conjoon_Data_Exception
 */
require_once 'Conjoon/Data/Exception.php';

/**
 * @see Conjoon_Argument_Check
 */
require_once 'Conjoon/Argument/Check.php';

/**
 * @see Conjoon_Util_Array
 */
require_once 'Conjoon/Util/Array.php';

/**
/**
 * A storage mediator is responsible for determining whether to store
 * lobs in the database or in the file system. It takes care of processing
 * various operations related to lobs such as retrieving their content and
 * properly storing them, while maintaining relations between the db data
 * and the lob itself, no matter if it has been saved in the filesystem
 * or the db.
 * While configuring the system is up to the server administrator, a mediator
 * is able to provide fallbacks if the used instances of Conjoon_File_LobAccess
 * and Conjoon_Db_LobAccess have been implemented properly: As long as the
 * hint for the storage_container maintains valid, lob data can be retrieved from
 * the filesystem even if the application's configuration is set to storing the
 * lobs in the database (this goes for lobs that have been saved before
 * configuration changed).
 *
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
abstract class Conjoon_Db_LobAccess_StorageMediator {

    protected $_fileLobAccess = null;
    protected $_dbLobAccess   = null;


    /**
     * Returns true if LOBs should be stored in the filesystem.
     *
     * @return bool
     */
    abstract protected function _isFileSystemUsedForLobs();

    /**
     * Returns the file storage base path
     *
     * @return string|null
     */
    abstract protected function _getLobStorageBasePath();

    /**
     * Computes a file name based on the given data in $data
     *
     * @param array $data
     *
     * @return string
     */
    abstract protected function _generateFileNameStringForLob(Array $data);

    /**
     * Computes a unique key for teh LOB name based on the specified
     * data in $data
     *
     * @param array $data
     *
     * @return string
     */
    protected function _generateLobKey(Array $data)
    {
        return md5(uniqid(mt_rand(), true));
    }

    /**
     * Computes a storage container string which should be safe to use for
     * the hosts filesystem based on the specified data in $data
     *
     * @param array $data
     *
     * @return string
     */
    protected function _generateStorageContainerString(Array $data)
    {
        $time = time();
        $ft   = date("Y-W", $time);
        $st   = date("l", $time);
        return $ft ."/". $st
               ."/".$ft ."-" . $st ."-"
               . mt_rand(1, 1024);
    }

    /**
     * Returns the complete lob data for the specified id and key.
     * The properties key and id have to be set in the passed array.
     *
     * @param array $data
     *
     * @return array|null If a call to this method is successfull, the
     * returned property "resource" will be set with the lob's content
     *
     * @throws Conjoon_Data_Exception
     */
    public function getLobContentWithData(Array $data)
    {
        Conjoon_Argument_Check::check(array(
            'id' => array('type' => 'int'),
            'key' => array('type' => 'string')
        ), $data);

        $lobData = $this->_dbLobAccess->getLobData($data);

        if (!$lobData || empty($lobData)) {
            return null;
        }

        if (!array_key_exists('storage_container', $lobData)) {
            throw new Conjoon_Data_Exception(
                "Property \"storage_container\" not available in fetched lob data"
            );
        }

        if ($lobData['storage_container']) {
            $storageBasePath = $this->_getLobStorageBasePath();
            if ($storageBasePath === null) {
                return null;
            }

            $assumedDir = $this->_getLobStorageBasePath()
                          .'/' . $lobData['storage_container'];

            $file = $assumedDir . '/' . $this->_generateFileNameStringForLob($lobData);


            $fc = $this->_fileLobAccess->getLobContent(array('path' => $file));

            if ($fc === null) {
                return null;
            }

            Conjoon_Util_Array::apply($lobData, array(
                'resource' => $fc
            ));


            return $lobData;
        }

        return $lobData;
    }

    /**
     * Helper function for creating a folder.
     * $data must have lobStorageBasePath and storageContainer available.
     *
     * @param array $data
     *
     * @return string|null the full path name for a lob to be stored if creating
     * the dir was successfull (or the dir already existed), otherwise null
     *
     * @see _createFolderImpl
     */
    protected function _createFolder(Array $data)
    {
        if (!isset($data['lobStorageBasePath'])) {
            throw new Conjoon_Data_Exception(
                "\"lobStorageBasePath\" setting missing for _createFolder."
            );
        }

        if (!isset($data['storageContainer'])) {
            throw new Conjoon_Data_Exception(
                "\"storageContainer\" setting missing for _createFolder."
            );
        }

        $basePath = str_replace(
            "\\", "/", rtrim($data['lobStorageBasePath'], "\\/")
        );

        if (!@is_dir($basePath)) {
            return null;
        }

        $storageContainer = str_replace(
            "\\", "/", trim($data['storageContainer'], "\\/")
        );

        $finalPath = $basePath . '/' . $storageContainer;

        if (@file_exists($finalPath)) {
            if (!@is_dir($finalPath)) {
                return null;
            }
            return $finalPath;
        }

        $parts = explode("/", $storageContainer);

        $tDir = $basePath;
        while (count($parts)) {
            $dir = array_shift($parts);
            $tDir .= '/' . $dir;
            if (@is_dir($tDir)) {
                continue;
            }
            @mkdir($tDir);
        }

        if (!@file_exists($finalPath)) {
            return null;
        }

        return $finalPath;
    }

    /**
     * Sets the accessors for this class
     *
     * @param Conjoon_File_LobAccess $fileLobAccess
     * @param Conjoon_Data_LobAccess $dbLobAccess
     */
    public function setAccessors(Conjoon_File_LobAccess $fileLobAccess,
    Conjoon_Db_LobAccess $dbLobAccess)
    {
        if ($this->_fileLobAccess || $this->_dbLobAccess) {
            throw new Conjoon_Data_Exception(
                "setAccessors was already called"
            );
        }

        $this->_fileLobAccess = $fileLobAccess;
        $this->_dbLobAccess   = $dbLobAccess;
    }

    /**
     * Creates a lob.
     * Additional parameters will be added to the array during processing,
     * such as the generated key for the lob.
     *
     * @param array $data An associative array with at least the following
     * key/value pairs:
     * - resource The resource to fetch the content from. This can either be
     * a native resource or a string. If $isPath is set to true, the string
     * will be treatened as a path and the contents from the file found under
     * this path will be read out/copied/moved.
     * @param bool $isPath Whether to treat "resource" as a file path.
     * @param bool $move if §isPath is set to true,
     *
     * @param bool $move
     *
     * @return array|null an array with the submitted data, along with additional
     * properties applied during runtime, or null on failure. The $result will be
     * stored in the variable $dbResult
     *
     * @throws Conjoon_Data_Exception
     */
    protected function _createLob(Array &$data, $isPath = false, $move = false)
    {
        if (!isset($data['resource'])) {
            throw new Conjoon_Data_Exception(
                "Property \"resource not available for \" _createLob."
            );
        }

        $resource           = $data['resource'];
        $useFileSystem      = $this->_isFileSystemUsedForLobs();
        $lobStorageBasePath = $this->_getLobStorageBasePath();
        $key                = $this->_generateLobKey($data);
        $path               = null;

        Conjoon_Util_Array::apply($data, array(
            'key'                => $key,
            'lobStorageBasePath' => $lobStorageBasePath
        ));

        // try to create the folders first if filestorage is used,
        // so we still have the db as a fallback
        if ($lobStorageBasePath !== null && $useFileSystem === true) {
            $storageContainer = $this->_generateStorageContainerString($data);

            Conjoon_Util_Array::apply($data, array(
                'storageContainer' => $storageContainer
            ));

            $path = $this->_createFolder($data);
        }

        // first off, create the needed data in the database
        // if path is null, the app is either configured not to use
        // the file system for storing lobs or soemthing went wrong
        // while trying to find the dir where to store the lob
        if ($path === null) {
            if (!is_resource($resource) && $isPath === true) {
                $result = $this->_addLobFromPathToDb($data);
                if ($move === true) {
                    $this->_fileLobAccess->deleteLobForId($resource);
                }
            } else if (is_resource($resource)) {
                $result = $this->_addLobFromResourceToDb($resource, $data);
            } else {
                // string
                $result = $this->_dbLobAccess->addLob($data);
            }

            if ($result === null) {
                return null;
            }

            Conjoon_Util_Array::apply($data, array(
                'dbResult' => $result
            ));

            return $data;
        }

        // create needed data for storing file in file system
        $resource         = $data['resource'];
        $data['resource'] = "";
        $id = (int)$this->_dbLobAccess->addLob($data);

        if ($id <= 0) {
            return null;
        }

        Conjoon_Util_Array::apply($data, array(
            'id'       => $id,
            'resource' => $resource,
            'dbResult' => $id
        ));

        $file     = $this->_generateFileNameStringForLob($data);
        $fileName = $path . '/' . $file;

        // does the file already exist?
        if (@file_exists($fileName)) {
            $this->_dbLobAccess->deleteLobForId($id);
            return null;
        }


        // and finally, add the file contents to the file system
        if (is_resource($resource)) {
            $res = $this->_fileLobAccess->addLobFromStream(array(
                'path'     => $fileName,
                'resource' => $resource
            ));

        } else if ($isPath) {
            if ($move === true) {
               $res = $this->_fileLobAccess->moveLob(array(
                    'from' => $resource,
                    'to'   => $path,
                    'name' => $file
                ));
            } else {
                $res = $this->_fileLobAccess->copyLob(array(
                    'from' => $resource,
                    'to'   => $path,
                    'name' => $file
                ));
            }
        } else {
            $res = $this->_fileLobAccess->addLob(array(
                'path'     => $fileName,
                'resource' => $resource
            ));
        }

        if ($res === null) {
            $this->_dbLobAccess->deleteLobForId($id);
            return null;
        }

        return $data;
    }

    /**
     * Helper function for adding a Lob from a file path to the db.
     *
     */
    protected function _addLobFromPathToDb(Array &$data)
    {
        $path = $data['resource'];

        $fp = @fopen($path, 'rb');
        if (!$fp) {
            return null;
        }

        Conjoon_util_Array::apply($data, array('resource' => $fp));
        $result = $this->_addLobFromResourceToDb($data);
        Conjoon_util_Array::apply($data, array('resource' => $path));

        @fclose($fp);
        return $result;
    }

    /**
     * Helper function for adding a Lob from a resource to the db.
     * When caling this, you should have already validated that
     * the resource property from data is a resource.
     *
     */
    protected function _addLobFromResourceToDb(Array &$data)
    {
        $result = null;

        if ($this->_dbLobAccess->isStreamWritingSupported()) {
            $result = $this->_dbLobAccess->addLobFromStream($data);
        } else {

            $cont     = "";
            $resource = $data['resource'];

            while (!feof($resource)) {
                $cont .= fread($resource, 1024);
            }

            Conjoon_util_Array::apply($data, array('resource' => $cont));
            $result = $this->_dbLobAccess->addLob($data);
            Conjoon_util_Array::apply($data, array('resource' => $resource));
        }

        return $result;
    }

}
