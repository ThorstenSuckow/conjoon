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
 * @see Conjoon_Db_Table
 */
require_once 'Conjoon/Db/Table.php';

/**
 * Table data gateway. Models the table <tt>groupware_files</tt>.
 *
 * @uses Conjoon_Db_Table
 * @package Conjoon_Groupware_Files
 * @subpackage Model
 * @category Model
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Conjoon_Modules_Groupware_Files_File_Model_File extends Conjoon_Db_Table {

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
     * Adds the given content to the file table.
     *
     * @param integer $folderId
     * @param string $name
     * @param resource|string $content
     * @param string $type
     * @param string $key
     *
     * @return integer
     *
     * @throws InvalidArgumentException
     */
    public function addFileToFolder($folderId, $name, $content, $type, $key)
    {
        $folderId = (int)$folderId;
        $name     = trim((string)$name);
        $type     = trim((string)$type);
        $key      = trim((string)$key);

        if ($folderId <= 0) {
            throw new InvalidArgumentException(
                "Invalid argument supplied for folderId - $folderId"
            );
        }
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
        if ($key == "") {
            throw new InvalidArgumentException(
                "Invalid argument supplied for key - $key"
            );
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

            throw new Conjoon_Exception(
                "Cannot add file data - adapter not of type "
                ."Zend_Db_Adapter_Pdo_Mysql, but ".get_class($db)
            );
        }

        $statement = $db->prepare(
            "INSERT INTO `".self::getTablePrefix() . "groupware_files`
              (
              `name`,
              `mime_type`,
              `key`,
              `content`,
              `groupware_files_folders_id`
              )
              VALUES
              (
                :name,
                :mime_type,
                :key,
                :content,
                :groupware_files_folders_id
            )"
        );

        $statement->bindParam(':key', $key, PDO::PARAM_STR);
        $statement->bindParam( ':groupware_files_folders_id', $folderId,
            PDO::PARAM_INT
        );
        $statement->bindParam(':name',$name, PDO::PARAM_STR);
        $statement->bindParam(':mime_type', $type, PDO::PARAM_STR);
        $statement->bindParam(':content', $content, PDO::PARAM_LOB);

        $statement->execute();

        $result = $statement->rowCount();
        if ($result > 0) {
            return $db->lastInsertId();
        }
        return 0;
    }

    /**
     * Returns the file for the specified key and id.
     *
     * @param string $key The key of the file to query in the database.
     * @param integer $id The id of the file to query in the database.
     *
     * @return array
     *
     * @throws InvalidArgumentException
     */
    public function getFileForKeyAndId($key, $id)
    {
        $key = trim((string)$key);
        $id  = (int)$id;

        if ($key == "") {
            throw new InvalidArgumentException("Invalid argument for key - $key");
        }

        if ($id <= 0) {
            throw new InvalidArgumentException("Invalid argument for id - $id");
        }

        $select = $this->select()
                  ->from($this)
                  ->where('`id`=?', $id)
                  ->where('`key`=?', $key);

        $row = $this->fetchRow($select);

        if (!$row) {
            return array();
        }

        return $row->toArray();
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
     * @throws InvalidArgumentException
     */
    public function getFileDataForKeyAndId($key, $id)
    {
        $key = trim((string)$key);
        $id  = (int)$id;

        if ($key == "") {
            throw new InvalidArgumentException("Invalid argument for key - $key");
        }

        if ($id <= 0) {
            throw new InvalidArgumentException("Invalid argument for id - $id");
        }

        $select = $this->select()
                  ->from($this,  array(
                    'id',
                    'key',
                    'groupware_files_folders_id',
                    'name',
                    'mime_type'
                  ))
                  ->where('`id`=?', $id)
                  ->where('`key`=?', $key);

        $row = $this->fetchRow($select);

        if (!$row) {
            return array();
        }

        return $row->toArray();
    }

    /**
     * Tries to return the contents from the file as a stream. If that
     * cannot be realized, the content will be returned as a string.
     *
     * @param string $key
     * @param integer $id
     *
     * @return resource|string
     *
     * @throws InvalidArgumentException
     * @throws Conjoon_Exception
     */
    public function getFileContentAsStreamForKeyAndId($key, $id)
    {
        $key = trim((string)$key);
        $id  = (int)$id;

        if ($key == "") {
            throw new InvalidArgumentException(
                "Invalid argument for key - $key"
            );
        }
        if ($id <= 0) {
            throw new InvalidArgumentException(
                "Invalid argument for id - $id"
            );
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

            throw new Conjoon_Exception(
                "Cannot get file content  - "
                ."adapter not of type Zend_Db_Adapter_Pdo_Mysql, but ".
                get_class($db)
            );
        }

        $statement = $db->prepare(
            "SELECT `content` FROM `".self::getTablePrefix() . "groupware_files`
             WHERE `key` = :key AND `id` = :id"
        );
        $statement->setFetchMode(PDO::FETCH_BOUND);

        $statement->bindParam(':key', $key, PDO::PARAM_STR);
        $statement->bindParam( ':id', $id,  PDO::PARAM_INT);

        $statement->execute();

        $statement->bindColumn('content', $content, PDO::PARAM_LOB);

        $statement->fetch();

        return $content;
    }

}