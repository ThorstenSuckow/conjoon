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


namespace Conjoon\Mail\Client\Message\Flag;

/**
 * @see Conjoon_Mail_Client_Message_Flag_ClientSeenFlag
 */
require_once 'Conjoon/Mail/Client/Message/Flag/SeenFlag.php';

/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class SeenFlagTest extends \PHPUnit_Framework_TestCase {

    protected $_input;

    protected function setUp()
    {
        $this->_input = array(
            'uId' => "hklhkl",
            'clear'     => true
        );
    }

    /**
     * @expectedException \Conjoon_Argument_Exception
     */
    public function testConstructFailFirst()
    {
        new SeenFlag("", true);
    }

    /**
     * Ensures everything works as expected
     */
    public function testConstructOk()
    {
        $flag = new SeenFlag(
            $this->_input['uId'],
            $this->_input['clear']
        );

        $this->assertSame('\Seen', $flag->__toString());
        $this->assertSame($this->_input['uId'], $flag->getUId());
        $this->assertSame($this->_input['clear'], $flag->isClear());

    }

}
