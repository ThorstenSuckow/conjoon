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
 * Table data gateway. Models the table <tt>groupware_feeds_items</tt>.
 *
 * @uses Zend_Db_Table
 * @package Conjoon_Groupware_Feeds
 * @subpackage Model
 * @category Model
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Conjoon_Modules_Groupware_Feeds_Item_Model_Item
   extends Zend_Db_Table_Abstract implements Conjoon_BeanContext_Decoratable{

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
            return $toArray ? array() : null;
        }
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                ->from('groupware_feeds_items', '*')
                ->join('groupware_feeds_accounts',
                    'groupware_feeds_items.groupware_feeds_accounts_id=groupware_feeds_accounts.id',
                     array('name'))
                ->where('groupware_feeds_items.id=?', $id);

        $stmt = $db->query($select);
        $data = $stmt->fetch(Zend_Db::FETCH_ASSOC);

        return $data;
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
            array('is_read' => (bool)$read),
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
                ->from('groupware_feeds_items', '*')
                ->join('groupware_feeds_accounts',
                    'groupware_feeds_items.groupware_feeds_accounts_id=groupware_feeds_accounts.id',
                     array('name'))
                ->where('groupware_feeds_items.groupware_feeds_accounts_id=?', $id)
                ->order('groupware_feeds_items.saved_timestamp DESC');

        $stmt = $db->query($select);
        $data = $stmt->fetchAll();

        return $data;
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

        require_once 'Conjoon/Modules/Groupware/Feeds/Item/Model/Flag.php';
        $flagModel = new Conjoon_Modules_Groupware_Feeds_Item_Model_Flag();

        if ($flagModel->isItemPresent($accountId, $item['guid'])) {
            return 0;
        }

        $item['groupware_feeds_accounts_id'] = $accountId;
        $id = $this->insert($item);

        if ($id > 0) {

            $flagModel->addItem($accountId, $item['guid']);

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