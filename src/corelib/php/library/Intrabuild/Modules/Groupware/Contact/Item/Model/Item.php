<?php
/**
 * intrabuild
 * (c) 2002-2008 siteartwork.de/MindPatterns
 * license@siteartwork.de
 *
 * $Author: T. Suckow $
 * $Id: Item.php 104 2008-08-27 02:19:02Z T. Suckow $
 * $Date: 2008-08-27 04:19:02 +0200 (Mi, 27 Aug 2008) $
 * $Revision: 104 $
 * $LastChangedDate: 2008-08-27 04:19:02 +0200 (Mi, 27 Aug 2008) $
 * $LastChangedBy: T. Suckow $
 * $URL: file:///F:/svn_repository/intrabuild/trunk/src/corelib/php/library/Intrabuild/Modules/Groupware/Email/Item/Model/Item.php $
 */

/**
 * Zend_Db_Table
 */
require_once 'Zend/Db/Table/Abstract.php';

/**
 * Intrabuild_BeanContext_Decoratable
 */
require_once 'Intrabuild/BeanContext/Decoratable.php';

/**
 * Table data gateway. Models the table <tt>groupware_contact_items</tt>.
 *
 *
 *
 * @uses Zend_Db_Table
 * @package Intrabuild_Groupware_Email
 * @subpackage Model
 * @category Model
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Intrabuild_Modules_Groupware_Contact_Item_Model_Item
    extends Zend_Db_Table_Abstract implements Intrabuild_BeanContext_Decoratable {


    /**
     * The name of the table in the underlying datastore this
     * class represents.
     * @var string
     */
    protected $_name = 'groupware_contact_items';

    /**
     * The name of the column that denotes the primary key for this table
     * @var string
     */
    protected $_primary = 'id';

    /**
     * Returns all contact items which first/lastname or email address
     * matches a part of the submitted query-string for the specified user.
     *
     * @param integer $userId The id of the user to get the contacts for
     * @param string $query The part the name/email-address should contain
     *
     * @return Array
     */
    public function getContactsByNameOrEmailAddress($userId, $query)
    {
        $userId = (int)$userId;

        if ($userId <= 0) {
            return array();
        }

        $adapter = $this->getDefaultAdapter();

        $select= $adapter->select()
                ->from(array('items' => 'groupware_contact_items'),
                  array(
                      'id',
                      'first_name',
                      'last_name'
                ))
                ->join(
                    array('flag' => 'groupware_contact_items_flags'),
                    '`flag`.`groupware_contact_items_id` = `items`.`id`' .
                    ' AND '.
                    $adapter->quoteInto('`flag`.`user_id`=?', $userId, 'INTEGER').
                    ' AND '.
                    '`flag`.`is_deleted`=0',
                    array()
                )
                ->joinLeft(
                    array('email_addresses' => 'groupware_contact_items_email'),
                    '`email_addresses`.`groupware_contact_items_id` = `items`.`id`',
                    array('email_address', 'is_standard')
                )
                ->where('`items`.`first_name` LIKE ?',  "%".$query."%")
                ->orWhere('`items`.`last_name` LIKE ?', "%".$query."%")
                ->orWhere('CONCAT(`items`.`first_name`, \' \', `items`.`last_name`) LIKE ?', "%".$query."%")
                ->orWhere('`email_addresses`.`email_address` LIKE ?', "%".$query."%");

        $rows = $adapter->fetchAll($select);

        if ($rows != false) {
            return $rows;
        }

        return array();
    }

// -------- interface Intrabuild_BeanContext_Decoratable

    public function getRepresentedEntity()
    {
        return 'Intrabuild_Modules_Groupware_Contact_Item';
    }

    public function getDecoratableMethods()
    {
        return array(
        );
    }

}