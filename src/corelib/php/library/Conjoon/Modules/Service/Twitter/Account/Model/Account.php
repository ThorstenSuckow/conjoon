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
 * Zend_Db_Table
 */
require_once 'Zend/Db/Table/Abstract.php';

/**
 * @see Conjoon_BeanContext_Decoratable
 */
require_once 'Conjoon/BeanContext/Decoratable.php';

/**
 * Table data gateway. Models the table <tt>service_twitter_accounts</tt>.
 *
 * @uses Zend_Db_Table
 * @package Conjoon_Modules_Service_Twitter
 * @subpackage Model
 * @category Model
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Conjoon_Modules_Service_Twitter_Account_Model_Account
   extends Zend_Db_Table_Abstract implements Conjoon_BeanContext_Decoratable{

    /**
     * The name of the table in the underlying datastore this
     * class represents.
     * @var string
     */
    protected $_name = 'service_twitter_accounts';

    /**
     * The name of the column that denotes the primary key for this table
     * @var string
     */
    protected $_primary = 'id';


    /**
     * Returns all twitter accounts for the specified user-id.
     *
     * @param int $id The id of the user to get the email accounts for
     *
     * @return Zend_Db_Table_Rowset
     */
    public function getAccountsForUser($id)
    {
        $id = (int)$id;

        if ($id <= 0) {
           return null;
        }

        $rows = $this->fetchAll(
            $this->select()
                 ->where('user_id=?', $id)
                 ->order('name DESC')
        );


        return $rows;
    }

    /**
     * Returns the twitter account for the specified account-id.
     *
     * @param int $id The id of the account to retrieve
     *
     * @return Zend_Db_Table_Rowset
     */
    public function getAccount($id)
    {
        $id = (int)$id;

        if ($id <= 0) {
            return null;
        }

        $row = $this->fetchRow($this->select()->where('id=?', $id));

        return $row;
    }

    /**
     * Adss a Twitter account to the db.
     *
     * @param Array $data An assoc array with the following key/value pairs:
     * name, password, updateInterval
     * @param integer $userId id of the ser for whom the data will be added
     *
     * @return the id of the added data, or 0 if an error occurred
     */
    public function addAccountForUserId($data, $userId)
    {
        if (!isset($data['update_interval'])) {
            return 0;
        }

        if (!isset($data['name'])) {
            return 0;
        }

        if (!isset($data['password'])) {
            return 0;
        }

        $userId = (int)$userId;

        if ($userId <= 0) {
            return 0;
        }

        $whiteList = array(
            'name',
            'password',
            'update_interval'
        );

        $addData = array();

        foreach ($data as $key => $value) {
            if (in_array($key, $whiteList)) {
                $addData[$key] = $value;
            }
        }

        if (empty($addData)) {
            return 0;
        }

        $addData['user_id'] = $userId;

        $id = $this->insert($addData);

        if ((int)$id == 0) {
            return 0;
        }

        return $id;
    }

    /**
     * Removes the account with the specified id.
     *
     * @param integer $accountId The id of the account to remove
     *
     * @return boolean false if the accoutn was not removed, otherwise true
     */
    public function deleteAccountForId($accountId)
    {
        $accountId = (int)$accountId;

        if ($accountId <= 0) {
            return false;
        }

        $where = $this->getAdapter()->quoteInto(
            'id = ?', $accountId, 'INTEGER'
        );
        $affected = $this->delete($where);

        return $affected !== 0;
    }

    /**
     * Updates the account with the specified id with the data found in $data.
     *
     * @param array $data An associative array with key/value pairs which
     * represents the data to update.
     * @param integer $accountId The id of the account to update with the given
     * data
     *
     * @return boolean false if the account was not updated, otherwise true
     */
    public function updateAccountForId(Array $data, $accountId)
    {
        $accountId = (int)$accountId;

        if ($accountId <= 0) {
            return false;
        }

        $updateData = array();

        $whiteList = array(
            'name',
            'password',
            'update_interval'
        );

        foreach ($data as $key => $value) {
            if (in_array($key, $whiteList)) {
                $updateData[$key] = $value;
            }
        }

        if (empty($updateData)) {
            return false;
        }

        $where = $this->getAdapter()->quoteInto('id = ?', $accountId, 'INTEGER');
        $affected = $this->update($updateData, $where);

        return $affected !== 0;
    }


// -------- interface Conjoon_BeanContext_Decoratable

    public function getRepresentedEntity()
    {
        return 'Conjoon_Modules_Service_Twitter_Account';
    }

    public function getDecoratableMethods()
    {
        return array(
            'getAccountsForUser',
            'getAccount'
        );
    }
}