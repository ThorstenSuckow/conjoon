<?php
/**
 * conjoon
 * (c) 2002-2009 siteartwork.de/conjoon.org
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
 * Zend_Db_Table
 */
require_once 'Zend/Db/Table/Abstract.php';

/**
 * Table data gateway. Models the table <tt>groupware_email_items_attachments</tt>.
 *
 * @uses Zend_Db_Table
 * @package Intrabuild_Groupware_Email
 * @subpackage Model
 * @category Model
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Intrabuild_Modules_Groupware_Email_Attachment_Model_Attachment
    extends Zend_Db_Table_Abstract implements Intrabuild_BeanContext_Decoratable {

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

// -------- interface Intrabuild_BeanContext_Decoratable

    public function getRepresentedEntity()
    {
        return 'Intrabuild_Modules_Groupware_Email_Attachment';
    }

    public function getDecoratableMethods()
    {
        return array(
            'getAttachmentsForItem'
        );
    }

}