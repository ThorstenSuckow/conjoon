<?php
/**
 * conjoon
 * (c) 2002-2012 siteartwork.de/conjoon.org
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
 * @see Conjoon_Mail_Client_Message_Flag_ClientSeenFlag
 */
require_once 'Conjoon/Mail/Client/Message/Flag/ClientSeenFlag.php';

/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Mail_Client_Message_Flag_ClientSeenFlagTest extends PHPUnit_Framework_TestCase {

    protected $_input;

    protected function setUp()
    {
        $this->_input = array(
            'messageId' => "hklhkl",
            'clear'     => true
        );
    }

    /**
     * @expectedException Conjoon_Argument_Exception
     */
    public function testConstructFailFirst()
    {
        new Conjoon_Mail_Client_Message_Flag_ClientSeenFlag("", true);
    }

    /**
     * Ensures everything works as expected
     */
    public function testConstructOk()
    {
        $flag = new Conjoon_Mail_Client_Message_Flag_ClientSeenFlag(
            $this->_input['messageId'],
            $this->_input['clear']
        );

        $this->assertSame('\Seen', $flag->__toString());
        $this->assertSame($this->_input['messageId'], $flag->getMessageId());
        $this->assertSame($this->_input['clear'], $flag->isClear());

    }

}
