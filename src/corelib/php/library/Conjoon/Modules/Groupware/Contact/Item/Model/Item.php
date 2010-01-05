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
 * Conjoon_BeanContext_Decoratable
 */
require_once 'Conjoon/BeanContext/Decoratable.php';

/**
 * Table data gateway. Models the table <tt>groupware_contact_items</tt>.
 *
 *
 *
 * @uses Conjoon_Db_Table
 * @package Conjoon_Groupware_Email
 * @subpackage Model
 * @category Model
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Conjoon_Modules_Groupware_Contact_Item_Model_Item
    extends Conjoon_Db_Table implements Conjoon_BeanContext_Decoratable {


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
                ->from(array('items' => self::getTablePrefix() . 'groupware_contact_items'),
                  array(
                      'id',
                      'first_name',
                      'last_name'
                ))
                ->join(
                    array('flag' => self::getTablePrefix() . 'groupware_contact_items_flags'),
                    '`flag`.`groupware_contact_items_id` = `items`.`id`' .
                    ' AND '.
                    $adapter->quoteInto('`flag`.`user_id`=?', $userId, 'INTEGER').
                    ' AND '.
                    '`flag`.`is_deleted`=0',
                    array()
                )
                ->joinLeft(
                    array('email_addresses' => self::getTablePrefix() . 'groupware_contact_items_email'),
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

// -------- interface Conjoon_BeanContext_Decoratable

    public function getRepresentedEntity()
    {
        return 'Conjoon_Modules_Groupware_Contact_Item';
    }

    public function getDecoratableMethods()
    {
        return array(
        );
    }

}