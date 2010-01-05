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
* @see Zend_Mail_Storage_Imap
*/
require_once 'Zend/Mail/Storage/Imap.php';

/**
* @see Conjoon_Mail_Message
*/
require_once 'Conjoon/Mail/Message.php';


/**
 * Fix for http://framework.zend.com/issues/browse/ZF-3318
 *
 * This class uses custom implementation of message class until fix for above
 * bug gets released.
 */
class Conjoon_Mail_Storage_Imap extends Zend_Mail_Storage_Imap {

    /**
     * used message class, change it in an extened class to extend the returned message class
     * @var string
     */
    protected $_messageClass = 'Conjoon_Mail_Message';

}