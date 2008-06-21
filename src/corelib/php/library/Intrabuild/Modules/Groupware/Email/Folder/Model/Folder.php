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
 * Table data gateway. Models the table <tt>groupware_email_folders</tt>.
 *
 * @uses Zend_Db_Table
 * @package Intrabuild_Groupware_Email
 * @subpackage Model
 * @category Model
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */    
class Intrabuild_Modules_Groupware_Email_Folder_Model_Folder 
    extends Zend_Db_Table_Abstract implements Intrabuild_BeanContext_Decoratable{    

    /**
     * The name of the table in the underlying datastore this
     * class represents.
     * @var string
     */
    protected $_name = 'groupware_email_folders';

    /**
     * The name of the column that denotes the primary key for this table
     * @var string
     */
    protected $_primary = 'id';
    
    /**
     * Moves a folder to a new node.
     *
     * @param integer $id The id of the folder to move
     * @param integer $newParentId The id of the folder to which the folder specified
     * with $id gets moved to
     *
     * @return integer The number of rows updated, i.e. 0 if no update happened,
     * otherwise 1
     */
    public function moveFolder($id, $parentId)
    {
        $id       = (int)$id;
        $parentId = (int)$parentId;
        
        if ($id <= 0 || $parentId <= 0) {
            return 0;    
        }
        
        $data = array('parent_id' => $parentId);
        $adapter = $this->getAdapter();
        return $this->update($data, array(
            $adapter->quoteInto('id = ?', $id, 'INTEGER')
        ));        
    }
    
    /**
     * Renames a folder.
     *
     * @param integer $id The id of the folder to rename
     * @param string $name The new name of the folder
     *
     * @return integer The number of rows updated, i.e. 0 if no update happened,
     * otherwise 1
     */
    public function renameFolder($id, $name)
    {
        $id = (int)$id;
        
        if ($id <= 0) {
            return 0;    
        }
        
        $adapter = $this->getAdapter();
        $data = array('name' => $name);
        return $this->update($data, array(
            $adapter->quoteInto('id = ?', $id, 'INTEGER')
        ));        
    }    
    
    /**
     * Appends a new folder to the folder with the specified $parentId. 
     * If the folder is connected to an email-account, the new folder
     * will inherit the account-id from it's parent-folder. The new folder
     * will also inherit the meta-info-value of the parent folder
     *
     * @param integer $parentId
     * @param string $name    
     * @param integer $userId
     */
    public function addFolder($parentId, $name, $userId)
    {
        if ((int)$parentId <= 0 || (int)$userId <= 0 || $name == "") {
            return -1;    
        }  
        
        $parentRow = $this->fetchRow($parentId)->toArray();
        
        if (!is_array($parentRow) || $parentRow['meta_info'] == null) {
            return -1;    
        }
        
        $data = array(
            'name'             => $name,
            'is_child_allowed' => 1,
            'is_locked'        => 0,
            'type'             => 'folder',
            'parent_id'        => $parentId,
            'meta_info'        => $parentRow['meta_info']
        );        
        
        $id = (int)$this->insert($data);
        
        if ($id <= 0) {
            return -1;    
        }
        
        // check if the parent folder is related to one ore more accounts
        require_once 'Intrabuild/Modules/Groupware/Email/Folder/Model/FoldersAccounts.php';
        $foldersAccountsModel = new Intrabuild_Modules_Groupware_Email_Folder_Model_FoldersAccounts();
        
        $select = $foldersAccountsModel
                  ->select()
                  ->where('groupware_email_folders_id = ?', $parentId);
        $folderAccounts = $foldersAccountsModel->fetchAll($select)->toArray();
        
        for ($i = 0, $len = count($folderAccounts); $i < $len; $i++) {
            $foldersAccountsModel->insert(array(
                'groupware_email_folders_id'  => $id,
                'groupware_email_accounts_id' => $folderAccounts[$i]['groupware_email_accounts_id']
            ));
        }
        
        return $id;
        
    } 
    
    /**
     * Returns the base query for reading out folders.
     */
    public static function getFolderBaseQuery()
    {
        $adapter = self::getDefaultAdapter();
        return $adapter->select()
               ->from(array('folders' => 'groupware_email_folders'), array(
                 'id',
                 'is_child_allowed',
                 'is_locked',
                 'type'
               ))
               ->joinLeft(array('childtable' => 'groupware_email_folders'),
                'childtable.parent_id=folders.id', 
                 array(
                  'child_count' => 'COUNT(DISTINCT childtable.id)'
               ))
               ->joinLeft(array(
                'items' => 'groupware_email_items'),
                'folders.id=items.groupware_email_folders_id',
                 array()
               )
               ->where('folders.is_deleted = ?', 0)
               ->group('folders.id')
               ->order('folders.id ASC'); 
    }
    
    /**
     * Retruns all root folders for the user. A root folder is either a
     * folder that was created when an account was created, or a folder
     * that was put into public that contains only folders of the type
     * 'folder'.
     * A root folder that is connectec with multiple accounts usually is of the
     * type 'accounts_root', a root-folder that was created for a specific account 
     * is usually of the type 'root'. If the folder is of the type 'root' (i.e.
     * was created for a specific account), the name of the folder will
     * default to the name of the account it is connected with.
     * 
     * @return array
     */
    protected function getRootFolders($userId)
    {
        $adapter = self::getDefaultAdapter();
        $select  = self::getFolderBaseQuery()
                ->join(
                      array('pendingfolder' => 'groupware_email_folders'),
                      'pendingfolder.id=folders.id',
                      array('pending_count' => '(0)')
                   )
                   ->joinLeft(
                       array('foldersaccounts' => 'groupware_email_folders_accounts'),
                       'foldersaccounts.groupware_email_folders_id=folders.id',
                       array('name' => 
                             'IF(folders.type="root",'.
                             'accounts.name,'.
                             'folders.name'.
                             ') AS name')
                   )
                   ->join(
                       array('accounts' => 'groupware_email_accounts'),
                       'accounts.id=foldersaccounts.groupware_email_accounts_id',
                       array()
                   )
                   ->where('folders.type=?', 'root')
                   ->orWhere('folders.type=?', 'accounts_root');
        
        $rows = $adapter->fetchAll($select);    
        
        return $rows;
    } 
    
    /**
     * Returns the child-folders for the specified id and userId.
     * If the parentId equals to 0, all root folders for the user 
     * will be read out. 
     *
     * @return array
     */
    public function getFolders($parentId, $userId)
    { 
        $userId   = (int)$userId;
        $parentId = (int)$parentId;
         
        if ($userId <= 0 || $parentId < 0) {
            return array();    
        }
        
        if ($parentId == 0) {
            return $this->getRootFolders($userId);    
        }
        
        $adapter = $this->getAdapter();
        $select  = self::getFolderBaseQuery()
                   ->join(array(
                    'namefolder' => 'groupware_email_folders'
                   ),
                   'namefolder.id=folders.id',
                   array('name')
                   )
                   ->joinLeft(array(
                    'flag' => 'groupware_email_items_flags'),
                    'items.id = flag.groupware_email_items_id'.
                    ' AND '.
                    'flag.is_read=0'.
                    ' AND ' .
                    $adapter->quoteInto('flag.user_id=?', $userId, 'INTEGER'),
                    array('pending_count' => 'COUNT(DISTINCT flag.groupware_email_items_id)')
                   )
                   ->where('folders.parent_id = ?', $parentId);
        
     
        $rows = $adapter->fetchAll($select);    
        
        return $rows;
    }
    
// -------- interface Intrabuild_BeanContext_Decoratable 
    
    public function getRepresentedEntity()
    {
        return 'Intrabuild_Modules_Groupware_Email_Folder';    
    }
    
    public function getDecoratableMethods()
    {
        return array(
            'getFolders'
        );
    }    
}