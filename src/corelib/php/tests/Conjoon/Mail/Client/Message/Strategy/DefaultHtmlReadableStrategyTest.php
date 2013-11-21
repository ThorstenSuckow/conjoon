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
 * @see  \Conjoon\Mail\Client\Message\Strategy\DefaultHtmlReadableStrategy
 */
require_once 'Conjoon/Mail/Client/Message/Strategy/DefaultHtmlReadableStrategy.php';

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
class DefaultHtmlReadableStrategyTest extends \PHPUnit_Framework_TestCase {


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

        $this->inputNoContentTextHtml = array(
            'input' => array(
                'message' => array(
                    'contentTextHtml' => '',
                    'contentTextPlain' => 'some plain text'
                )
            ),
            'output' => 'some plain text'
        );

        $this->inputNoContentTextAtAll = array(
            'input' => array(
                'message' => array(
                    'contentTextHtml' => ''
                )
            ),
            'output' => ''
        );

        $htmlPurifierConfig = \HTMLPurifier_Config::createDefault();
        $htmlPurifierConfig->set('Cache.DefinitionImpl', null);

        $htmlPurifierConfig->set('HTML.Trusted', false);

        $this->strategy = new \Conjoon\Mail\Client\Message\Strategy\DefaultHtmlReadableStrategy(
            new \HTMLPurifier($htmlPurifierConfig),
            new \Conjoon\Mail\Client\Message\Strategy\DefaultPlainReadableStrategy
        );

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

    /**
     * Ensures everything works as expected
     */
    public function testNoContentTextHtmlOk() {

        $this->assertSame(
            $this->strategy->execute($this->inputNoContentTextHtml['input']),
            $this->inputNoContentTextHtml['output']
        );

    }

    /**
     * Ensures everything works as expected
     */
    public function testNoContentTextAtAllOk() {

        $this->assertSame(
            $this->strategy->execute($this->inputNoContentTextAtAll['input']),
            $this->inputNoContentTextAtAll['output']
        );

    }

}
