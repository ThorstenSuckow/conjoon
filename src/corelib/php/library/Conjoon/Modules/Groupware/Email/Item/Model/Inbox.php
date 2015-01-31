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
 * Conjoon_BeanContext_Decoratable
 */
require_once 'Conjoon/BeanContext/Decoratable.php';

/**
 * Table data gateway. Models the table <tt>groupware_email_items_inbox</tt>.
 *
 * @uses Conjoon_Db_Table
 * @package Conjoon_Groupware_Email
 * @subpackage Model
 * @category Model
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Modules_Groupware_Email_Item_Model_Inbox
    extends Conjoon_Db_Table implements Conjoon_BeanContext_Decoratable {

    const HASH       = 'hash';
    const UID        = 'uid';
    const MESSAGE_ID = 'message_id';

    /**
     * The name of the table in the underlying datastore this
     * class represents.
     * @var string
     */
    protected $_name = 'groupware_email_items_inbox';

    /**
     * The name of the column that denotes the primary key for this table
     * @var string
     */
    protected $_primary = 'groupware_email_items_id';

    /**
     * Returns the message id from the specified item.
     * Returns empty string if the item was not found or if no message-id was
     * explicit set for this item.
     *
     * @param integer $itemId
     *
     * @return string
     */
    public function getMessageIdForItem($itemId)
    {
        $itemId = (int)$itemId;

        if ($itemId <= 0) {
            return "";
        }

        $row = $this->fetchRow(
            $this->select()->from($this, array('message_id'))
                 ->where('groupware_email_items_id = ?', $itemId)
        );

        if (!$row || $row->message_id == "") {
            return "";
        }

        return $row->message_id;
    }

    /**
     * A helper method for computing a unique hash for a message.
     * If the implementation of the algorithm changes, the inbox-table's
     * column "hash" has to be changed accordingly.
     *
     * @param string $rawHeader
     * @param string $rawBody
     *
     * @return string
     */
    public static function computeMessageHash(&$rawHeader, &$rawBody)
    {
        return md5($rawHeader . $rawBody);
    }

    /**
     * Returns the rows found in the database, matching the account-id and the
     * submitted $uids. Only those rows will be returned, where the field "uid"
     * matches the value in the corresponding index of $uid.
     *
     * @param array $uids A list of uid to look up
     * @param integer $accountId The id of the account for which the presence of
     * the uids should be checked.
     *
     * @return array
     */
    public function getMatchingUids(Array $uids, $accountId)
    {
        $accountId = (int)$accountId;

        if ($accountId <= 0 || count($uids) == 0) {
            return array();
        }

        $adapter = $this->getAdapter();

        $uidParts = array();
        for ($i = 0, $len = count($uids); $i < $len; $i++) {
            $uidParts[] = $adapter->quote($uids[$i], 'STRING');
        }

        $uidString = implode(',', $uidParts);

        $select = $adapter->select()->from(
                    array('items' => self::getTablePrefix() . 'groupware_email_items'),
                    array()
                  )
                  ->join(
                    array('foldersaccounts' => self::getTablePrefix() . 'groupware_email_folders_accounts'),
                    $adapter->quoteInto('foldersaccounts.groupware_email_accounts_id = ?', $accountId, 'INTEGER'),
                    array()
                  )
                  ->join(
                    array('folders' => self::getTablePrefix() . 'groupware_email_folders'),
                    'folders.id = foldersaccounts.groupware_email_folders_id',
                    array()
                  )
                  ->join(
                    array('inbox' => self::getTablePrefix() . 'groupware_email_items_inbox'),
                    'inbox.uid IN ('.$uidString.') ' .
                    ' AND ' .
                    'inbox.groupware_email_items_id = items.id',
                    array('uid')
                  )
                  ->where('items.groupware_email_folders_id=folders.id');

        $rows = $adapter->fetchAll($select);

        if ($rows == false) {
            return array();
        }

        return $rows;
    }

    /**
     * Checks if there is an entry for the specified hash/uid/message_id
     * in the inbox-table.
     * A uid/message-id/hash will only be treated as a duplicate, if the
     * specific value already exists for an email that was alreatdy downloaded
     * with the specific $accountId.
     *
     * @param string $uid The uid as generated from the server's uidl
     * support, or the message_id as transported by the message-header
     * @param integer $accountId The id of the account for which the uid
     * should be looked up
     *
     * @return boolean true if the data was found, otherwise false.
     */
    public function isUniqueKeyPresent($uid, $accountId, $type)
    {
        switch ($type) {
            case (self::UID):
                $queryPortion = 'inbox.uid = ?';
            break;

            case (self::HASH):
                $queryPortion = 'inbox.hash = ?';
            break;

            case (self::MESSAGE_ID):
                $queryPortion = 'inbox.message_id = ?';
            break;

            default:
                throw new InvalidArgumentException("Invalid type: '".$type."'");
            return;
        }

        $adapter = $this->getAdapter();

        $select = $adapter->select()->from(
                    array('items' => self::getTablePrefix() . 'groupware_email_items'),
                    array('id')
                  )
                  ->join(
                    array('foldersaccounts' => self::getTablePrefix() . 'groupware_email_folders_accounts'),
                    $adapter->quoteInto('foldersaccounts.groupware_email_accounts_id = ?', $accountId, 'INTEGER'),
                    array()
                  )
                  ->join(
                    array('folders' => self::getTablePrefix() . 'groupware_email_folders'),
                    'folders.id = foldersaccounts.groupware_email_folders_id',
                    array()
                  )
                  ->join(
                    array('inbox' => self::getTablePrefix() . 'groupware_email_items_inbox'),
                    $adapter->quoteInto($queryPortion, $uid, 'STRING') .
                    ' AND ' .
                    'inbox.groupware_email_items_id = items.id',
                    array()
                  )
                  ->where('items.groupware_email_folders_id=folders.id');

        $row = $adapter->fetchRow($select);

        if ($row == false) {
            return false;
        }

        return true;
    }

    /**
     * Returns the total number of emails which fetched_timestamp is
     * greater than or equal to minDate
     *
     * @param interger $userId
     * @param $integer $minDate
     *
     * @return integer The total number of items fetched since minDate,
     * or 0 if there where no items
     */
    public function getLatestItemCount($userId, $minDate)
    {
        if ((int)$userId <= 0 || (int)$minDate < 0) {
            return 0;
        }

        // fetch the requested range of email items
        $adapter = self::getDefaultAdapter();
        $select = $adapter->select()
                  ->from(
                      array('items' => self::getTablePrefix() . 'groupware_email_items'),
                      array('COUNT(items.id) as count_id')
                  )
                  ->join(
                    array('accounts' => self::getTablePrefix() . 'groupware_email_accounts'),
                    $adapter->quoteInto('`accounts`.`user_id` = ?', $userId, 'INTEGER') .
                    ' AND '.
                    '`accounts`.`is_deleted` = 0',
                    array()
                  )
                  ->join(
                    array('foldersaccounts' => self::getTablePrefix() . 'groupware_email_folders_accounts'),
                    'foldersaccounts.groupware_email_accounts_id=accounts.id',
                    array()
                  )
                  ->join(
                    array('inbox' => self::getTablePrefix() . 'groupware_email_items_inbox'),
                    '`inbox`.`groupware_email_items_id`=`items`.`id`'.
                    ' AND '.
                    $adapter->quoteInto('`inbox`.`fetched_timestamp` >= ?', $minDate, 'INTEGER') .
                    ' AND '.
                    'items.groupware_email_folders_id=foldersaccounts.groupware_email_folders_id',
                    array()
                  );

        $row = $adapter->fetchRow($select);
        if ($row == false) {
            return 0;
        }
        return $row['count_id'];
    }

    /**
     * Fetches all email items for the specified user where fetched_timestamp
     * is greater than or equal to $minDate. This query will respect
     * all accounts of the user and all folders that are of the meta-type
     * 'inbox'.
     *
     *
     * @param integer $userId The id of the user
     * @param integer $minDate A timestamp
     * @param array $sortInfo An array with sortInfo.
     *
     * @return array
     */
    public function getLatestEmailItemsFor($userId, $minDate, Array $sortInfo)
    {
        if ((int)$userId <= 0 || (int)$minDate < 0) {
            return array();
        }

        // fetch the requested range of email items
        $adapter = self::getDefaultAdapter();

        require_once 'Conjoon/Modules/Groupware/Email/Item/Model/Item.php';

        $itemModel = new Conjoon_Modules_Groupware_Email_Item_Model_Item;

        $select = Conjoon_Modules_Groupware_Email_Item_Model_Item::getItemBaseQuery(
            $userId,
            $sortInfo,
            array(
                'name' => array('inbox' => self::getTablePrefix() . 'groupware_email_items_inbox'),
                'cols' => array()
            )
        );
        $select = $select
                  ->join(
                    array('accounts' => self::getTablePrefix() . 'groupware_email_accounts'),
                    $adapter->quoteInto('`accounts`.`user_id` = ?', $userId, 'INTEGER') .
                    ' AND '.
                    '`accounts`.`is_deleted` = 0',
                    array()
                  )
                  ->join(
                    array('foldersaccounts' => self::getTablePrefix() . 'groupware_email_folders_accounts'),
                    'foldersaccounts.groupware_email_accounts_id=accounts.id',
                    array()
                  )
                  ->where(
                    'items.groupware_email_folders_id=foldersaccounts.groupware_email_folders_id'.
                    ' AND ' .
                    '`inbox`.`groupware_email_items_id`=`items`.`id`'.
                    ' AND '.
                    $adapter->quoteInto('`inbox`.`fetched_timestamp` >= ?', $minDate, 'INTEGER')
                  );

        $rows = $adapter->fetchAll($select);

        if ($rows != false) {
            return $itemModel->applyPathToEmailItems($rows);
        }

        return array();
    }

    /**
     * Adds the specified data to the inbox table. Takes care of sending
     * large data as a stream into the table.
     *
     * @param array $data
     *
     * @return bool
     *
     * @throws Conjoon_Exception
     */
    public function addInboxData(Array $data)
    {
        $db = self::getDefaultAdapter();

        /**
         * @see Zend_Db_Adapter_Pdo_Mysql
         */
        require_once 'Zend/Db/Adapter/Pdo/Mysql.php';

        if (!($db instanceof Zend_Db_Adapter_Pdo_Mysql)) {
            /**
             * @see Conjoon_Exception
             */
            require_once 'Conjoon/Exception.php';

            throw new Conjoon_Exception(
                "Cannot add inbox data - adapter not of type "
                ."Zend_Db_Adapter_Pdo_Mysql, but ".get_class($db)
            );
        }

        $statement = $db->prepare(
            "INSERT INTO `".self::getTablePrefix() . "groupware_email_items_inbox`
              (
              `groupware_email_items_id`,
              `raw_header`,
              `raw_body`,
              `hash`,
              `message_id`,
              `uid`,
              `fetched_timestamp`
              )
              VALUES
              (
                :groupware_email_items_id,
                :raw_header,
                :raw_body,
                :hash,
                :message_id,
                :uid,
                :fetched_timestamp
            )"
        );

        $statement->bindParam(
            ':groupware_email_items_id',
            $data['groupware_email_items_id'], PDO::PARAM_INT
        );
        $statement->bindParam(':raw_header',
            $data['raw_header'], PDO::PARAM_LOB);
        $statement->bindParam(':raw_body',
            $data['raw_body'],     PDO::PARAM_LOB);
        $statement->bindParam(':hash', $data['hash'], PDO::PARAM_STR);
        $statement->bindParam(
            ':message_id', $data['message_id'], PDO::PARAM_STR
        );
        $statement->bindParam(':uid', $data['uid'], PDO::PARAM_STR);
        $statement->bindParam(
            ':fetched_timestamp', $data['fetched_timestamp'], PDO::PARAM_INT
        );

        return (bool)$statement->execute();
    }

// -------- interface Conjoon_BeanContext_Decoratable

    public function getRepresentedEntity()
    {
        return 'Conjoon_Modules_Groupware_Email_Item';
    }

    public function getDecoratableMethods()
    {
        return array(
            'getLatestEmailItemsFor'
        );
    }

}
