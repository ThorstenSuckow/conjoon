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
 * @package Conjoon
 * @subpackage Model
 * @category Model
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Conjoon_Auth_Adapter_Db implements Zend_Auth_Adapter_Interface {

    private $userName;

    private $password;

    /**
     * Constructor.
     *
     * @param string $userName The username to lookup in the database.
     * @param string $password The password to lookup in the database.
     */
    public function __construct($userName, $password)
    {
        $this->userName = $userName;
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
        $userName = $this->userName;
        $password = $this->password;

        // return a general failure if either username or password
        // equal to <code>null</code>
        if (trim($userName) == null || trim($password) == null) {
            return new Zend_Auth_Result(
                Zend_Auth_Result::FAILURE,
                $userName,
                array('Authentication failed. Invalid data.')
             );
        }

        require_once 'Conjoon/Modules/Default/User/Model/User.php';
        $userTable = new Conjoon_Modules_Default_User_Model_User();

        // check here if the username exists
        $count = $userTable->getUserNameCount($userName);

        // rowset! check count()... if this is > 1, 1..n users share the same
        // username, which is a bad thing
        if ($count > 1) {
            return new Zend_Auth_Result(
                Zend_Auth_Result::FAILURE_IDENTITY_AMBIGUOUS,
                $userName,
                array('More than one record matches the supplied identity.')
            );
        } else if ($count == 0) {
            return new Zend_Auth_Result(
                Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND,
                $userName,
                array('A record with the supplied identity could not be found.')
            );
        }

        require_once 'Conjoon/BeanContext/Decorator.php';
        $decorator = new Conjoon_BeanContext_Decorator($userTable);
        $user = $decorator->getUserForUserNameCredentialsAsEntity($userName, md5($password));

        // <code>null</code> means, that no user was found with the
        // username/ password combination
        if ($user === null) {
            return new Zend_Auth_Result(
                Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID,
                $userName,
                array('Supplied credential is invalid.')
            );
        }

        // anything else from here on matches.
        return new Zend_Auth_Result(
            Zend_Auth_Result::SUCCESS,
            $user,
            array('Authentication successful.')
        );
    }

}
