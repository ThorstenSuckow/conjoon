<?php
/**
 * intrabuild
 * (c) 2002-2008 siteartwork.de/MindPatterns
 * license@siteartwork.de
 *
 * $Author: T. Suckow $
 * $Id: Flag.php 2 2008-06-21 10:38:49Z T. Suckow $
 * $Date: 2008-06-21 12:38:49 +0200 (Sa, 21 Jun 2008) $
 * $Revision: 2 $
 * $LastChangedDate: 2008-06-21 12:38:49 +0200 (Sa, 21 Jun 2008) $
 * $LastChangedBy: T. Suckow $
 * $URL: file:///F:/svn_repository/intrabuild/trunk/src/corelib/php/library/Intrabuild/Modules/Groupware/Email/Item/Model/Flag.php $
 */

/**
 * Zend_Db_Table
 */
require_once 'Zend/Db/Table/Abstract.php';

/**
 * Table data gateway. Models the table <tt>groupware_feeds_items_flags</tt>.
 *
 * @uses Zend_Db_Table
 * @package Intrabuild_Groupware_Email
 * @subpackage Model
 * @category Model
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Intrabuild_Modules_Groupware_Feeds_Item_Model_Flag extends Zend_Db_Table_Abstract {

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