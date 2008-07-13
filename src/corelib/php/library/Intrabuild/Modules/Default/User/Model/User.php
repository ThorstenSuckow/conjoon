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
 * @see Intrabuild_BeanContext_Decoratable
 */
require_once 'Intrabuild/BeanContext/Decoratable.php'; 

/**
 * Table data gateway. Models the table <tt>users</tt>.
 *
 * @uses Zend_Db_Table
 * @package Intrabuild
 * @subpackage Model
 * @category Model
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */    
class Intrabuild_Modules_Default_User_Model_User
    extends Zend_Db_Table_Abstract implements Intrabuild_BeanContext_Decoratable {    
        
    /**
     * The name of the table in the underlying datastore this
     * class represents.
     * @var string
     */
    protected $_name = 'users';

    /**
     * The name of the column that denotes the primary key for this table
     * @var string
     */
    protected $_primary = 'id';

    /**
     * Returs the row that matches both the email address and the password.
     *
     * @param string $emailAddress 
     * @param string $password
     *
     * @return Zend_Db_Table_Row
     */
    public function getUserForEmailCredentials($emailAddress, $password)
    {
        $emailAddress = strtolower(trim((string)$emailAddress));
        $password     = trim((string)$password);
        
        if ($emailAddress == "" || $password == "") {
            return null;    
        }
        
        /**
         * @todo make sure email and username is a unique index
         */
        $select = $this->select()
                       ->from($this)
                       ->where('email_address = ?', $emailAddress)
                       ->where('password=?', $password);
        
        $row = $this->fetchRow($select);
        
        return $row;
    }

    /**
     * Returns the number of users that share the same email-address.
     * 
     * @param string $emailAddress
     *
     * @return integer
     */
    public function getEmailAddressCount($emailAddress)
    {
        $emailAddress = strtolower(trim((string)$emailAddress));
        
        if ($emailAddress == "") {
            return 0;    
        }   
        
        /**
         * @todo make sure comparison uses lowercase even in db field
         */
        $select = $this->select()
                  ->from($this, array(
                    'COUNT(id) as count_id'
                  ))
                  ->where('email_address = ?', $emailAddress);
        
        $row = $this->fetchRow($select);
            
        return ($row !== null) ? $row->count_id : 0;
    }

    /**
     * Looks up the row with the primary key $id, fetches the data and
     * creates a new instance of Intrabuild_User out of it or returns the data
     * as fetched from the table.
     *
     * @param integer $id
     *  
     *
     * @return Intrabuild_User or null if no data with the primary key $id exists.
     *
     * @throws Intrabuild_BeanContext_Exception The method will throw an exception
     * of the type Intrabuild_BeanContext_Exception if the BeanContext-object
     * Intrabuild_User could not be created automatically.
     */
    public function getUser($id)
    {
        $id = (int)$id;
        
        if ($id <= 0) {
            return null;    
        }
        
        $row = $this->fetchRow($this->select()->where('id=?', $id));

        return $row;
    }
    
// -------- interface Intrabuild_BeanContext_Decoratable 
    
    public function getRepresentedEntity()
    {
        return 'Intrabuild_Modules_Default_User';    
    }
    
    public function getDecoratableMethods()
    {
        return array(
            'getUser',
            'getUserForEmailCredentials'
        );
    }       

}