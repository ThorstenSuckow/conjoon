<?php
/**
 * intrabuild
 * (c) 2002-2008 siteartwork.de/MindPatterns
 * license@siteartwork.de
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
 * Table data gateway. Models the table <tt>groupware_feeds_items</tt>.
 *
 * @uses Zend_Db_Table
 * @package Intrabuild_Groupware_Feeds
 * @subpackage Model
 * @category Model
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */    
class Intrabuild_Modules_Groupware_Feeds_ItemModel extends Zend_Db_Table_Abstract {    

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
     * @param boolean $toArray if set to true, the rows will be
     * transformed to arrays, otherwise an instance of 
     * Intrabuild_Modules_Groupware_Feeds_Itemwill be returned
     *
     * @return mixed
     */
    public function getItem($id, $toArray = false)
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
        $dataTmp = $stmt->fetch(Zend_Db::FETCH_ASSOC);
        
        if (!is_array($dataTmp)) {
            return $toArray ? array() : null;   
        }
        
        if ($toArray === true) {
            return $dataTmp;    
        }
        
        require_once 'Intrabuild/BeanContext/Inspector.php';
        require_once 'Intrabuild/Util/Array.php';
        
        return Intrabuild_BeanContext_Inspector::create(
            'Intrabuild_Modules_Groupware_Feeds_Item',
            Intrabuild_Util_Array::camelizeKeys($dataTmp));
    }    
    
    /**
     * Returns all feeds accounts for the specified account-id.
     * Note, that the field 'account_id' won't be available in the returned 
     * array.
     *
     * @param int $id The id of the user to get the email accounts for
     * @param boolean $toArray if set to true, the rows will be
     * transformed to arrays, otherwise to instances data will be returned
     * as an array
     *
     * @return array
     */
    public function getItemsForAccount($id, $toArray = false)
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
        $dataTmp = $stmt->fetchAll();
        
        if ($toArray === true) {
            return $dataTmp;    
        }
        
        require_once 'Intrabuild/BeanContext/Inspector.php';
        require_once 'Intrabuild/Util/Array.php';
        
        $data = array();
        for ($i = 0, $len = count($dataTmp); $i < $len; $i++) {
            $data[] = Intrabuild_BeanContext_Inspector::create(
                          'Intrabuild_Modules_Groupware_Feeds_Item',
                          Intrabuild_Util_Array::camelizeKeys($dataTmp[$i])
                       );
        }
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
     * @return boolean true, if the item was not already in the db, otherwise false
     */
    public function addItemIfNotExists(Array $item, $accountId) 
    {
        $accountId = (int)$accountId;
        
        if ($accountId <= 0 || !isset($item['guid'])) {
            return false;    
        }
        
        $select = $this->select()
                  ->where('guid = ?', $item['guid'])
                  ->order('groupware_feeds_accounts_id', $accountId);
                  
        $row = $this->fetchRow($select);
        
        if ($row != null) {
            return false;    
        }
        
        $item['saved_timestamp']             = time();
        $item['groupware_feeds_accounts_id'] = $accountId;
        $id = $this->insert($item);
        
        if ($id > 0) {
            return $id;    
        }
        
        return -1;
    }

}