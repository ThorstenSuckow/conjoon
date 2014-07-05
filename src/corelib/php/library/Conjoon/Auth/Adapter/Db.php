<?php
/**
 * conjoon
 * (c) 2007-2014 conjoon.org
 * licensing@conjoon.org
 *
 * conjoon
 * Copyright (C) 2014 Thorsten Suckow-Homberg/conjoon.org
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
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
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Auth_Adapter_Db implements Zend_Auth_Adapter_Interface {

    private $userName;

    private $password;

    private $rememberMe = false;

    private $cookieName = null;

    private $cookieRememberMe = null;

    /**
     * Constructor.
     *
     * @param array $config a configuration array with the following key/value
     *                      combinations:
     * - username string $userName The username to lookup in the database.
     * - password string  $password The password to lookup in the database.
     * - remember_me boolean $rememberMe Whether auto login should be used the next
     * - cookie -> name string md5 hash of the username
     * - cookie -> remember_me_token string
     *
     * if any of the "cookie" keys can befound, those values will be used instead
     * of username/passwrod
     *
     * @throws InvalidArgumentException in case values where missing
     */
    public function __construct(array $config)
    {
        if (isset($config['cookie'])) {
            if (!is_array($config['cookie']) || (!isset($config['cookie']['name']) ||
                !isset($config['cookie']['remember_me_token']))) {
                throw new InvalidArgumentException("missing values for cookies!");
            }

            $this->cookieName       = $config['cookie']['name'];
            $this->cookieRememberMe = $config['cookie']['remember_me_token'];

            return;
        }

        if (!isset($config['username']) || !isset($config['password'])) {
            throw new InvalidArgumentException("missing values for username/password!");
        }

        $this->userName   = $config['username'];
        $this->password   = $config['password'];

        $this->rememberMe = isset($config['remember_me'])
                            ? (bool) $config['remember_me']
                            : false;
    }

    /**
     * This emthod will authenticate a user against a database table.
     * It will also generate a login token that is generated during the
     * login process and will be stored in the db table. The token should then
     * be written into the session - before dispatching any request, it is advised
     * to check whether the session stored token still equals to the token stored
     * in the database - if not, it is likely that another login occured with
     * this user credentials.
     * We assume that the controller set the default adapter
     * for all database operations, thus is available without futher specifying
     * it.
     *
     * @return Zend_Auth_Result
     *
     * @throws Zend_Auth_Adapter_Exception
     */
    public function authenticate()
    {
        $cookieName      = $this->cookieName;
        $rememberMeToken = $this->cookieRememberMe;

        $userName   = $this->userName;
        $password   = $this->password;
        $rememberMe = $this->rememberMe;

        if ($cookieName == "" && $rememberMeToken == "" &&
            (trim($userName) == null || trim($password) == null)) {

            // return a general failure if either username or password
            // equal to <code>null</code>
            return new Zend_Auth_Result(
                Zend_Auth_Result::FAILURE,
                $userName,
                array('Authentication failed. Invalid data.')
             );
        }

        /**
         * @see Conjoon_Modules_Default_User_Model_User
         */
        require_once 'Conjoon/Modules/Default/User/Model/User.php';
        $userTable = new Conjoon_Modules_Default_User_Model_User();

        // check here if the username exists
        if ($cookieName != "" && $rememberMeToken != "") {
            $count = $userTable->getUserNameCount($cookieName, true);
        } else {
            $count = $userTable->getUserNameCount($userName);
        }


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

        /**
         * @see Conjoon_BeanContext_Decorator
         */
        require_once 'Conjoon/BeanContext/Decorator.php';
        $decorator = new Conjoon_BeanContext_Decorator($userTable);

        if ($cookieName != "" && $rememberMeToken != "") {
            $user = $decorator->getUserForHashedUsernameAndRememberMeTokenAsEntity(
                $cookieName, $rememberMeToken
            );
        } else {
            $user = $decorator->getUserForUserNameCredentialsAsEntity($userName, md5($password));
        }
        // <code>null</code> means, that no user was found with the
        // username/ password combination
        if ($user === null) {
            return new Zend_Auth_Result(
                Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID,
                $userName,
                array('Supplied credential is invalid.')
            );
        }

        // we have a match - generate a token and store it into the database
        $token = md5(uniqid(rand(), true));

        $where = $userTable->getAdapter()->quoteInto('id = ?', $user->getId());
        $time = time();

        $updData = array(
            'auth_token' => $token,
            'last_login' => $time
        );

        if ($cookieName == "" && $rememberMeToken == "") {
            $rememberMeToken = $rememberMe === true
                               ? md5(uniqid(rand(), true))
                               : null;
            $updData['remember_me_token'] = $rememberMeToken;
            $user->setRememberMeToken($rememberMeToken);
        }

        $userTable->update($updData, $where);

        if (!$user->getLastLogin()) {
            $user->setLastLogin(-1);
        }

        $user->setAuthToken($token);

        // anything else from here on matches.
        return new Zend_Auth_Result(
            Zend_Auth_Result::SUCCESS,
            $user,
            array('Authentication successful.')
        );
    }

}
