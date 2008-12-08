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
 * @see Conjoon_BeanContext_Decoratable
 */
require_once 'Conjoon/BeanContext/Decoratable.php';

/**
 * Table data gateway. Models the table <tt>groupware_feeds_accounts</tt>.
 *
 * @uses Zend_Db_Table
 * @package Conjoon_Groupware_Feeds
 * @subpackage Model
 * @category Model
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Conjoon_Modules_Groupware_Feeds_Account_Model_Account
   extends Zend_Db_Table_Abstract implements Conjoon_BeanContext_Decoratable{

    /**
     * The name of the table in the underlying datastore this
     * class represents.
     * @var string
     */
    protected $_name = 'groupware_feeds_accounts';

    /**
     * The name of the column that denotes the primary key for this table
     * @var string
     */
    protected $_primary = 'id';

    /**
     * Updates the account with the specified id with the passed values.
     *
     * @param integer $id The of the account to update
     * @param array $data an associative array with key/value pairs.
     *
     * @return boolean true if the account was updated, otherwise, false
     */
    public function updateAccount($id, $data)
    {
        $id = (int)$id;

        if ($id <= 0) {
            return false;
        }

        $affected = $this->update(
            $data, $this->getAdapter()->quoteInto('id = ?', $id, 'INTEGER')
        );

        return $affected !== 0;
    }

    /**
     * Adds a new account.
     *
     * @param array $data An associative array with the key/value pairs.
     *
     * @return integer The id of the newly added data
     */
    public function addAccount(Array $data)
    {
        return $this->insert($data);
    }

    /**
     * Removes the account with the specified id entirely.
     *
     * @return boolean true if the account was deleted, otherwise false
     */
    public function deleteAccount($id)
    {
        $id = (int)$id;

        if ($id <= 0) {
            return false;
        }

        $where    = $this->getAdapter()->quoteInto('id = ?', $id, 'INTEGER');
        $affected = $this->delete($where);

        if ($affected != 0) {
            require_once 'Conjoon/Modules/Groupware/Feeds/Item/Model/Item.php';
            $itemModel = new Conjoon_Modules_Groupware_Feeds_Item_Model_Item();
            $itemModel->deleteItemsForAccount($id);
        }

        return $affected !== 0;
    }

    /**
     * Returns all accounts which need to be updated with new feed items.
     * Based on the passed timestamp, only those accounts get returned where
     * the $time minus the last_update field of an account is greater than
     * the value of the update_field of the account.
     *
     * @param integer $userId The id of the user the accounts belong to
     * @param integer $time The timestamp that is used to measure if a new
     * update of a specific account is neccessary
     *
     * @return Zend_Db_Table_Rowset
     */
    public function getAccountsToUpdate($userId, $time)
    {
        $userId = (int)$userId;

        if ($userId <= 0) {
            return array();
        }

        $select = $this->select()
                  ->where('user_id=?', $userId)
                  ->where('(?-last_updated) > update_interval', $time);

        $rows = $this->fetchAll($select);

        return $rows;
    }

    /**
     * Deletes old feed items based on the configured "deleteInterval"-property.
     *
     * @param integer $userId The id of the user the accounts belong to
     */
    public function deleteOldFeedItems($userId)
    {
        $userId = (int)$userId;

        if ($userId <= 0) {
            return;
        }

        $time = time();

        $query = "DELETE
                    groupware_feeds_items
                  FROM
                    groupware_feeds_items,groupware_feeds_accounts
                  WHERE
                    (? - groupware_feeds_items.saved_timestamp ) > groupware_feeds_accounts.delete_interval
                   AND
                    groupware_feeds_items.groupware_feeds_accounts_id = groupware_feeds_accounts.id
                   AND
                    groupware_feeds_accounts.user_id=?";

        $db   = Zend_Db_Table::getDefaultAdapter();
        $stmt = $db->query($query, array($time, $userId));
        $stmt->execute();
    }

    /**
     * Returns all feeds accounts for the specified user-id.
     *
     * @param int $id The id of the user to get the email accounts for
     *
     * @return Zend_Db_Table_Rowset
     */
    public function getAccountsForUser($id)
    {
        $id = (int)$id;

        if ($id <= 0) {
           return array();
        }

        $rows = $this->fetchAll(
            $this->select()
                 ->where('user_id=?', $id)
                 ->where('is_deleted=?', false)
                 ->order('name DESC')
        );


        return $rows;
    }

    /**
     * Updates last_update field of one or more rows.
     *
     * @param mixed $id Either an integer with teh id of the row to update or
     * a numeric array with the ids of the rows to update
     * @param integer $timestamp The timestamp value for the last_updated field
     *
     * @return integer The number of rows updated
     */
    public function setLastUpdated($id, $timestamp)
    {
        if (!is_array($id)) {
            $id = array((int)$id);
        }
        $ids = array();
        for ($i = 0, $len = count($id); $i < $len; $i++) {
            $id[$i] = (int)$id[$i];
            if ($id[$i] > 0) {
                $ids[] = $id[$i];
            }
        }

        if (count($ids) == 0) {
            return 0;
        }

        $ids = implode(',', array_values($ids));

        return $this->update(
            array('last_updated' => $timestamp),
            'id IN ('.$ids.')'
        );
    }


    /**
     * Returns the account with the specified id.
     *
     * @param integer $id The id of the account to query.
     *
     * @return Zend_Db_Table_row
     */
    public function getAccount($id)
    {
        $id = (int)$id;

        if ($id <= 0) {
            return null;
        }

        $row = $this->fetchRow($this->select()->where('id=?', $id));

        return $row;
    }

// -------- interface Conjoon_BeanContext_Decoratable

    public function getRepresentedEntity()
    {
        return 'Conjoon_Modules_Groupware_Feeds_Account';
    }

    public function getDecoratableMethods()
    {
        return array(
            'getAccountsForUser',
            'getAccountsToUpdate',
            'getAccount'
        );
    }
}