<?php
/**
 * intrabuild
 * (c) 2002-2008 siteartwork.de/MindPatterns
 * license@siteartwork.de
 *
 * $Author: T. Suckow $
 * $Id: Inbox.php 78 2008-08-22 20:23:26Z T. Suckow $
 * $Date: 2008-08-22 22:23:26 +0200 (Fr, 22 Aug 2008) $
 * $Revision: 78 $
 * $LastChangedDate: 2008-08-22 22:23:26 +0200 (Fr, 22 Aug 2008) $
 * $LastChangedBy: T. Suckow $
 * $URL: file:///F:/svn_repository/intrabuild/trunk/src/corelib/php/library/Intrabuild/Modules/Groupware/Email/Item/Model/Inbox.php $
 */

/**
 * Zend_Db_Table
 */
require_once 'Zend/Db/Table/Abstract.php';


/**
 * Table data gateway. Models the table <tt>groupware_email_items_outbox</tt>.
 *
 * @uses Zend_Db_Table
 * @package Intrabuild_Groupware_Email
 * @subpackage Model
 * @category Model
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Intrabuild_Modules_Groupware_Email_Item_Model_Outbox extends Zend_Db_Table_Abstract {


    /**
     * The name of the table in the underlying datastore this
     * class represents.
     * @var string
     */
    protected $_name = 'groupware_email_items_outbox';

    /**
     * The name of the column that denotes the primary key for this table
     * @var string
     */
    protected $_primary = 'groupware_email_items_id';



}