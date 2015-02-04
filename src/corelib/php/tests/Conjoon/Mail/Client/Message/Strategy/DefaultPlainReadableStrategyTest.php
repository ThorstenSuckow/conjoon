<?php
/**
 * conjoon
 * (c) 2007-2015 conjoon.org
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
     * @ticket CN-914
     */
    public function test_CN914() {

        $input = array(
            'message' => array(
                'contentTextPlain' =>
                    " <\n adresse@me.com>"
            )
        );

        $output = " &lt;<a href=\"mailto:adresse@me.com\">adresse@me.com</a>&gt;";

        $result = $this->strategy->execute($input);
        $this->assertTrue($result instanceof \Conjoon\Mail\Client\Message\Strategy\ReadableStrategyResult);


        $this->assertSame(
            $output,
            $result->getBody()
        );

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
