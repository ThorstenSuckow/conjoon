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
 * Table data gateway. Models the table <tt>groupware_email_items_references</tt>.
 *
 * @uses Conjoon_Db_Table
 * @package Conjoon_Groupware_Email
 * @subpackage Model
 * @category Model
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Conjoon_Modules_Groupware_Email_Item_Model_References extends Conjoon_Db_Table {

    /**
     * The name of the table in the underlying datastore this
     * class represents.
     * @var string
     */
    protected $_name = 'groupware_email_items_references';

    /**
     * The name of the column that denotes the primary key for this table
     * @var string
     */
    protected $_primary = array(
        'groupware_email_items_id',
        'user_id'
    );

}