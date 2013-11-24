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


namespace Conjoon\Mail\Client\Message\Strategy;

/**
 * @see  \Conjoon\Mail\Client\Message\Strategy\DefaultPlainReadableStrategy
 */
require_once 'Conjoon/Mail/Client/Message/Strategy/DefaultPlainReadableStrategy.php';


/**
 * @category   Conjoon
 * @package    Conjoon_Mail
 * @subpackage UnitTests
 * @group      Conjoon_Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultPlainReadableStrategyTest extends \PHPUnit_Framework_TestCase {


    protected $strategy;

    protected $input;

    protected $inputNoText;

    protected function setUp()
    {
        $this->input = array(
            'input' => array(
                'message' => array(
                    'contentTextPlain' => 'some plain text'
                )
            ),
            'output' => 'some plain text'
        );

        $this->inputNoText = array(
            'input' => array(
                'message' => array(
                    'contentTextPlain' => ''
                )
            ),
            'output' => ''
        );

        $this->strategy = new \Conjoon\Mail\Client\Message\Strategy\DefaultPlainReadableStrategy;

    }

    /**
     * @expectedException \Conjoon\Mail\Client\Message\Strategy\StrategyException
     */
    public function testException() {
        $this->strategy->execute(array());
    }

    /**
     * Ensures everything works as expected
     */
    public function testOk() {

        $result = $this->strategy->execute($this->input['input']);
        $this->assertTrue($result instanceof \Conjoon\Mail\Client\Message\Strategy\ReadableStrategyResult);


        $this->assertSame(
            $result->getBody(),
            $this->input['output']
        );

    }

    /**
     * Ensures everything works as expected
     */
    public function testInputNoTextOk() {

        $result = $this->strategy->execute($this->inputNoText['input']);
        $this->assertTrue($result instanceof \Conjoon\Mail\Client\Message\Strategy\ReadableStrategyResult);


        $this->assertSame(
            $result->getBody(),
            $this->inputNoText['output']
        );

    }

}
