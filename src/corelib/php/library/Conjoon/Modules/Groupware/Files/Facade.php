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
 * @see Zend_File_Transfer_Adapter_Http
 */
require_once 'Zend/File/Transfer/Adapter/Http.php';

/**
 *
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Modules_Groupware_Files_Facade {

    /**
     * @var Conjoon_Modules_Groupware_Files_Facade $_instance
     */
    private static $_instance = null;

    /**
     * @var Conjoon_Modules_Groupware_Files_Folder_Facade $_folderFacade
     */
    private $_folderFacade = null;

    /**
     * @var Conjoon_Modules_Groupware_Files_File_Facade $_fileFacade
     */
    private $_fileFacade = null;

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
     * Returns an upload object based on the global accessible $_FILES variable.
     * The returned upload will be pre-configured with a validator for the
     * system's max upload size and the count of allowed simultaneusly uploads
     * set to 1.
     *
     * @return Zend_File_Transfer_Adapter_Http
     */
    public function generateUploadObject()
    {
        // build up upload
        $upload = new Zend_File_Transfer_Adapter_Http();

        // assign and check validators
        $upload->addValidator('Count', true, array('min' => 1, 'max' => 1));
        $upload->addValidator('Size', true, $this->_getUploadMaxFileSize());

        return $upload;
    }

    /**
     * Uploads the specified file found in $upload for the specified user
     * to the temp folder.
     * As of know, only one file per upload is permitted.
     * make sure the upload has been filtered and validated before passing it
     * to this method
     *
     * @param Zend_File_Transfer_Adapter_Http $upload
     * @param int $userId
     *
     * @return null if an error occured or Conjoon_Groupware_Files_File_Dto
     *
     * @throws InvalidArgumentException
     * @throws Conjoon_Modules_Groupware_Files_Exception
     */
    public function uploadFileToTempFolderForUser(
        Zend_File_Transfer_Adapter_Http $upload, $userId)
    {
        $this->_checkArguments(array(
            'userId'   => array('value' => &$userId,   'type' => 'int')
        ));

        if (count($upload->getFilters()) && !$upload->isFiltered()) {
            /**
             * @see Conjoon_Modules_Groupware_Files_Exception
             */
            require_once 'Conjoon/Modules/Groupware/Files/Exception.php';

            throw new Conjoon_Modules_Groupware_Files_Exception(
                "The upload has not been filtered yet."
            );
        }

        if (!$upload->isValid()) {
            /**
             * @see Conjoon_Modules_Groupware_Files_Exception
             */
            require_once 'Conjoon/Modules/Groupware/Files/Exception.php';

            throw new Conjoon_Modules_Groupware_Files_Exception(
                "The upload has not been validated yet."
            );
        }


        // check if only one file is available
        if (count(array_keys($upload->getFileInfo())) != 1) {
            throw new Conjoon_Modules_Groupware_Files_Exception(
                "Only one file per download permitted."
            );
        }

        $fileInfo = array_pop($upload->getFileInfo());
        $name     = $fileInfo['name'];
        $path     = $fileInfo['tmp_name'];
        $type     = $fileInfo['type'];

        $tmpFolderId = $this->_getFolderFacade()->getTempFolderIdForUser(
            $userId
        );

        $obj = $this->_getFileFacade()->moveFileToFolderForUserId(
            $path, $tmpFolderId, $userId, $name, $type
        );

        if (!$obj) {
            return null;
        }

        return $obj->getDto();
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
        $this->_checkArguments(array(
            'name'   => array('value' => &$name,   'type' => 'string'),
            'type'   => array('type'  => &$type,   'type' => 'string'),
            'userId' => array('value' => &$userId, 'type' => 'int')
        ));


        // first of, retrieve the id for the tmp folder of the specified user
        // in the db
        $tmpFolderId = $this->_getFolderFacade()
                       ->getTempFolderIdForUser($userId);

        if ($tmpFolderId === 0) {
            return null;
        }

        $obj = $this->_getFileFacade()->createFileInFolderForUser(
            $tmpFolderId, $name, $content, $type, $userId
        );

        if (!$obj) {
            return null;
        }

        return $obj->getDto();
    }


// -------- api

    /**
     * Returns the number of bytes denoting the maximum file size for uploads.
     *
     * @return float
     */
    public function _getUploadMaxFileSize()
    {
        /**
         * @see Zend_Registry
         */
        require_once 'Zend/Registry.php';

        /**
         * @see Conjoon_Keys
         */
        require_once 'Conjoon/Keys.php';

        /**
         * @see Zend_File_Transfer
         */
        require_once 'Zend/File/Transfer/Adapter/Http.php';

        $config = Zend_Registry::get(Conjoon_Keys::REGISTRY_CONFIG_OBJECT);
        $maxAllowedPacket = $config->database->variables->max_allowed_packet;
        if (!$maxAllowedPacket) {
            /**
             * @see Conjoon_Db_Util
             */
            require_once 'Conjoon/Db/Util.php';

            $maxAllowedPacket = Conjoon_Db_Util::getMaxAllowedPacket(
                Zend_Db_Table::getDefaultAdapter()
            );
        }

        $maxFileSize = min(
            (float)$config->files->upload->max_size,
            (float)$maxAllowedPacket
        );

        // allowed filesize is max-filesize - 33-36 % of max filesize,
        // due to base64 encoding which might happen
        $maxFileSize = $maxFileSize - round($maxFileSize/3);

        return $maxFileSize;
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
                    if ($config['value'] == "") {
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
     * @return Conjoon_Modules_Groupware_Files_Folder_Facade
     */
    private function _getFolderFacade()
    {
        if (!$this->_folderFacade) {
             /**
             * @see Conjoon_Modules_Groupware_Files_Folder_Facade
             */
            require_once 'Conjoon/Modules/Groupware/Files/Folder/Facade.php';

            $this->_folderFacade = Conjoon_Modules_Groupware_Files_Folder_Facade
                                   ::getInstance();
        }

        return $this->_folderFacade;
    }


    /**
     *
     * @return Conjoon_Modules_Groupware_Files_File_Facade
     */
    private function _getFileFacade()
    {
        if (!$this->_fileFacade) {
             /**
             * @see Conjoon_Modules_Groupware_Files_File_Facade
             */
            require_once 'Conjoon/Modules/Groupware/Files/File/Facade.php';

            $this->_fileFacade = Conjoon_Modules_Groupware_Files_File_Facade
                                   ::getInstance();
        }

        return $this->_fileFacade;
    }

}