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
 * @see Conjoon_Db_Table
 */
require_once 'Conjoon/Db/Table.php';

/**
 * @see Conjoon_BeanContext_Decoratable
 */
require_once 'Conjoon/BeanContext/Decoratable.php';

/**
 * Table data gateway. Models the table <tt>service_twitter_accounts</tt>.
 *
 * @uses Conjoon_Db_Table
 * @package Conjoon_Modules_Service_Twitter
 * @subpackage Model
 * @category Model
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Modules_Service_Twitter_Account_Model_Account
   extends Conjoon_Db_Table implements Conjoon_BeanContext_Decoratable{

    const UPDATE_INTERVAL_DEFAULT = 60000;

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
     * name, oauth_token, oauth_token_secret, updateInterval, twitter_id
     * @param integer $userId id of the ser for whom the data will be added
     *
     * @return the id of the added data, or 0 if an error occurred
     */
    public function addAccountForUserId($data, $userId)
    {
        if (!isset($data['name'])) {
            return 0;
        }

        if (!isset($data['oauth_token'])) {
            return 0;
        }

        if (!isset($data['twitter_id'])) {
            return 0;
        }

        if (!isset($data['oauth_token_secret'])) {
            return 0;
        }

        if (!isset($data['update_interval'])) {
            $data['update_interval'] = self::UPDATE_INTERVAL_DEFAULT;
        }

        $userId = (int)$userId;

        if ($userId <= 0) {
            return 0;
        }

        $whiteList = array(
            'name',
            'oauth_token',
            'oauth_token_secret',
            'twitter_id',
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

        // we will first look up a twitter account that has
        // already the twitterid, the name and the userid.
        // if found, we will update it with the data.
        $row = $this->fetchRow(
            $this->select()
                ->where('user_id=?',    $addData['user_id'])
                ->where('twitter_id=?', $addData['twitter_id'])
                ->where('name=?',       $addData['name'])
        );

        if ($row && ($row instanceof Zend_Db_Table_Row)) {
            $where = $this->getAdapter()->quoteInto('id = ?', $row->id, 'INTEGER');
            $affected = $this->update($addData, $where);
            return (int)$row->id;
        }

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