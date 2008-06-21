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
            'getUser'
        );
    }       

}