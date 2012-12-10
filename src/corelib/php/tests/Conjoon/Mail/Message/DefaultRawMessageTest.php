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


namespace Conjoon\Mail\Message;

/**
 * @see Conjoon\Mail\Message\DefaultRawMessage
 */
require_once 'Conjoon/Mail/Message/DefaultRawMessage.php';

/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultRawMessageTest extends \PHPUnit_Framework_TestCase {

    /**
     * @expectedException \Conjoon\Argument\InvalidArgumentException
     */
    public function testConstructor_Exception()
    {
        new DefaultRawMessage("", "sfs");
    }

    public function testOk()
    {
        $rawMessage = new DefaultRawMessage("header", "");

        $rawMessage = new DefaultRawMessage("header", "body");

        $this->assertSame("header", $rawMessage->getHeader());
        $this->assertSame("body", $rawMessage->getBody());
    }

}
