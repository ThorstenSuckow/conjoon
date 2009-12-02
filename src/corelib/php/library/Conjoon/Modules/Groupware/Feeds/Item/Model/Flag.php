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
 * @see Conjoon_Db_Table
 */
require_once 'Conjoon/Db/Table.php';

/**
 * Table data gateway. Models the table <tt>groupware_feeds_items_flags</tt>.
 *
 * @uses Conjoon_Db_Table
 * @package Conjoon_Groupware_Email
 * @subpackage Model
 * @category Model
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Conjoon_Modules_Groupware_Feeds_Item_Model_Flag extends Conjoon_Db_Table {

    /**
     * The name of the table in the underlying datastore this
     * class represents.
     * @var string
     */
    protected $_name = 'groupware_feeds_items_flags';

    /**
     * The name of the column that denotes the primary key for this table
     * @var string
     */
    protected $_primary = array(
        'groupware_feeds_accounts_id',
        'guid'
    );

    /**
     * Adds a new guid/accountid pair to the table.
     *
     * @param integer $accountId
     * @param string $guid
     *
     * @return 1 if the row has been inserted, otherwise 0
     */
    public function addItem($accountId, $guid)
    {
        $accountId = (int)$accountId;

        if ($accountId <= 0 || $guid == null) {
            return 0;
        }

        $data = array(
            'groupware_feeds_accounts_id' => $accountId,
            'guid'                       => $guid
        );

        $id = $this->insert($data);

        return $id;
    }

    /**
     * Removes flasg for a specific account id.
     *
     * @param integer $accountId
     *
     * @return integer number of deleted rows
     */
    public function deleteForAccountId($accountId)
    {
        $accountId = (int)$accountId;

        if ($accountId <= 0) {
            return 0;
        }

        $where    = $this->getAdapter()->quoteInto('groupware_feeds_accounts_id = ?', $accountId, 'INTEGER');
        $affected = $this->delete($where);

        return $affected;
    }

    /**
     * Checks wether a feed item with the specified guid was already stored.
     *
     * @param integer $accountId
     * @param string $guid
     *
     * @return true if it exists, otherwise false
     */
    public function isItemPresent($accountId, $guid)
    {
        $accountId = (int)$accountId;

        if ($accountId <= 0 || $guid == null) {
            return false;
        }

        $rows = $this->find($accountId, $guid);

        if (count($rows) == 0) {
            return false;
        }

        return true;
    }
}