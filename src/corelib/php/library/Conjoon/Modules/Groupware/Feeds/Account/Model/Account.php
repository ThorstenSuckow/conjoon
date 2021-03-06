<?php
/**
 * conjoon
 * (c) 2007-2015 conjoon.org
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
 * @see Conjoon_BeanContext_Decoratable
 */
require_once 'Conjoon/BeanContext/Decoratable.php';

/**
 * Table data gateway. Models the table <tt>groupware_feeds_accounts</tt>.
 *
 * @uses Conjoon_Db_Table
 * @package Conjoon_Groupware_Feeds
 * @subpackage Model
 * @category Model
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Modules_Groupware_Feeds_Account_Model_Account
   extends Conjoon_Db_Table implements Conjoon_BeanContext_Decoratable{

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
     * @param integer $userId The id of the user for which the account
     * gets added
     *
     * @return integer The id of the newly added data
     */
    public function addAccount(Array $data, $userId)
    {
        $userId = (int)$userId;

        if ($userId <= 0) {
            return 0;
        }

        $data['user_id'] = $userId;

        return $this->insert($data);
    }

    /**
     * Removes the account with the specified id entirely.
     *
     * @param integer $id
     * @param boolean $removeFeedItems Whether or not to remove corresponding
     * feed items. Defaults to true.
     *
     * @return boolean true if the account was deleted, otherwise false
     */
    public function deleteAccount($id, $removeFeedItems = true)
    {
        $id = (int)$id;

        if ($id <= 0) {
            return false;
        }

        $where    = $this->getAdapter()->quoteInto('id = ?', $id, 'INTEGER');
        $affected = $this->delete($where);

        if ($affected != 0 && $removeFeedItems === true) {
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