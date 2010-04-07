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
     */
    public function getAttachmentsForItem($groupwareEmailItemsId)
    {
        if ((int)$groupwareEmailItemsId <= 0) {
            return array();
        }

        $select = $this->select()
                  ->from($this)
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