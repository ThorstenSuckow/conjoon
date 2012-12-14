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
                array("173", true),
                array("172", false)
            ),
            '[{"id":"175","isSpam":false},{"id":"174","isSpam":true}]'
            => array(
                array("175", true),
                array("174", false)
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
        $a = 0;
        foreach ($this->_input as $input => $output) {
            $collection = new DefaultFlagCollection(
                $input
            );

            $flags = $collection->getFlags();

            $this->assertSame(count($flags), 2);

            $decodeInput = json_decode($input);

            for ($i = 0, $len = count($flags); $i < $len; $i++) {
                if ($a == 0) {
                    $this->assertTrue($flags[$i] instanceof SeenFlag);
                } else {
                    // JunkFlag oder NotJunk

                    if (!$decodeInput[$i]->isSpam) {
                        $this->assertTrue($flags[$i] instanceof NotJunkFlag);
                        $this->assertTrue(!$flags[$i]->isClear());
                    } else {
                        $this->assertTrue($flags[$i] instanceof JunkFlag);
                        $this->assertTrue(!$flags[$i]->isClear());
                    }
                }

                $this->assertSame($output[$i][0], $flags[$i]->getUId());
            }

            $a++;
        }
    }

}