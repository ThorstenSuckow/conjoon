<?php
/**
 * conjoon
 * (c) 2002-2010 siteartwork.de/conjoon.org
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
 * Table data gateway. Models the table <tt>groupware_email_imap_mapping</tt>.
 *
 * @uses Conjoon_Db_Table
 * @package Conjoon_Groupware_Email
 * @subpackage Model
 * @category Model
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Conjoon_Modules_Groupware_Email_ImapMapping_Model_ImapMapping
    extends Conjoon_Db_Table implements Conjoon_BeanContext_Decoratable{

    /**
     * The name of the table in the underlying datastore this
     * class represents.
     * @var string
     */
    protected $_name = 'groupware_email_imap_mapping';

    /**
     * The name of the column that denotes the primary key for this table
     * @var string
     */
    protected $_primary = 'id';

    /**
     * Returns all mappings found in the table for the specified
     * user id.
     *
     * @param int $userId The id of the user to get the mappings for
     *
     * @return Zend_Db_Table_Rowset
     */
    public function getImapMappingsForUser($userId)
    {
        $userId = (int)$userId;

        if ($userId <= 0) {
            return array();
        }

        $adapter = $this->getDefaultAdapter();

        $select= $adapter->select()
                 ->from(array('mappings' => self::getTablePrefix() . 'groupware_email_imap_mapping'))
                 ->join(
                     array('accounts' => self::getTablePrefix() . 'groupware_email_accounts'),
                     '`accounts`.`id` = `mappings`.`groupware_email_accounts_id`',
                     array('name')
                 )
                 ->join(
                     array('folders' => self::getTablePrefix() . 'groupware_email_folders_accounts'),
                     '`accounts`.`id` = `folders`.`groupware_email_accounts_id`',
                     array(
                        'groupware_email_folders_id AS rootFolderId'
                     )
                 )
                 ->where('accounts.user_id=?',    $userId)
                 ->where('accounts.is_deleted=?', 0);

        $rows = $adapter->fetchAll($select);

        if ($rows != false) {
            return $rows;
        }

        return $rows;
    }

// -------- interface Conjoon_BeanContext_Decoratable

    public function getRepresentedEntity()
    {
        return 'Conjoon_Modules_Groupware_Email_ImapMapping';
    }

    public function getDecoratableMethods()
    {
        return array(
            'getImapMappingsForUser'
        );
    }

}