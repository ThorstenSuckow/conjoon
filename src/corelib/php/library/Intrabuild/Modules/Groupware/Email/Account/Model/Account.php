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
 * @see Zend_Db_Table 
 */
require_once 'Zend/Db/Table/Abstract.php';

/**
 * @see Intrabuild_BeanContext_Decoratable
 */
require_once 'Intrabuild/BeanContext/Decoratable.php'; 

/**
 * Table data gateway. Models the table <tt>groupware_email_accounts</tt>.
 *
 * @uses Zend_Db_Table
 * @package Intrabuild_Groupware_Email
 * @subpackage Model
 * @category Model
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */    
class Intrabuild_Modules_Groupware_Email_Account_Model_Account 
    extends Zend_Db_Table_Abstract implements Intrabuild_BeanContext_Decoratable{    

    /**
     * The name of the table in the underlying datastore this
     * class represents.
     * @var string
     */
    protected $_name = 'groupware_email_accounts';

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
        $affected = $this->update(array('is_deleted' => 1), $where);
        
        return $affected;
    }
    
    /**
     * Returns the account with the specified id for the specified user
     *
     * @param int $id The accountId the id of the account to query
     * @param int $userId The id of the user to whom the specified account belongs
     *
     * @return Zend_Db_Table_Rowset
     */
    public function getAccount($accountId, $userId)
    {
        $accountId = (int)$accountId;
        $userId    = (int)$userId;
        
        if ($accountId <= 0 || $userId <= 0) {
            return null;    
        }
        
        $row = $this->fetchRow(
            $this->select()
                ->where('id=?', $accountId)
                ->where('user_id=?', $userId)
                ->where('is_deleted=?', false)
        );
        
        return $row;
    }    
    
    /**
     * Returns all email accounts for the specified user-id.
     * Note, that the field 'user_id' won't be available in the returned 
     * array.
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
                ->order('is_standard DESC')
        );
        
        return $rows;
    }

// -------- interface Intrabuild_BeanContext_Decoratable       

    public function getRepresentedEntity()
    {
        return 'Intrabuild_Modules_Groupware_Email_Account';    
    }
    
    public function getDecoratableMethods()
    {
        return array(
            'getAccountsForUser',
            'getAccount'
        );
    }

}