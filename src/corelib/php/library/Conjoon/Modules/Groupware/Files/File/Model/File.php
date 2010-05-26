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
     * @param integer $userId
     * @param string $name
     * @param string $content
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

        return (int)$this->insert(array(
            'name'                       => $name,
            'mime_type'                  => $type,
            'key'                        => $key,
            'content'                    => $content,
            'groupware_files_folders_id' => $folderId
        ));
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

}