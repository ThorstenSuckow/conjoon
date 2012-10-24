<?php
/**
 * conjoon
 * (c) 2002-2012 siteartwork.de/conjoon.org
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
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
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
     *
     * @return integer
     */
    public function getUserNameCount($userName)
    {
        $userName = strtolower(trim((string)$userName));

        if ($userName == "") {
            return 0;
        }

        /**
         * @todo make sure comparison uses lowercase even in db field
         */
        $select = $this->select()
                  ->from($this, array(
                    'COUNT(id) as count_id'
                  ))
                  ->where('user_name = ?', $userName);

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
            'getUserForUserNameCredentials'
        );
    }

}