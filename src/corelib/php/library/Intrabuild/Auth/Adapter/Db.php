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
 * Zend_Auth_Adapter_Interface
 */
require_once 'Zend/Auth/Adapter/Interface.php';

/**
 * Zend_Auth_Adapter_Exception
 */
require_once 'Zend/Auth/Adapter/Exception.php';

/**
 * Zend_Auth_Result
 */
require_once 'Zend/Auth/Result.php';

/**
 * Adapter for checking for authentication credentials in a
 * database.
 *
 * @uses Zend_Auth_Adapter_Interface
 * @package Intrabuild
 * @subpackage Model
 * @category Model
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */    
class Intrabuild_Auth_Adapter_Db implements Zend_Auth_Adapter_Interface {
     
    private $email;
    
    private $password;
 
    /**
     * Constructor.
     *
     * @param string $email The email to lookup in the database.
     * @param string $password The password to lookup in the database.
     */
    public function __construct($email, $password)
    {
        $this->email     = $email;
        $this->password = $password;
    }
    
    /**
     * We assume that the controller set the default adapter
     * for all database operations, thus is available without futher specifying 
     * it .
     *
     * @return Zend_Auth_Result
     * 
     * @throws Zend_Auth_Adapter_Exception
     */
    public function authenticate()
    {
        $email    = $this->email;
        $password = $this->password;
        
        // return a general failure if either username or password 
        // equal to <code>null</code>
        if (trim($email) == null || trim($password) == null) {
            return new Zend_Auth_Result(Zend_Auth_Result::FAILURE, $email);  
        }
        
        require_once 'Intrabuild/Modules/Default/User/Model/User.php'; 
        $userTable = new Intrabuild_Modules_Default_User_Model_User();     
        
        // check here if the username exists
        $rowUsername = $userTable->fetchAll('email_address = "'.$email.'"');
         
        // rowset! check count()... if this is > 1, 1..n users share the same
        // username, which is a bad thing
        if ($rowUsername->count() > 1) {
            return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_IDENTITY_AMBIGUOUS, $email);      
        } else if ($rowUsername->count() == 0) {
             return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND, $email); 
        }
        
        // the username exists. check if username and password match.
        $rowCredentials = $userTable->fetchRow('email_address = "'.$email.'" AND
                                               password="'.md5($password).'"');
        
        // <code>null</code> means, that no user was found with the 
        // username/ password combination
        if ($rowCredentials === null) {
             return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID, $email); 
        }
        
        require_once 'Intrabuild/BeanContext/Decorator.php';
        
        $decorator = new Intrabuild_BeanContext_Decorator($userTable);
        
        $user = $decorator->getUserAsEntity($rowCredentials->id);
       
        // just to be sure...
        if ($user === null) {
             return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID, $email); 
        }        
        
        // anything else from here on matches. 
        return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $user); 
    }
 
}
