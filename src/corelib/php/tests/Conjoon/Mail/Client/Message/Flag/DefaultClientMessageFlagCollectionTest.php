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
 * @see Conjoon_Mail_Client_Message_Flag_DefaultClientMessageFlagCollection
 */
require_once 'Conjoon/Mail/Client/Message/Flag/DefaultClientMessageFlagCollection.php';


/**
 * @category   Conjoon
 * @package    Conjoon_Mail
 * @subpackage UnitTests
 * @group      Conjoon_Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Mail_Client_Message_Flag_DefaultClientMessageFlagCollectionTest
    extends PHPUnit_Framework_TestCase {

    protected $_input;

    protected $_validJsonUnknownFlag;

    public function setUp()
    {
        $this->_input = array(
            '[{"id":"173","isRead":false},{"id":"172","isRead":true}]'
            => array(
                array("173", false),
                array("172", true)
            )
        );

        $this->_validJsonUnknownFlag = '[{"id":"173","isRead":false},{"id":"172","iSread":true}]';
    }

    /**
     * @expectedException Conjoon_Argument_Exception
     */
    public function testConstructorException_A()
    {
        new Conjoon_Mail_Client_Message_Flag_DefaultClientMessageFlagCollection("");
    }

    /**
     * @expectedException Conjoon_Mail_Client_Message_Flag_ClientMessageFlagException
     */
    public function testConstructorException_B()
    {
        new Conjoon_Mail_Client_Message_Flag_DefaultClientMessageFlagCollection(
            "sasfaasfsfa"
        );
    }

    /**
     * @expectedException Conjoon_Mail_Client_Message_Flag_ClientMessageFlagException
     */
    public function testConstructorException_C()
    {
        new Conjoon_Mail_Client_Message_Flag_DefaultClientMessageFlagCollection(
            $this->_validJsonUnknownFlag
        );
    }

    /**
     * Ensures everything works as expected.
     */
    public function testOk()
    {
        foreach ($this->_input as $input => $output) {
            $collection = new Conjoon_Mail_Client_Message_Flag_DefaultClientMessageFlagCollection(
                $input
            );

            $flags = $collection->getClientMessageFlags();

            $this->assertSame(count($flags), 2);

            for ($i = 0, $len = count($flags); $i < $len; $i++) {
                $this->assertTrue($flags[$i] instanceof Conjoon_Mail_Client_Message_Flag_ClientSeenFlag);
                $this->assertSame($output[$i][0], $flags[$i]->getMessageId());
                $this->assertSame($output[$i][1], $flags[$i]->isClear());
            }
        }
    }

}