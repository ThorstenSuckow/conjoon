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
 * @see  \Conjoon\Mail\Client\Message\Strategy\PlainReadableStrategy
 */
require_once 'Conjoon/Mail/Client/Message/Strategy/PlainReadableStrategy.php';


/**
 * @category   Conjoon
 * @package    Conjoon_Mail
 * @subpackage UnitTests
 * @group      Conjoon_Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class PlainReadableStrategyTest extends \PHPUnit_Framework_TestCase {


    protected $strategy;

    protected $input;

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

        $this->strategy = new \Conjoon\Mail\Client\Message\Strategy\PlainReadableStrategy;

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

        $this->assertSame(
            $this->strategy->execute($this->input['input']),
            $this->input['output']
        );

    }

}
