<?php
/**
 * conjoon
 * (c) 2007-2015 conjoon.org
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
 * @see Conjoon_Db_Table
 */
require_once 'Conjoon/Db/Table.php';

/**
 * @see Conjoon_BeanContext_Decoratable
 */
require_once 'Conjoon/BeanContext/Decoratable.php';

/**
 * Table data gateway. Models the table <tt>users</tt>.
 *
 * @uses Conjoon_Db_Table
 * @package Conjoon
 * @subpackage Model
 * @category Model
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Modules_Default_User_Model_User
    extends Conjoon_Db_Table implements Conjoon_BeanContext_Decoratable {

    /**
     * The name of the table in the underlying datastore this
     * class represents, without any prefix defined by Conjoon_Db_Table::setTablePrefix
     * @var string
     */
    protected $_name = 'users';

    /**
     * The name of the column that denotes the primary key for this table
     * @var string
     */
    protected $_primary = 'id';

    /**
     * Removes all session fragments for the specified user
     * related to auto login information from the user table.
     *
     * @param integer $id The id for the user to process
     */
    public function clearAutoLoginInformationForUserId($id) {

        $userId = (int) trim((string)$id);

        if ($userId <= 0) {
            return null;
        }

        $where = $this->getAdapter()->quoteInto('id = ?', $userId);

        $updData = array(
            'remember_me_token' => NULL
        );

        return $this->update($updData, $where);
    }

    /**
     * Returs the row that matches both the md5ed username and the token.
     *
     * @param string md5ed $userName
     * @param string $password
     *
     * @return Zend_Db_Table_Row
     */
    public function getUserForHashedUsernameAndRememberMeToken($userName, $token) {

        $userName = strtolower(trim((string)$userName));
        $token = trim((string)$token);

        if ($userName == "" || $token == "") {
            return null;
        }

        /**
         * @todo make sure username and password is a unique index
         */
        $select = $this->select()
            ->from($this)
            ->where('MD5(user_name) = ?', $userName)
            ->where('remember_me_token=?', $token);

        $row = $this->fetchRow($select);

        return $row;
    }

    /**
     * Returs the row that matches both the username and the password.
     *
     * @param string $userName
     * @param string $password
     *
     * @return Zend_Db_Table_Row
     */
    public function getUserForUserNameCredentials($userName, $password)
    {
        $userName = strtolower(trim((string)$userName));
        $password = trim((string)$password);

        if ($userName == "" || $password == "") {
            return null;
        }

        /**
         * @todo make sure username and password is a unique index
         */
        $select = $this->select()
                       ->from($this)
                       ->where('user_name = ?', $userName)
                       ->where('password=?', $password);

        $row = $this->fetchRow($select);

        return $row;
    }

    /**
     * Returns the number of users that share the same username,
     * in case the index on the user_name field is missing.
     *
     * @param string $userName
     * @param boolean $md5ed whether the username should is hashed
     *
     * @return integer
     */
    public function getUserNameCount($userName, $md5ed = false)
    {
        $userName = strtolower(trim((string)$userName));

        if ($userName == "") {
            return 0;
        }

        /**
         * @todo make sure comparison uses lowercase even in db field
         */
        $query = $md5ed === true
                 ? 'MD5(user_name) = ?'
                 : 'user_name = ?';
        $select = $this->select()
                  ->from($this, array(
                    'COUNT(id) as count_id'
                  ))
                  ->where($query, $userName);

        $row = $this->fetchRow($select);

        return ($row !== null) ? $row->count_id : 0;
    }

    /**
     * Looks up the row with the primary key $id, fetches the data and
     * creates a new instance of Conjoon_User out of it or returns the data
     * as fetched from the table.
     *
     * @param integer $id
     *
     *
     * @return Conjoon_User or null if no data with the primary key $id exists.
     *
     * @throws Conjoon_BeanContext_Exception The method will throw an exception
     * of the type Conjoon_BeanContext_Exception if the BeanContext-object
     * Conjoon_User could not be created automatically.
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

// -------- interface Conjoon_BeanContext_Decoratable

    public function getRepresentedEntity()
    {
        return 'Conjoon_Modules_Default_User';
    }

    public function getDecoratableMethods()
    {
        return array(
            'getUser',
            'getUserForUserNameCredentials',
            'getUserForHashedUsernameAndRememberMeToken'
        );
    }

}
