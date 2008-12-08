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
 * @see Zend_Db_Table
 */
require_once 'Zend/Db/Table/Abstract.php';

/**
 * Table data gateway. Models the table <tt>groupware_email_folders_accounts</tt>.
 *
 * @uses Zend_Db_Table
 * @package Intrabuild_Groupware_Email
 * @subpackage Model
 * @category Model
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Intrabuild_Modules_Groupware_Email_Folder_Model_FoldersAccounts extends Zend_Db_Table_Abstract {

    /**
     * The name of the table in the underlying datastore this
     * class represents.
     * @var string
     */
    protected $_name = 'groupware_email_folders_accounts';

    /**
     * The name of the column that denotes the primary key for this table
     * @var string
     */
    protected $_primary = array(
        'groupware_email_folders_id',
        'groupware_email_accounts_id'
    );

    /**
     * Deletes all data for the folder with the specified id.
     *
     * @param integer $id
     *
     * @return integer 0 if no data was deleted, otherwise the number of deleted
     * data
     */
    public function deleteForFolder($id)
    {
        $id = (int)$id;
        if ($id <= 0) {
            return 0;
        }

        $where    = $this->getAdapter()->quoteInto('groupware_email_folders_id = ?', $id, 'INTEGER');
        $affected = $this->delete($where);

        return $affected;
    }


}