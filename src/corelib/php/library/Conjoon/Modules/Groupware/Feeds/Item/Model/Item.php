<?php
/**
 * conjoon
 * (c) 2007-2014 conjoon.org
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
 * Table data gateway. Models the table <tt>groupware_feeds_items</tt>.
 *
 * @uses Conjoon_Db_Table
 * @package Conjoon_Groupware_Feeds
 * @subpackage Model
 * @category Model
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Modules_Groupware_Feeds_Item_Model_Item
   extends Conjoon_Db_Table implements Conjoon_BeanContext_Decoratable{

    /**
     * The name of the table in the underlying datastore this
     * class represents.
     * @var string
     */
    protected $_name = 'groupware_feeds_items';

    /**
     * The name of the column that denotes the primary key for this table
     * @var string
     */
    protected $_primary = 'id';

    /**
     * Returns a feed item by the specified id
     *
     * @param int $id The id of the item to query
     *
     * @return mixed
     */
    public function getItem($id)
    {
        $id = (int)$id;

        if ($id <= 0) {
            return array();
        }
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(self::getTablePrefix() . 'groupware_feeds_items', '*')
                ->join(self::getTablePrefix() . 'groupware_feeds_accounts',
                    self::getTablePrefix() . 'groupware_feeds_items.groupware_feeds_accounts_id='
                    .self::getTablePrefix() . 'groupware_feeds_accounts.id',
                     array('name'))
                ->where(self::getTablePrefix() . 'groupware_feeds_items.id=?', $id);

        $stmt = $db->query($select);
        $data = $stmt->fetch(Zend_Db::FETCH_ASSOC);

        return $data;
    }

    /**
     * Returns the account ids for the specified feed ids.
     *
     * @param array $feedIds The ids of the feed items to return the
     * corresponding accountids for.
     *
     * @return array
     */
    public function getAccountIdsForFeedIds(Array $feedIds)
    {
        /**
         * @see Conjoon_Filter_PositiveArrayValues
         */
        require_once 'Conjoon/Filter/PositiveArrayValues.php';

        $filter = new Conjoon_Filter_PositiveArrayValues();

        $feedIds = $filter->filter($feedIds);

        if (empty($feedIds)) {
            return array();
        }

        $idList = implode(',', $feedIds);

        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(self::getTablePrefix() . 'groupware_feeds_items', array('groupware_feeds_accounts_id'))
                ->where(self::getTablePrefix() . 'groupware_feeds_items.id IN ('.$idList.')')
                ->group('groupware_feeds_accounts_id');

        $stmt = $db->query($select);
        $data = $stmt->fetchAll();

        $res = array();
        for ($i = 0, $len = count($data); $i < $len; $i++) {
            $res[] = $data[$i]['groupware_feeds_accounts_id'];
        }

        return $res;
    }

    /**
     * Marks a single or more feed item(s) either read or unread.
     *
     * @param mixed $id The id of a single feed item or a numeric array with the
     * ids of the feed items to update
     * @param boolean $read true for marking the item(s) as read, otherwise false
     * for setting the item(s) as unread
     *
     * @return integer The total number of rows updated
     *
     */
    public function setItemRead($id, $read)
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
            array('is_read' => (int)((bool)$read)),
            'id IN ('.$ids.')'
        );
    }

    /**
     * Returns all feeds accounts for the specified account-id.
     *
     * @param int $id The id of the user to get the email accounts for
     *
     * @return Zend_Db_Table_Rowset
     */
    public function getItemsForAccount($id)
    {
        $id = (int)$id;

        if ($id <= 0) {
            return $toArray ? array() : null;
        }
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(self::getTablePrefix() . 'groupware_feeds_items', '*')
                ->join(self::getTablePrefix() . 'groupware_feeds_accounts',
                    self::getTablePrefix() . 'groupware_feeds_items.groupware_feeds_accounts_id='
                    .self::getTablePrefix() . 'groupware_feeds_accounts.id',
                     array('name'))
                ->where(self::getTablePrefix() . 'groupware_feeds_items.groupware_feeds_accounts_id=?', $id)
                ->order(self::getTablePrefix() . 'groupware_feeds_items.saved_timestamp DESC');

        $stmt = $db->query($select);
        $data = $stmt->fetchAll();

        return $data;
    }

    /**
     * Returns a list of feed items that can be deleted based on the accounts
     * configured deleteInterval.
     *
     * @param integer $userId The id of the user for which deletable feed items
     * should be returned.
     *
     * @return Array
     */
    public function getFeedItemIdsToDelete($userId)
    {
        $userId = (int)$userId;

        if ($userId <= 0) {
            return array();
        }

        $time = time();

        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from(self::getTablePrefix() . 'groupware_feeds_items', array('id'))
                ->join(
                    self::getTablePrefix() . 'groupware_feeds_accounts',
                    self::getTablePrefix() . 'groupware_feeds_items.groupware_feeds_accounts_id='
                    .self::getTablePrefix() . 'groupware_feeds_accounts.id',
                    array()
                )
                ->where(self::getTablePrefix() . 'groupware_feeds_accounts.user_id=?', $userId)
                ->where('(? - '.self::getTablePrefix() . 'groupware_feeds_items.saved_timestamp ) > '
                . self::getTablePrefix() . 'groupware_feeds_accounts.delete_interval', $time);

        $stmt = $db->query($select);
        $data = $stmt->fetchAll();

        $res = array();
        for ($i = 0, $len = count($data); $i < $len; $i++) {
            $res[] = $data[$i]['id'];
        }

        return $res;
    }

    /**
     * Deletes the feed items for the specified ids.
     *
     * @param Array $feedIds
     *
     * @return integer The actual number of records deleted.
     */
    public function deleteFeedItemsForIds(Array $feedIds)
    {
        /**
         * @see Conjoon_Filter_PositiveArrayValues
         */
        require_once 'Conjoon/Filter/PositiveArrayValues.php';

        $filter = new Conjoon_Filter_PositiveArrayValues();

        $feedIds = $filter->filter($feedIds);

        if (empty($feedIds)) {
            return;
        }

        $idList = implode(',', $feedIds);

        $affected = $this->delete('id IN ('.$idList.')');

        return $affected;
    }

    /**
     * Deletes old feed items based on the configured "deleteInterval"-property
     * of the corresponding feed accounts.
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
                    ".self::getTablePrefix() . "groupware_feeds_items," . self::getTablePrefix() . "groupware_feeds_accounts
                  WHERE
                    (? - " . self::getTablePrefix() . "groupware_feeds_items.saved_timestamp ) > "
                    . self::getTablePrefix() . "groupware_feeds_accounts.delete_interval
                   AND
                    " . self::getTablePrefix() . "groupware_feeds_items.groupware_feeds_accounts_id = "
                    . self::getTablePrefix() . "groupware_feeds_accounts.id
                   AND
                    " . self::getTablePrefix() . "groupware_feeds_accounts.user_id=?";

        $db   = Zend_Db_Table::getDefaultAdapter();
        $stmt = $db->query($query, array($time, $userId));
        $stmt->execute();
    }

    /**
     * Deltes items belonging to a specific account.
     *
     * @return integer The number of accounts deleted.
     */
    public function deleteItemsForAccount($id)
    {
        $id = (int)$id;

        if ($id <= 0) {
            return 0;
        }

        $where    = $this->getAdapter()->quoteInto('groupware_feeds_accounts_id = ?', $id, 'INTEGER');
        $affected = $this->delete($where);

        /**
         * @see Conjoon_Modules_Groupware_Feeds_Item_Model_Flag
         */
        require_once 'Conjoon/Modules/Groupware/Feeds/Item/Model/Flag.php';
        $flagModel = new Conjoon_Modules_Groupware_Feeds_Item_Model_Flag();

        $flagModel->deleteForAccountId($id);

        return $affected;
    }

    /**
     * Adds a feed item to the users feed collection if it not already exists.
     *
     * @param $item array The item with it's data to insert.
     * @param $accountId The id of the account this item will belong to
     *
     * @return integer 0 if the item was not added, otherwise the primary key of
     * the newly added item
     */
    public function addItemIfNotExists(Array $item, $accountId)
    {
        $accountId = (int)$accountId;

        if ($accountId <= 0 || !isset($item['guid'])) {
            return 0;
        }

        /**
         * @see Conjoon_Modules_Groupware_Feeds_Item_Model_Flag
         */
        require_once 'Conjoon/Modules/Groupware/Feeds/Item/Model/Flag.php';
        $flagModel = new Conjoon_Modules_Groupware_Feeds_Item_Model_Flag();

        if ($flagModel->isItemPresent($accountId, md5($item['guid']))) {
            return 0;
        }

        $item['groupware_feeds_accounts_id'] = $accountId;
        $id = $this->insert($item);

        if ($id > 0) {

            $flagModel->addItem($accountId, md5($item['guid']));

            return $id;
        }

        return 0;
    }

   // -------- interface Conjoon_BeanContext_Decoratable

    public function getRepresentedEntity()
    {
        return 'Conjoon_Modules_Groupware_Feeds_Item';
    }

    public function getDecoratableMethods()
    {
        return array(
            'getItem',
            'getItemsForAccount'
        );
    }

}