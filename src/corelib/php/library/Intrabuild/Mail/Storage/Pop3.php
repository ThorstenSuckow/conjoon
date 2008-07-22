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
* @see Zend_Mail_Storage_Pop3
*/
require_once 'Zend/Mail/Storage/Pop3.php';

/**
* @see Intrabuild_Mail_Message
*/
require_once 'Intrabuild/Mail/Message.php';


/**
 * Fix for http://framework.zend.com/issues/browse/ZF-3318
 *
 * This class uses custom implementation of message class until fix for above
 * bug gets released.
 */
class Intrabuild_Mail_Storage_Pop3 extends Zend_Mail_Storage_Pop3 {

    /**
     * used message class, change it in an extened class to extend the returned message class
     * @var string
     */
    protected $_messageClass = 'Intrabuild_Mail_Message';


    /*
     * Get raw messagee
     *
     * @param  int $id   number of message
     * @return string raw message
     * @throws Zend_Mail_Protocol_Exception
     */
    public function getRawMessage($id)
    {
        $content = $this->_protocol->retrieve($id);
        return $content;
    }

}