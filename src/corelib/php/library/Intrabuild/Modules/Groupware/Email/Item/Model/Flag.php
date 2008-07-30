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
 * Zend_Db_Table
 */
require_once 'Zend/Db/Table/Abstract.php';

/**
 * Table data gateway. Models the table <tt>groupware_email_items_flags</tt>.
 *
 * @uses Zend_Db_Table
 * @package Intrabuild_Groupware_Email
 * @subpackage Model
 * @category Model
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Intrabuild_Modules_Groupware_Email_Item_Model_Flag extends Zend_Db_Table_Abstract {

    /**
     * The name of the table in the underlying datastore this
     * class represents.
     * @var string
     */
    protected $_name = 'groupware_email_items_flags';

    /**
     * The name of the column that denotes the primary key for this table
     * @var string
     */
    protected $_primary = array(
        'groupware_email_items_id',
        'user_id'
    );

    /**
     * Marks a specified item for the specified user as either "read" or "unread"
     *
     * @return integer 1, if the data has been updated, otherwise 0
     */
    public function flagItemAsRead($groupwareEmailItemsId, $userId, $isRead)
    {
        $groupwareEmailItemsId = (int)$groupwareEmailItemsId;
        $userId                = (int)$userId;

        if ($groupwareEmailItemsId <= 0 || $userId <= 0) {
            return 0;
        }

        $data = array('is_read' => (bool)$isRead);
        $adapter = $this->getAdapter();
        return $this->update($data, array(
            $adapter->quoteInto('groupware_email_items_id = ?', $groupwareEmailItemsId, 'INTEGER'),
            $adapter->quoteInto('user_id = ?', $userId, 'INTEGER')
        ));
    }

    /**
     * Marks a specified item for the specified user as either "spam" or "no spam"
     *
     * @return integer 1, if the data has been updated, otherwise 0
     */
    public function flagItemAsSpam($groupwareEmailItemsId, $userId, $isSpam)
    {
        $groupwareEmailItemsId = (int)$groupwareEmailItemsId;
        $userId                = (int)$userId;

        if ($groupwareEmailItemsId <= 0 || $userId <= 0) {
            return 0;
        }

        $data = array('is_spam' => (bool)$isSpam);
        $adapter = $this->getAdapter();
        return $this->update($data, array(
            $adapter->quoteInto('groupware_email_items_id = ?', $groupwareEmailItemsId, 'INTEGER'),
            $adapter->quoteInto('user_id = ?', $userId, 'INTEGER')
        ));
    }


}