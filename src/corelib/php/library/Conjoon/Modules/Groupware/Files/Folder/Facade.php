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
 *
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Modules_Groupware_Files_Folder_Facade {

    /**
     * @var Conjoon_Modules_Groupware_Files_Folder_Facade $_instance
     */
    private static $_instance = null;

    /**
     * @var Conjoon_Modules_Groupware_Files_Folder_Model_Folder
     */
    private $_folderModel = null;


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
     * Returns the id for the temp folder for the specified user.
     * If the temp folder for this user does not exist, it will be
     * created.
     *
     * @param int $userId
     *
     * @return int
     *
     * @throws InvalidArgumentException
     */
    public function getTempFolderIdForUser($userId)
    {
        return $this->createTempFolderForUser($userId);
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

}