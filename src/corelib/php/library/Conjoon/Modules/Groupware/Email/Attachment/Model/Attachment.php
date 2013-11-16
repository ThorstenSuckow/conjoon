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
 * @see Conjoon_Db_Table
 */
require_once 'Conjoon/Db/Table.php';

/**
 * Table data gateway. Models the table <tt>groupware_email_items_attachments</tt>.
 *
 * @uses Conjoon_Db_Table
 * @package Conjoon_Groupware_Email
 * @subpackage Model
 * @category Model
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Modules_Groupware_Email_Attachment_Model_Attachment
    extends Conjoon_Db_Table implements Conjoon_BeanContext_Decoratable {

    /**
     * The name of the table in the underlying datastore this
     * class represents.
     * @var string
     */
    protected $_name = 'groupware_email_items_attachments';

    /**
     * The name of the column that denotes the primary key for this table
     * @var string
     */
    protected $_primary = 'id';

    /**
     * Copies a file from the file table to the attachment table.
     * The content will be stored as base64 encoded.
     *
     * @param string $key The key from the file to copy to the
     * attachments table
     * @param integer $id The id of the file to copy to the
     * attachment table
     * @param integer $itemId the id of the email item for which the file gets copied
     * @param string $name optional, the new name to store newly
     * created attachment under
     *
     * @return integer The id of the newly created row, or 0
     *
     * @throws InvalidArgumentException
     * @throws Conjoon_Exception
     */
    public function copyFromFilesForItemId($key, $id, $itemId, $name = null)
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

        if ($itemId <= 0) {
            throw new InvalidArgumentException(
                "Invalid argument for itemId - $itemId"
            );
        }

        if ($name !== null) {
            $name = trim((string)$name);
            if ($name == "") {
               throw new InvalidArgumentException(
                    "Invalid argument for name - $name"
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

            throw new Conjoon_Exception(
                "Cannot copy file data to attachments attachment data - "
                ."adapter not of type Zend_Db_Adapter_Pdo_Mysql, but ".
                get_class($db)
            );
        }

        $stmt = $db->query("INSERT INTO ".
                   "`".self::getTablePrefix() . "groupware_email_items_attachments`
                   (`key`, `groupware_email_items_id`, `file_name`, `mime_type`,
                   `content`)
                   (SELECT "
                   ."'".md5(uniqid(mt_rand(), true))."' AS `key`, "
                   .$itemId." AS `groupware_email_items_id`,"
                   .(!$name ? "`name`," : $db->quote($name)." AS `name`,")
                   ." `mime_type`, `content` FROM "
                   ."`".self::getTablePrefix() . "groupware_files`
                   WHERE `id` = ".$id." AND `key`=".$db->quote($key).")"
        );

        $result = $stmt->rowCount();
        if ($result > 0) {
            return $db->lastInsertId();
        }
        return 0;
    }

    /**
     * Tries to return the contents from the attachment as a stream. If that
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
    public function getAttachmentContentAsStreamForKeyAndId($key, $id)
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
                "Cannot get attachment content - "
                ."adapter not of type Zend_Db_Adapter_Pdo_Mysql, but ".
                get_class($db)
            );
        }

        $statement = $db->prepare(
            "SELECT `content` FROM `".self::getTablePrefix() . "groupware_email_items_attachments`
             WHERE `key` = :key AND `id` = :id"
        );
        $statement->setFetchMode(PDO::FETCH_BOUND);

        $statement->bindParam(':key', $key, PDO::PARAM_STR);
        $statement->bindParam( ':id', $id,  PDO::PARAM_INT);

        $statement->execute();

        $statement->bindColumn(1, $content, PDO::PARAM_LOB);

        $statement->fetch();

        return $content;
    }

    /**
     * Adds an attachment to this table.
     *
     * @param array $data
     *
     * @return integer
     *
     * @throws InvalidArgumentException
     * @throws Conjoon_Exception
     */
    public function addAttachmentForItem(Array $data, $itemId)
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
                "Cannot add attachment data - adapter not of type "
                ."Zend_Db_Adapter_Pdo_Mysql, but ".get_class($db)
            );
        }

        // insert into attachment content
        $statement = $db->prepare(
            "INSERT INTO `".self::getTablePrefix() . "mail_attachment_content`
              (`content`)
              VALUES
              (:content)"
        );
        $statement->bindParam(':content', $data['content'], PDO::PARAM_LOB);
        $statement->execute();
        $result = $statement->rowCount();
        if ($result > 0) {
            $mailAttachmentContentId = $db->lastInsertId();
        } else {
            return 0;
        }


        $statement = $db->prepare(
            "INSERT INTO `".self::getTablePrefix() . "groupware_email_items_attachments`
              (
              `key`,
              `groupware_email_items_id`,
              `file_name`,
              `mime_type`,
              `encoding`,
              `mail_attachment_content_id`,
              `content_id`
              )
              VALUES
              (
                :key,
                :groupware_email_items_id,
                :file_name,
                :mime_type,
                :encoding,
                :mail_attachment_content_id,
                :content_id
            )"
        );

        $key = (isset($data['key']) ? $data['key'] : md5(uniqid(mt_rand(), true)));

        $statement->bindParam(':key', $key, PDO::PARAM_STR);
        $statement->bindParam( ':groupware_email_items_id', $itemId,
            PDO::PARAM_INT
        );
        $statement->bindParam(':file_name',$data['file_name'], PDO::PARAM_STR);
        $statement->bindParam(':mime_type', $data['mime_type'], PDO::PARAM_STR);
        $statement->bindParam(':encoding', $data['encoding'], PDO::PARAM_STR);
        $statement->bindParam(':mail_attachment_content_id', $mailAttachmentContentId, PDO::PARAM_STR);
        $statement->bindParam(':content_id', $data['content_id'], PDO::PARAM_STR);

        $statement->execute();

        $result = $statement->rowCount();
        if ($result > 0) {
            return $db->lastInsertId();
        }
        return 0;
    }

    /**
     * Deletes an attachment with the specified id.
     *
     * @param integer $id The id of the attachment
     *
     * @return integer
     *
     * @throws InvalidArgumentException
     */
    public function deleteAttachmentForId($id)
    {
        $id = (int)$id;

        if ($id <= 0) {
            throw new InvalidArgumentException(
                "Invalid argument supplied for id - $id"
            );
        }

        return $this->delete('id='.$id);
    }

    /**
     * Updates the name for a given attachment
     *
     * @param integer $id
     * @param string $name
     *
     * @return int
     *
     * @throws InvalidArgumentException
     */
    public function updateNameForAttachment($id, $name)
    {
        $id   = (int)$id;
        $name = trim((string)$name);

        if ($id <= 0) {
            throw new InvalidArgumentException(
                "Invalid argument for id - $id"
            );
        }
        if ($name == "") {
            throw new InvalidArgumentException(
                "Invalid argument for name - $name"
            );
        }

        return $this->update(array('file_name' => $name), 'id='.$id);
    }

    /**
     * Copies an attachment entry and saves it for a new item id.
     * If the name was supplied, the attachment will also be stored under the
     * new name.
     *
     * @param integer  $attachmentId
     * @param $integer $itemId
     * @param string $name
     *
     * @return integer The id of teh last inserted id, or 0
     *
     * @throws InvalidArgumentException
     * @throws Conjoon_Exception
     */
    public function copyAttachmentForNewItemId($attachmentId, $itemId, $name = null)
    {
        $attachmentId = (int)$attachmentId;
        $itemId       = (int)$itemId;

        if ($attachmentId <= 0) {
            throw new InvalidArgumentException(
                "Invalid argument for attachmentId - $attachmentId"
            );
        }
        if ($itemId <= 0) {
            throw new InvalidArgumentException(
                "Invalid argument for itemId - $itemId"
            );
        }
        if ($name !== null) {
            $name = trim((string)$name);
            if ($name == "") {
               throw new InvalidArgumentException(
                    "Invalid argument for name - $name"
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

            throw new Conjoon_Exception(
                "Cannot add attachment data - adapter not of type "
                ."Zend_Db_Adapter_Pdo_Mysql, but ".get_class($db)
            );
        }

        $stmt = $db->query("INSERT INTO ".
                   "`".self::getTablePrefix() . "groupware_email_items_attachments`
                   (`key`, `groupware_email_items_id`, `file_name`, `mime_type`,
                   `encoding`, `content`)
                   (SELECT "
                   ."'".md5(uniqid(mt_rand(), true))."' AS `key`, "
                   .$itemId." AS `groupware_email_items_id`,"
                   .(!$name ? "`file_name`," : $db->quote($name)." AS `file_name`,")
                   ." `mime_type`, `encoding`, `content` FROM "
                   ."`".self::getTablePrefix() . "groupware_email_items_attachments`
                   WHERE id = ".$attachmentId.")"
        );

        $result = $stmt->rowCount();
        if ($result > 0) {
            return $db->lastInsertId();
        }
        return 0;
    }

    /**
     * Returns the attachment for the specified id.
     * This method should only be used when its clear that the attachment for
     * the specified id belongs to the user the attachment gets read out for.
     *
     * @param integer $id The id of the attachment to query in the database.
     *
     * @return array
     *
     * @throws InvalidArgumentException
     */
    public function getAttachmentForId($id)
    {
        $id  = (int)$id;
        if ($id <= 0) {
            throw new InvalidArgumentException("Invalid argument for id - $id");
        }

        $select = $this->select()
                  ->from($this)
                  ->where('`id`=?', $id);

        $row = $this->fetchRow($select);

        if (!$row) {
            return array();
        }

        return $row->toArray();
    }


    /**
     * Returns the attachment for the specified key and id.
     *
     * @param string $key The key of the attachment to query in the database.
     * @param integer $id The id of the attachment to query in the database.
     *
     * @return array
     *
     * @throws InvalidArgumentException
     */
    public function getAttachmentForKeyAndId($key, $id)
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

        $d = $row->toArray();

        $attachmentContentId = $row['mail_attachment_content_id'];

        $select = $this->select()
            ->from('mail_attachment_content_id')
            ->where('`id`=?', $attachmentContentId);

        $row = $this->fetchRow($select);

        if (!$row) {
            return array();
        }

        $d['content'] = $row->content;

        return $d;
    }

    /**
     * Returns the email item id for the specified attachment key and id.
     * Note: If the attachment was not found, this method will return "0"!
     *
     * @param string $key The key of the attachment to query in the database.
     * @param integer $id The id of the attachment to query in the database.
     *
     * @return integer
     *
     * @throws InvalidArgumentException
     */
    public function getEmailItemIdForAttachmentKeyAndId($key, $id)
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
                  ->from($this, 'groupware_email_items_id')
                  ->where('`id`=?', $id)
                  ->where('`key`=?', $key);

        $row = $this->fetchRow($select);

        if (!$row) {
            return 0;
        }

        $id = $row->groupware_email_items_id;

        return (int)$id;
    }

    /**
     * Returns all the attachments for the specified item.
     *
     * @return Zend_Db_Table_Rowset
     */
    public function getAttachmentsForItem($groupwareEmailItemsId)
    {
        if ((int)$groupwareEmailItemsId <= 0) {
            return array();
        }

        $select = $this->select()
                  ->from($this, array(
                    'id',
                    'key',
                    'groupware_email_items_id',
                    'file_name',
                    'mime_type',
                    'encoding',
                    'content_id',
                  ))
                  ->where('groupware_email_items_id=?', $groupwareEmailItemsId)
                  ->order('id ASC');

        return $this->fetchAll($select);
    }

    /**
     * Returns the attachment data without the content for the specified key
     * and id.
     *
     * @param string $key
     * @param integer $id
     *
     * @return array
     *
     * @throws InvalidArgumentException
     */
    public function getAttachmentDataForKeyAndId($key, $id)
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
                    'groupware_email_items_id',
                    'file_name',
                    'mime_type',
                    'encoding',
                    'content_id',
                  ))
                  ->where('`id`=?', $id)
                  ->where('`key`=?', $key);

        $row = $this->fetchRow($select);

        if (!$row) {
            return array();
        }

        return $row->toArray();
    }

// -------- interface Conjoon_BeanContext_Decoratable

    public function getRepresentedEntity()
    {
        return 'Conjoon_Modules_Groupware_Email_Attachment';
    }

    public function getDecoratableMethods()
    {
        return array(
            'getAttachmentsForItem'
        );
    }

}
