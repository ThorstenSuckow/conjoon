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
 * @see Conjoon\Mail\Client\Message\Flag\NotJunkFlag
 */
require_once 'Conjoon/Mail/Client/Message/Flag/NotJunkFlag.php';

/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class NotJunkFlagTest extends \PHPUnit_Framework_TestCase {

    protected $_input;

    protected function setUp()
    {
        $this->_input = array(
            'messageId' => "hklhkl",
            'clear'     => true
        );
    }

    /**
     * @expectedException \Conjoon_Argument_Exception
     */
    public function testConstructFailFirst()
    {
        new JunkFlag("", true);
    }

    /**
     * Ensures everything works as expected
     */
    public function testConstructOk()
    {
        $flag = new NotJunkFlag(
            $this->_input['messageId'],
            $this->_input['clear']
        );

        $this->assertTrue($flag instanceof \Conjoon\Mail\Message\Flag\NotJunkFlag);

        $this->assertSame('$NotJunk', $flag->__toString());
        $this->assertSame($this->_input['messageId'], $flag->getMessageId());
        $this->assertSame($this->_input['clear'], $flag->isClear());

    }

}
