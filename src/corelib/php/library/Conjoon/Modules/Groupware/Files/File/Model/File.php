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
 * @see Conjoon_Db_Table
 */
require_once 'Conjoon/Db/Table.php';

/**
 * @see Conjoon_Db_LobAccess
 */
require_once 'Conjoon/Db/LobAccess.php';

/**
 * @see Conjoon_Data_Exeption
 */
require_once 'Conjoon/Data/Exception.php';

/**
 * @see Conjoon_Argument_Check
 */
require_once 'Conjoon/Argument/Check.php';

/**
 * Table data gateway. Models the table <tt>groupware_files</tt>.
 *
 * @uses Conjoon_Db_Table
 * @package Conjoon_Groupware_Files
 * @subpackage Model
 * @category Model
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Modules_Groupware_Files_File_Model_File extends Conjoon_Db_Table
    implements Conjoon_Db_LobAccess {

    /**
     * The name of the table in the underlying datastore this
     * class represents.
     * @var string
     */
    protected $_name = 'groupware_files';

    /**
     * The name of the column that denotes the primary key for this table
     * @var string
     */
    protected $_primary = 'id';

    /**
     * removes the row with the specified id.
     *
     * @param integer $id
     *
     * @return integer The number of rows deleted
     *
     * @throws Conjoon_Data_Exception
     * @deprecated use deleteLob instead
     */
    public function removeFile($id)
    {
        $data = array(
            'id' => $id
        );

        return $this->deleteLobForId($data);
    }

    /**
     * Adds the given content to the file table.
     *
     * @param integer $folderId
     * @param string $name
     * @param resource|string $content
     * @param string $type
     * @param string $key
     * @param string $storageContainer
     *
     * @return integer
     *
     * @throws Conjoon_Data_Exception
     *
     * @deprecated use addLob/addLobfromStream
     */
    public function addFileToFolder($folderId, $name, $content, $type, $key,
        $storageContainer = null)
    {
        $data = array(
            'groupwareFilesFoldersId' => $folderId,
            'name'                    => $name,
            'resource'                => $content,
            'mimeType'                => $type,
            'key'                     => $key,
            'storageContainer'        => $storageContainer
        );

        if (is_resource($content)) {
            return $this->addLobFromStream($data);
        }

        return $this->addLob($data);
    }

    /**
     * Returns the file for the specified key and id plain, without
     * creating resources or similiar.
     *
     * @param string $key The key of the file to query in the database.
     * @param integer $id The id of the file to query in the database.
     *
     * @return array
     *
     * @throws Conjoon_Data_Exception
     *
     * @deprecated use getLobData
     */
    public function getFileForKeyAndId($key, $id)
    {
        $data = array(
            'includeResource'  => true,
            'key'              => $key,
            'id'               => $id
        );

        return $this->getLobData($data);
    }

    /**
     * Checks whether the specified file is accessible for the specified user.
     *
     * @param integer $id
     * @param integer $userId
     *
     * @return boolean
     *
     * @throws InvalidArgumentException
     */
    public function isFileInFolderForUser($id, $userId)
    {
        $id     = (int)$id;
        $userId = (int)$userId;

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

        $adapter = self::getDefaultAdapter();

        $select = $adapter->select()
                  ->from(array('files' => self::getTablePrefix() . 'groupware_files'),
                        array('file_id' => 'id'))
                  ->from(array('files_folders' => self::getTablePrefix() . 'groupware_files_folders'),
                        array('folder_id' => 'id'))
                  ->join(
                        array('folderUsers' => self::getTablePrefix() . 'groupware_files_folders_users'),
                        $adapter->quoteInto('folderUsers.users_id=?', $userId, 'INTEGER') .
                        ' AND ' .
                        $adapter->quoteInto('folderUsers.relationship=?', 'owner', 'STRING') .
                        ' AND '.
                        'folderUsers.groupware_files_folders_id=files_folders.id',
                        array())
                  ->where('files.id = ? ', $id);

        $row = $adapter->fetchRow($select);

        if (!$row || empty($row)) {
            return false;
        }

        return true;
    }

    /**
     * Returns the file data without the content for the specified key
     * and id.
     *
     * @param string $key
     * @param integer $id
     *
     * @return array
     *
     * @throws Conjoon_Data_Exception
     *
     * @deprecated use getLobData
     */
    public function getFileDataForKeyAndId($key, $id)
    {
        $data = array(
            'key' => $key,
            'id'  => $id
        );

        return $this->getLobData($data);
    }

    /**
     * Tries to return the contents from the file as a stream. If that
     * cannot be realized, the content will be returned as a string.
     *
     * @param string $key
     * @param integer $id
     *
     * @return resource|null
     *
     * @throws Conjoon_Data_Exception
     *
     * @deprecated use getLobAsStream
     */
    public function getFileContentAsStreamForKeyAndId($key, $id)
    {
        $data = array(
            'key' => $key,
            'id'  => $id
        );

        if (!$this->isStreamAccessSupported()) {
            return $this->getLobContent($data);
        }

        return $this->getLobAsStream($data);
    }

// --------- Conjoon_Data_LobAccess

    /**
     * Copies a Lob.
     * The following properties have to be specified in $data
     * - id The id of the row from which the row should be copied
     * - key The key of the row that should be copied
     * - newKey The new key for the copied row
     * - name optional the new name for the lob
     * - storageContainer optional a name for the storageContainer
     * if not specified, the one from the original entry will be used
     * - groupwareFilesFoldersId optional, the new folderId for the file.
     * If not specified, the folder from the original entry will be used
     *
     * @param array $data
     *
     * @return id the id of the newly inserted row, or null if this was not
     * successfull
     *
     * @throws Conjoon_Data_Exception
     */
    public function copyLob(Array $data)
    {
        Conjoon_Argument_Check::check(array(
            'id' => array('type' => 'int'),
            'key' => array('type' => 'string'),
            'newKey' => array('type' => 'string')
        ), $data);

        $name = isset($data['name']) ? $data['name'] : null;

        $storageContainer = isset($data['storageContainer'])
                            ? $data['storageContainer'] : null;

        $folderId = isset($data['groupwareFilesFoldersId'])
                ? $data['groupwareFilesFoldersId'] : null;

        if ($folderId !== null) {
            $folderId = (int)$folderId;
            if ($folderId <= 0) {
               throw new Conjoon_Data_Exception(
                    "Invalid argument for groupwareFilesFoldersId - $groupwareFilesFoldersId"
                );
            }
        }

        if ($name !== null) {
            $name = trim((string)$name);
            if ($name == "") {
               throw new Conjoon_Data_Exception(
                    "Invalid argument for name - $name"
                );
            }
        }

        if ($storageContainer !== null) {
            $storageContainer = trim((string)$storageContainer);
            if ($storageContainer == "") {
               throw new Conjoon_Data_Exception(
                    "Invalid argument for storageContainer - $storageContainer"
                );
            }
        }

        $db = self::getDefaultAdapter();

        /**
         * @see Zend_Db_Adapter_Pdo_Mysql
         */
        require_once 'Zend/Db/Adapter/Pdo/Mysql.php';

        if (!($db instanceof Zend_Db_Adapter_Pdo_Mysql)) {
            /**
             * @see Conjoon_Exception
             */
            require_once 'Conjoon/Exception.php';

            throw new Conjoon_Data_Exception(
                "Cannot copy data - "
                ."adapter not of type Zend_Db_Adapter_Pdo_Mysql, but ".
                get_class($db)
            );
        }

        $stmt = $db->query("INSERT INTO ".
                   "`". self::getTablePrefix() . "groupware_files`
                   (`key`, `groupware_files_folders_id`, `name`,
                   `mime_type`, `content`, `storage_container`
                   )
                   (SELECT "
                   .$db->quote($data['newKey'])." AS `key`, "

                   .(!$folderId ? "`groupware_files_folders_id`, "
                                : $db->quote($folderId)." AS `groupware_files_folders_id`, ")
                   .(!$name ? "`name`," : $db->quote($name)." AS `name`,")
                   ." `mime_type`, `content`,"
                   .(!$storageContainer ? "`storage_container`"
                                        : $db->quote($storageContainer)." AS `storage_container`")
                   ." FROM "
                   ."`".self::getTablePrefix() . "groupware_files`
                   WHERE `id` = ".$data['id']
                   ." AND `key`=".$db->quote($data['key']).")"
        );

        $result = $stmt->rowCount();
        if ($result > 0) {
            return $db->lastInsertId();
        }
        return null;
    }

    /**
     * Moves a Lob.
     * The following properties have to be specified in $data
     * - id The id of the row from which the row should be moved
     * - key The key of the row that should be moved
     * - name optional the new name for the lob
     * - groupwareFilesFoldersId the new folderId for the lob.
     * If not specified, the folder from the original entry will be used
     * - storageContainer optional a name for the storageContainer
     * if not specified, the one from the original entry will be used
     *
     * @param array $data
     *
     * @return integer the id of the lob which should equal to the id passed
     * in the data array, otherwise null
     *
     * @throws Conjoon_Data_Exception
     */
    public function moveLob(Array $data)
    {
        Conjoon_Argument_Check::check(array(
            'id' => array('type' => 'int'),
            'key' => array('type' => 'string'),
            'groupwareFilesFoldersId' => array('type' => 'int')
        ), $data);

        $name = isset($data['name']) ? $data['name'] : null;

        $storageContainer = isset($data['storageContainer'])
                            ? $data['storageContainer'] : null;

        if ($name !== null) {
            $name = trim((string)$name);
            if ($name == "") {
               throw new Conjoon_Data_Exception(
                    "Invalid argument for name - $name"
                );
            }
        }

        if ($storageContainer !== null) {
            $storageContainer = trim((string)$storageContainer);
            if ($storageContainer == "") {
               throw new Conjoon_Data_Exception(
                    "Invalid argument for storageContainer - $storageContainer"
                );
            }
        }

        $dataArray = array(
            'groupware_files_folders_id' => $data['groupwareFilesFoldersId']
        );

        if ($name !== null) {
            $dataArray['name'] = $name;
        }

        if ($name !== null) {
            $dataArray['storage_container'] = $storageContainer;
        }

        if ($this->update($dataArray, 'id='.$data['id']." AND key='".$data['key']."'") > 0) {
            return $data['id'];
        }

        return null;
    }

    /**
     * Deletes a lob for an id.
     * The following properties need to be available in $data
     * - id the id of the row
     *
     * @param int  $data
     *
     * @return bool true on success, otherwise false
     *
     * @throws Conjoon_Data_Exception
     */
    public function deleteLobForId($id)
    {
        $data = array('id' => $id);
        Conjoon_Argument_Check::check(array(
            'id' => array('value' => $id, 'type' => 'int')
        ), $data);

        $id = $data['id'];

        if ($this->delete('id='.$id) > 0) {
            return true;
        }

        return false;
    }

    /**
     * Deletes a lob.
     * The following properties need to be available in $data
     * - id the id of the row
     *
     * @param array $data
     *
     * @return bool true on success, otherwise false
     *
     * @throws Conjoon_Data_Exception
     */
    public function deleteLob(Array $data)
    {
        Conjoon_Argument_Check::check(array(
            'id' => array('type' => 'int')
        ), $data);

        if ($this->delete('id='.$data['id']) > 0) {
            return true;
        }

        return false;
    }

    /**
     * Sets the name for a lob.
     * The following properties need to be available in $data
     * - id the id of the row
     * - key the key of the row
     * - name The new name for the lob
     *
     * @param array $data
     *
     * @return bool true on success, otherwise false
     *
     * @throws Conjoon_Data_Exception
     */
    public function setLobName(Array $data)
    {
        Conjoon_Argument_Check::check(array(
            'id' => array('type' => 'int'),
            'key' => array('type' => 'string'),
            'name' => array('type' => 'string')
        ), $data);

        if ($this->update(array(
                'name' => $data['name']
            ), 'id='.$data['id']." AND key='".$data['key']."'") > 0) {
            return true;
        }

        return false;
    }

    /**
     * Returns the lob's content.
     * The following properties need to be available in $data
     * - id the id of the row
     * - key the key of the row
     *
     * @param array $data
     *
     * @return string
     *
     * @throws Conjoon_Data_Exception
     */
    public function getLobContent(Array $data)
    {
        Conjoon_Argument_Check::check(array(
            'id' => array('type' => 'int'),
            'key' => array('type' => 'string')
        ), $data);

        $select = $this->select()
                  ->from($this,  array(
                    'content'
                  ))
                  ->where('`id`=?', $data['id'])
                  ->where('`key`=?', $data['key']);

        $row = $this->fetchRow($select);

        if (!$row) {
            return "";
        }

        return (string)$row->content;
    }

    /**
     * Returns the meta data for the lob.
     * - id the id of the row
     * - key the key of the row
     * - includeResource true to return the content. Content will be available
     * in the "resource" property of the returned array
     *
     * @param array $data
     *
     * @return array or null if not exists
     *
     * @throws Conjoon_Data_Exception
     */
    public function getLobData(Array $data)
    {
        Conjoon_Argument_Check::check(array(
            'id' => array('type' => 'int'),
            'key' => array('type' => 'string')
        ), $data);

        $fields = array(
            'id',
            'groupware_files_folders_id',
            'key',
            'name',
            'mime_type',
            'storage_container',
        );

        if (isset($data['includeResource']) && $data['includeResource'] === true) {
            $fields[] = 'content AS resource';
        }

        $select = $this->select()
                  ->from($this,  $fields)
                  ->where('`id`=?',  $data['id'])
                  ->where('`key`=?', $data['key']);

        $row = $this->fetchRow($select);

        if (!$row || empty($row)) {
            return null;
        }

        return $row->toArray();
    }

    /**
     * Returns a stream resource for a lob.
     * - id the id of the row
     * - key the key of the row
     *
     * @param array $data
     *
     * @return resource|null
     *
     * @throws Conjoon_Data_Exception
     * @see isStreamAccessSupported
     */
    public function getLobAsStream(Array $data)
    {
        if (!$this->isStreamAccessSupported()) {
            throw new Conjoon_Data_Exception(
                "Stream access for ".get_class($this)." is not supported."
            );
        }

        Conjoon_Argument_Check::check(array(
            'id' => array('type' => 'int'),
            'key' => array('type' => 'string')
        ), $data);

        $db = self::getDefaultAdapter();

        $statement = $db->prepare(
            "SELECT `content` FROM `".self::getTablePrefix() . "groupware_files`
             WHERE `key` = :key AND `id` = :id"
        );


        $statement->bindParam(':key', $data['key'], PDO::PARAM_STR);
        $statement->bindParam( ':id', $data['id'],  PDO::PARAM_INT);

        $statement->execute();

        $content = null;

        $statement->bindColumn('content', $content, PDO::PARAM_LOB);

        $statement->fetch(PDO::FETCH_BOUND);

        if (!is_resource($content)) {
            return null;
        }

        return $content;
    }

    /**
     * Returns true if this class is capable of returning a lob as a
     * stream, otherwise false
     *
     * @return bool
     */
    public function isStreamAccessSupported()
    {
        $db = self::getDefaultAdapter();

        /**
         * @see Zend_Db_Adapter_Pdo_Mysql
         */
        require_once 'Zend/Db/Adapter/Pdo/Mysql.php';

        if (!($db instanceof Zend_Db_Adapter_Pdo_Mysql)) {
            /**
             * @see Conjoon_Exception
             */
            require_once 'Conjoon/Exception.php';

            throw new Conjoon_Exception(
                "Cannot get file content  - "
                ."adapter not of type Zend_Db_Adapter_Pdo_Mysql, but ".
                get_class($db)
            );
        }

        $version = $db->getServerVersion();

        if (!$version) {
            return false;
        }

        $res = version_compare($version, '5.1');

        return $res < 0;
    }

    /**
     * Returns true if this class is capable of writing a lob as a
     * stream, otherwise false
     *
     * @return bool
     */
    public function isStreamWritingSupported()
    {
        return $this->isStreamAccessSupported();
    }

    /**
     * Saves the lob specified in $data
     * The following properties must be available in $data:
     * - groupwareFilesFoldersId
     * - key
     * - name
     * - mimeType
     * - resource
     * - storageContainer
     *
     * @param array $data
     *
     * @return mixed a unique identifier for this lob, or null
     *
     * @throws Conjoon_Data_Exception
     */
    public function addLob(Array $data)
    {
        Conjoon_Argument_Check::check(array(
            'groupwareFilesFoldersId' => array(
                'type' => 'int'
            ),
            'key' => array(
                'type' => 'string'
            ),
            'name' => array(
                'type' => 'string'
            ),
            'mimeType' => array(
                'type' => 'string'
            ),
            'storageContainer' => array(
                'type' => 'string',
                'allowEmpty' => true,
                'mandatory' => false,
                'default' => null
            )
        ), $data);

        if (isset($data['resource']) && is_resource($data['resource'])) {
            throw new Conjoon_Exception(
                "Passed property resource must not be a resource"
            );
        }

        $db = self::getDefaultAdapter();

        $res = $this->insert(array(
            'groupware_files_folders_id' => $data['groupwareFilesFoldersId'],
            'key'                        => $data['key'],
            'name'                       => $data['name'],
            'mime_type'                  => $data['mimeType'],
            'storage_container'          => $data['storageContainer'],
            'content'                    => $data['resource'] ? $data['resource'] : ""
        ));

        if (!$res || $res < 0) {
            return null;
        }

        return (int)$res;
    }

    /**
     * Adds the lob from a stream.
     * - groupwareFilesFoldersId
     * - key
     * - name
     * - mimeType
     * - resource
     * - storageContainer
     *
     * @param array $data
     *
     * @return mixed a unique identifier for this lob, or null
     *
     * @throws Conjoon_Data_Exception
     * @see isStreamWritingSupported
     */
    public function addLobFromStream(Array $data)
    {
        if (!$this->isStreamWritingSupported()) {
            throw new Conjoon_Data_Exception(
                "Stream writing for ".get_class(self)." is not supported."
            );
        }

        Conjoon_Argument_Check::check(array(
            'groupwareFilesFoldersId' => array('type' => 'int'),
            'key' => array('type' => 'string'),
            'name' => array('type' => 'string'),
            'mimeType' => array('type' => 'string'),
            'storageContainer' => array(
                'type' => 'string',
                'allowEmpty' => true,
                'mandatory' => false,
                'default' => null
            )
        ), $data);

        if (isset($data['resource']) && !is_resource($data['resource'])) {
            throw new Conjoon_Exception(
                "Passed property resource must be a resource"
            );
        }

        $db = self::getDefaultAdapter();

        $statement = $db->prepare(
            "INSERT INTO `".self::getTablePrefix() . "groupware_files`
              (
              `name`,
              `mime_type`,
              `key`,
              `content`,
              `groupware_files_folders_id`,
              `storage_container`
              )
              VALUES
              (
                :name,
                :mime_type,
                :key,
                :content,
                :groupware_files_folders_id,
                :storage_container
            )"
        );

        $statement->bindParam(':key', $data['key'], PDO::PARAM_STR);
        $statement->bindParam( ':groupware_files_folders_id', $data['groupwareFilesFoldersId'],
            PDO::PARAM_INT
        );
        $statement->bindParam(':name',$data['name'], PDO::PARAM_STR);
        $statement->bindParam(':mime_type', $data['mimeType'], PDO::PARAM_STR);
        $statement->bindParam(':content', $data['resource'], PDO::PARAM_LOB);
        $statement->bindParam(':storage_container', $data['storageContainer'], PDO::PARAM_STR);

        $statement->execute();

        $result = $statement->rowCount();
        if ($result > 0) {
            return $db->lastInsertId();
        }
        return null;
    }


}
