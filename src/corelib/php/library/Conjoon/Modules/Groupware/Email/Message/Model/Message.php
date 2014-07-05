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
 * Conjoon_BeanContext_Decoratable
 */
require_once 'Conjoon/BeanContext/Decoratable.php';

/**
 *
 *
 *
 * @package Conjoon_Groupware_Email
 * @subpackage Model
 * @category Model
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
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

        $adapter = Conjoon_Db_Table::getDefaultAdapter();

        $select= $adapter->select()
                ->from(array('items' => Conjoon_Db_Table::getTablePrefix() . 'groupware_email_items'),
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
                ->join(
                    array('folders_users' => Conjoon_Db_Table::getTablePrefix() . 'groupware_email_folders_users'),
                    '`folders_users`.`groupware_email_folders_id` = `items`.`groupware_email_folders_id` '
                    .' AND '
                    .$adapter->quoteInto('`folders_users`.`users_id`=?', $userId, 'INTEGER')
                    .' AND '
                    .$adapter->quoteInto('`folders_users`.`relationship`=?', 'owner', 'STRING'),
                    array()
                )
                ->joinLeft(
                    array('flag' => Conjoon_Db_Table::getTablePrefix() . 'groupware_email_items_flags'),
                    '`flag`.`groupware_email_items_id` = `items`.`id`' .
                    ' AND '.
                    $adapter->quoteInto('`flag`.`user_id`=?', $userId, 'INTEGER'),
                    array('is_spam')
                )
                ->where('items.id=?', $groupwareEmailItemsId);

        $row = $adapter->fetchRow($select);

        if (!$row) {
            return null;
        }

        /**
         * @see Conjoon_Modules_Groupware_Email_Folder_Model_Folder
         */
        require_once 'Conjoon/Modules/Groupware/Email/Folder/Model/Folder.php';

        $folderModel = new Conjoon_Modules_Groupware_Email_Folder_Model_Folder;

        // unique id. Underscore needed since this wil automatically get
        // camelized later on
        $row['u_id'] = $row['id'];
        $row['path'] = $folderModel->getPathForFolderId($row['groupware_email_folders_id']);

        return $row;
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
