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
 * @see  \Conjoon\Mail\Client\Message\Strategy\HtmlReadableStrategyTest
 */
require_once 'Conjoon/Mail/Client/Message/Strategy/HtmlReadableStrategyTest.php';


/**
 * @category   Conjoon
 * @package    Conjoon_Mail
 * @subpackage UnitTests
 * @group      Conjoon_Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class HtmlReadableStrategyTest extends \PHPUnit_Framework_TestCase {


    protected $strategy;

    protected $input;

    protected function setUp()
    {
        $this->input = array(
            'input' => array(
                'message' => array(
                    'contentTextHtml' =>
                        '<div>some plain text<script type="text/javascript">alert("YO!");</script></div>'
                 )
            ),
            'output' => '<div>some plain text</div>'
        );

        $this->strategy = new \Conjoon\Mail\Client\Message\Strategy\HtmlReadableStrategy;

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
