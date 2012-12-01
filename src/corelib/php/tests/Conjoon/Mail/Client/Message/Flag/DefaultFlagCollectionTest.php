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
 * @see Conjoon_Mail_Client_Message_Flag_DefaultClientMessageFlagCollection
 */
require_once 'Conjoon/Mail/Client/Message/Flag/DefaultFlagCollection.php';


/**
 * @category   Conjoon
 * @package    Conjoon_Mail
 * @subpackage UnitTests
 * @group      Conjoon_Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultFlagCollectionTest extends \PHPUnit_Framework_TestCase {

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
     * @expectedException \Conjoon_Argument_Exception
     */
    public function testConstructorException_A()
    {
        new DefaultFlagCollection("");
    }

    /**
     * @expectedException \Conjoon\Mail\Client\Message\Flag\FlagException
     */
    public function testConstructorException_B()
    {
        new DefaultFlagCollection(
            "sasfaasfsfa"
        );
    }

    /**
     * @expectedException \Conjoon\Mail\Client\Message\Flag\FlagException
     */
    public function testConstructorException_C()
    {
        new DefaultFlagCollection(
            $this->_validJsonUnknownFlag
        );
    }

    /**
     * Ensures everything works as expected.
     */
    public function testOk()
    {
        foreach ($this->_input as $input => $output) {
            $collection = new DefaultFlagCollection(
                $input
            );

            $flags = $collection->getFlags();

            $this->assertSame(count($flags), 2);

            for ($i = 0, $len = count($flags); $i < $len; $i++) {
                $this->assertTrue($flags[$i] instanceof SeenFlag);
                $this->assertSame($output[$i][0], $flags[$i]->getMessageId());
                $this->assertSame($output[$i][1], $flags[$i]->isClear());
            }
        }
    }

}