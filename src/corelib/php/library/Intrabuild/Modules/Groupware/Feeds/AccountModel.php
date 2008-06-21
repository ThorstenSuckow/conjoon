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
 * Table data gateway. Models the table <tt>groupware_feeds_accounts</tt>.
 *
 * @uses Zend_Db_Table
 * @package Intrabuild_Groupware_Feeds
 * @subpackage Model
 * @category Model
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */    
class Intrabuild_Modules_Groupware_Feeds_AccountModel extends Zend_Db_Table_Abstract {    

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
     * Updates the is_deleted field in the table to "1" to indicate that this account 
     * was deleted and should not be used anymore.
     *
     * @return integer The number of accounts deleted. 
     */
    public function deleteAccount($id)
    {
        $id = (int)$id;
        
        if ($id <= 0) {
            return 0;    
        }    
        
        $where    = $this->getAdapter()->quoteInto('id = ?', $id, 'INTEGER');
        $affected = $this->delete($where);
        
        if ($affected != 0) {
            require_once 'Intrabuild/Modules/Groupware/Feeds/ItemModel.php';     
            $itemModel = new Intrabuild_Modules_Groupware_Feeds_ItemModel();
            $itemModel->deleteItemsForAccount($id);
        }
        
        return $affected;
    }
    
    /**
     * Returns an array with all accounts which need to be updated with new feed items.
     * 
     * @param integer $userId The id of the user the accounts belong to
     * @param bool $toArray Wether to return the accounts as instances of 
     * Intrabuild_Modules_Groupware_Feeds_Account or plain array
     *
     * @return array
     */
    public function getAccountsToUpdate($userId, $toArray = false)
    {
        $userId = (int)$userId;
        
        if ($userId <= 0) {
            return array();    
        }
        
        $time = time();
        $select = $this->select()
                  ->where('user_id=?', $userId)
                  ->where('(?-last_updated) > update_interval', $time);
        
        $rows = $this->fetchAll($select);
        
        $dataTmp = $rows->toArray();
        
        if ($toArray === true) {
            return $dataTmp;    
        }
        
        require_once 'Intrabuild/BeanContext/Inspector.php';
        require_once 'Intrabuild/Util/Array.php';
        
        $data = array();
        for ($i = 0, $len = count($dataTmp); $i < $len; $i++) {
            $data[] = Intrabuild_BeanContext_Inspector::create(
                          'Intrabuild_Modules_Groupware_Feeds_Account',
                          Intrabuild_Util_Array::camelizeKeys($dataTmp[$i])
                       );
        }
        return $data; 
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
     * Note, that the field 'user_id' won't be available in the returned 
     * array.
     *
     * @param int $id The id of the user to get the email accounts for
     * @param boolean $toArray if set to true, the rows will be
     * transformed to arrays, otherwise to instances data will be returned
     * as an array, otherwise to instances of Intrabuild_Groupware_Email_Account
     *
     * @return array
     */
    public function getAccountsForUser($id, $toArray = false)
    {
        $id = (int)$id;
        
        if ($id <= 0) {
           return $toArray ? array() : null;    
        }
        
        $rows = $this->fetchAll(
            $this->select()
                ->where('user_id=?', $id)
                ->where('is_deleted=?', false)
                ->order('name DESC')
         );
        
        $dataTmp = $rows->toArray();
        
        if ($toArray === true) {
            return $dataTmp;    
        }
        
        require_once 'Intrabuild/BeanContext/Inspector.php';
        require_once 'Intrabuild/Util/Array.php';
        
        $data = array();
        for ($i = 0, $len = count($dataTmp); $i < $len; $i++) {
            $data[] = Intrabuild_BeanContext_Inspector::create(
                          'Intrabuild_Modules_Groupware_Feeds_Account',
                          Intrabuild_Util_Array::camelizeKeys($dataTmp[$i])
                       );
        }
        return $data;
    }

    public function getAccount($id, $toArray = false)
    {
        $id = (int)$id;
        
        if ($id <= 0) {
            return $toArray ? array() : null;    
        }
        
        $row = $this->fetchRow($this->select()->where('id=?', $id));
        
        if ($row == null) {
            return $toArray ? array() : null;        
        }
        
        $dataTmp = $row->toArray();
        
        if ($toArray === true) {
            return $dataTmp;    
        }
        
        require_once 'Intrabuild/BeanContext/Inspector.php';
        require_once 'Intrabuild/Util/Array.php';
        
        return Intrabuild_BeanContext_Inspector::create(
            'Intrabuild_Modules_Groupware_Feeds_Account',
            Intrabuild_Util_Array::camelizeKeys($dataTmp)
        );
    }

}