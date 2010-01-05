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
 * Table data gateway. Models the table <tt>groupware_email_items_flags</tt>.
 *
 * @uses Conjoon_Db_Table
 * @package Conjoon_Groupware_Email
 * @subpackage Model
 * @category Model
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Conjoon_Modules_Groupware_Email_Item_Model_Flag extends Conjoon_Db_Table {

    /**
     * The name of the table in the underlying datastore this
     * class represents.
     * @var string
     */
    protected $_name = 'groupware_email_items_flags';

    /**
     * The name of the column that denotes the primary key for this table
     * @var string
     */
    protected $_primary = array(
        'groupware_email_items_id',
        'user_id'
    );


    /**
     * Returns an aray keyed by the passed ids and value set to either tru or false
     * if the ccorresponding item has an entry with is_deleted= 0.
     *
     * @param integer|array $id
     *
     * @return array
     */
    public function areItemsFlaggedAsDeleted($id)
    {
        $tmpIds = (array)$id;
        $ids    = array();

        for ($i = 0, $len = count($tmpIds); $i < $len; $i++) {
            $id = (int)$tmpIds[$i];
            if ($id > 0) {
                $ids[] = $id;
            }
        }

        if (count($ids) == 0) {
            return array();
        }

        $select = $this->select()->from($this, array(
                    'groupware_email_items_id'
                  ))
                  ->where('groupware_email_items_id IN (' . implode(',', $ids) . ')')
                  ->where('is_deleted=0')
                  ->group('groupware_email_items_id');

        $rows = $this->fetchAll($select);
        // rows contains ids which are not deleted!
        $returnArray = array_fill_keys($ids, true);

        foreach ($rows as $row) {
            $returnArray[$row->groupware_email_items_id] = false;
        }

        return $returnArray;

    }


    /**
     * Marks a specified item for the specified user as either "deleted"
     *
     * @param integer|array
     *
     * @return integer the number of rows updated
     */
    public function flagItemsAsDeleted($groupwareEmailItemsId, $userId)
    {
        $tmpIds = (array)$groupwareEmailItemsId;
        $ids    = array();

        for ($i = 0, $len = count($tmpIds); $i < $len; $i++) {
            $id = (int)$tmpIds[$i];
            if ($id > 0) {
                $ids[] = $id;
            }
        }

        $userId = (int)$userId;

        if (count($ids) == 0 || $userId <= 0) {
            return 0;
        }

        $data = array('is_deleted' => 1);
        $adapter = $this->getAdapter();
        return $this->update($data, array(
            'groupware_email_items_id IN (' . implode(',', $ids) . ')',
            $adapter->quoteInto('user_id = ?', $userId, 'INTEGER')
        ));
    }


    /**
     * Marks a specified item for the specified user as either "read" or "unread"
     *
     * @return integer 1, if the data has been updated, otherwise 0
     */
    public function flagItemAsRead($groupwareEmailItemsId, $userId, $isRead)
    {
        $groupwareEmailItemsId = (int)$groupwareEmailItemsId;
        $userId                = (int)$userId;

        if ($groupwareEmailItemsId <= 0 || $userId <= 0) {
            return 0;
        }

        $data = array('is_read' => (bool)$isRead);
        $adapter = $this->getAdapter();
        return $this->update($data, array(
            $adapter->quoteInto('groupware_email_items_id = ?', $groupwareEmailItemsId, 'INTEGER'),
            $adapter->quoteInto('user_id = ?', $userId, 'INTEGER')
        ));
    }

    /**
     * Marks a specified item for the specified user as either "spam" or "no spam"
     *
     * @return integer 1, if the data has been updated, otherwise 0
     */
    public function flagItemAsSpam($groupwareEmailItemsId, $userId, $isSpam)
    {
        $groupwareEmailItemsId = (int)$groupwareEmailItemsId;
        $userId                = (int)$userId;

        if ($groupwareEmailItemsId <= 0 || $userId <= 0) {
            return 0;
        }

        $data = array('is_spam' => (bool)$isSpam);
        $adapter = $this->getAdapter();
        return $this->update($data, array(
            $adapter->quoteInto('groupware_email_items_id = ?', $groupwareEmailItemsId, 'INTEGER'),
            $adapter->quoteInto('user_id = ?', $userId, 'INTEGER')
        ));
    }


}