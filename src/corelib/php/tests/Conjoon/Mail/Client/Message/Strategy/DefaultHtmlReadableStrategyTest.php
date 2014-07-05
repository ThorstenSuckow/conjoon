<?php
/**
 * conjoon
 * (c) 2007-2014 conjoon.org
 * licensing@conjoon.org
 *
 * conjoon
 * Copyright (C) 2014 Thorsten Suckow-Homberg/conjoon.org
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
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
 * @see \Conjoon\Text\Parser\Html\ExternalResourcesParser
 */
require_once 'Conjoon/Text/Parser/Html/ExternalResourcesParser.php';


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

    protected $parser;

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

        $this->parser = new \Conjoon\Text\Parser\Html\ExternalResourcesParser();

        $this->strategy = new \Conjoon\Mail\Client\Message\Strategy\DefaultHtmlReadableStrategy(
            new \HTMLPurifier($htmlPurifierConfig),
            new \Conjoon\Mail\Client\Message\Strategy\DefaultPlainReadableStrategy,
            $this->parser
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
    public function testNoContentTextHtmlOk() {

        $result = $this->strategy->execute($this->inputNoContentTextHtml['input']);
        $this->assertTrue($result instanceof \Conjoon\Mail\Client\Message\Strategy\ReadableStrategyResult);

        $this->assertSame(
            $result->getBody(),
            $this->inputNoContentTextHtml['output']
        );

    }

    /**
     * Ensures everything works as expected
     */
    public function testNoContentTextAtAllOk() {

        $result = $this->strategy->execute($this->inputNoContentTextAtAll['input']);
        $this->assertTrue($result instanceof \Conjoon\Mail\Client\Message\Strategy\ReadableStrategyResult);


        $this->assertSame(
            $result->getBody(),
            $this->inputNoContentTextAtAll['output']
        );

    }

    /**
     * Ensures everything works as expected.
     */
    public function testAreExternalResourcesAllowed() {

        $htmlPurifierConfig = \HTMLPurifier_Config::createDefault();
        $htmlPurifierConfig->set('Cache.DefinitionImpl', null);
        $htmlPurifierConfig->set('HTML.Trusted', false);

        $strategy = new \Conjoon\Mail\Client\Message\Strategy\DefaultHtmlReadableStrategy(
            new \HTMLPurifier($htmlPurifierConfig),
            new \Conjoon\Mail\Client\Message\Strategy\DefaultPlainReadableStrategy,
            $this->parser
        );

        $this->assertTrue($strategy->areExternalResourcesAllowed());

        $htmlPurifierConfig = \HTMLPurifier_Config::createDefault();
        $htmlPurifierConfig->set('Cache.DefinitionImpl', null);
        $htmlPurifierConfig->set('HTML.Trusted', false);
        $htmlPurifierConfig->set('URI.DisableExternalResources', false);

        $strategy = new \Conjoon\Mail\Client\Message\Strategy\DefaultHtmlReadableStrategy(
            new \HTMLPurifier($htmlPurifierConfig),
            new \Conjoon\Mail\Client\Message\Strategy\DefaultPlainReadableStrategy,
            $this->parser
        );

        $this->assertTrue($strategy->areExternalResourcesAllowed());

        $htmlPurifierConfig = \HTMLPurifier_Config::createDefault();
        $htmlPurifierConfig->set('Cache.DefinitionImpl', null);
        $htmlPurifierConfig->set('HTML.Trusted', false);
        $htmlPurifierConfig->set('URI.DisableExternalResources', true);

        $strategy = new \Conjoon\Mail\Client\Message\Strategy\DefaultHtmlReadableStrategy(
            new \HTMLPurifier($htmlPurifierConfig),
            new \Conjoon\Mail\Client\Message\Strategy\DefaultPlainReadableStrategy,
            $this->parser
        );

        $this->assertFalse($strategy->areExternalResourcesAllowed());
    }

}
