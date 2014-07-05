<?php
/**
 * conjoon
 * (c) 2007-2014 conjoon.org
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