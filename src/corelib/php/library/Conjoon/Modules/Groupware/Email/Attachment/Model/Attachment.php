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
 * Table data gateway. Models the table <tt>groupware_email_items_attachments</tt>.
 *
 * @uses Conjoon_Db_Table
 * @package Conjoon_Groupware_Email
 * @subpackage Model
 * @category Model
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
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
     * Adds an attachment to this table.
     *
     * @param array $data
     *
     * @return integer
     *
     * @throws InvalidArgumentException
     */
    public function addAttachmentForItem(Array $data, $itemId)
    {
        $itemId = (int)$itemId;
        if ($itemId <= 0) {
            throw new InvalidArgumentException(
                "Invalid argument for itemId - $itemId"
            );
        }

        return $this->insert(array(
            'key'                      => md5(uniqid(mt_rand(), true)),
            'groupware_email_items_id' => $itemId,
            'file_name'                => $data['file_name'],
            'mime_type'                => $data['mime_type'],
            'encoding'                 => $data['encoding'],
            'content'                  => $data['content']
        ));
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
     * @return integer
     *
     * @throws InvalidArgumentException
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

        $select = $this->select()
                  ->from($this)
                  ->where('`id`=?', $attachmentId);


        $row = $this->fetchRow($select);

        return $this->insert(array(
            'key'                      => md5(uniqid(mt_rand(), true)),
            'groupware_email_items_id' => $itemId,
            'file_name'                => $name !== null ? $name : $row->file_name,
            'mime_type'                => $row->mime_type,
            'encoding'                 => $row->encoding,
            'content'                  => $row->content
        ));
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

        return $row->toArray();
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