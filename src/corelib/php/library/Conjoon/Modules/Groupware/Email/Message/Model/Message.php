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
require_once 'Zend/Db/Table.php';

/**
 * Conjoon_BeanContext_Decoratable
 */
require_once 'Conjoon/BeanContext/Decoratable.php';

/**
 *
 *
 *
 * @uses Zend_Db_Table
 * @package Conjoon_Groupware_Email
 * @subpackage Model
 * @category Model
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Conjoon_Modules_Groupware_Email_Message_Model_Message
    implements Conjoon_BeanContext_Decoratable {

    /**
     * Returns the message for the specified items id.
     */
    public function getEmailMessage($groupwareEmailItemsId, $userId)
    {
        $groupwareEmailItemsId = (int)$groupwareEmailItemsId;

        if ($groupwareEmailItemsId <= 0) {
            return 0;
        }

        $adapter = Zend_Db_Table::getDefaultAdapter();

         $select= $adapter->select()
                ->from(array('items' => 'groupware_email_items'),
                  array(
                      'id',
                      'cc',
                      'bcc',
                      'reply_to',
                      'to',
                      'subject',
                      'from',
                      'date',
                      'content_text_plain AS body',
                      '(1) AS is_plain_text',
                      'groupware_email_folders_id'
                ))
                ->joinLeft(
                    array('flag' => 'groupware_email_items_flags'),
                    '`flag`.`groupware_email_items_id` = `items`.`id`' .
                    ' AND '.
                    $adapter->quoteInto('`flag`.`user_id`=?', $userId, 'INTEGER'),
                    array('is_spam')
                )
                ->where('items.id=?', $groupwareEmailItemsId);

        $row = $adapter->fetchRow($select);

        return ($row != false) ? $row : null;

    }


// -------- interface Conjoon_BeanContext_Decoratable

    public function getRepresentedEntity()
    {
        return 'Conjoon_Modules_Groupware_Email_Message';
    }

    public function getDecoratableMethods()
    {
        return array(
            'getEmailMessage'
        );
    }

}