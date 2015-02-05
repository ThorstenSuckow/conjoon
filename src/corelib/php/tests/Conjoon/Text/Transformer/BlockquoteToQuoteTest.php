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

namespace Conjoon\Text\Transformer;

/**
 * @see \Conjoon\Text\Transformer\BlockquoteToQuote
 */
require_once 'Conjoon/Text/Transformer/BlockquoteToQuote.php';


/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class BlockquoteToQuoteTest extends \PHPUnit_Framework_TestCase {

    protected $_transformer = null;

    protected $_inputs = array();

    /**
     *
     * @return void
     */
    public function setUp()
    {
        $this->_transformer = new BlockquoteToQuote();

        $this->_inputs = array(
            "<blockquote>&lt;<a href=''>test@test.de</a>&gt;</blockquote>"
            =>  "&gt; &lt;<a href=''>test@test.de</a>&gt;",
            "<blockquote>test</blockquote>"
            => "&gt; test",
            "<blockquote>test<blockquote>test\n me</blockquote></blockquote>"
            => "&gt; test\n&gt;&gt; test\n&gt;&gt; me",
            "<blockquote> test<blockquote>test\n me</blockquote></blockquote>"
            => "&gt; test\n&gt;&gt; test\n&gt;&gt; me",
            "<blockquote>".
            "test".
              "<blockquote>".
                "test\n".
                "me".
              "</blockquote>".
              "this\n".
              "is\n".
              "another\n".
              "separate\n".
              "block\n".
              "here\n".
            "</blockquote>"
            => "&gt; test\n".
                "&gt;&gt; test\n".
                "&gt;&gt; me\n".
                "&gt; this\n".
                "&gt; is\n".
                "&gt; another\n".
                "&gt; separate\n".
                "&gt; block\n".
                "&gt; here\n".
                "&gt;"
        );

    }

    /**
     *
     * @return void
     */
    public function tearDown()
    {

    }

// +---------------------------------------------------------------------------
// | Tests
// +---------------------------------------------------------------------------

    /**
     * @ticket CN-919
     */
    public function test_CN919() {

        $input  = "";
        $output = "";

        $this->assertEquals($output, $this->_transformer->transform($input));

        $input  = " ";
        $output = " ";

        $this->assertEquals($output, $this->_transformer->transform($input));
    }

    /**
     * Ensure everything works as expected.
     *
     */
    public function testTransform()
    {

        foreach ($this->_inputs as $input => $output) {
            $this->assertEquals($output, $this->_transformer->transform($input));
        }
    }

    /**
     * @expectedException \Conjoon\Argument\InvalidArgumentException
     */
    public function testInvalidArgument()
    {
        $this->_transformer->transform(array());
    }


}
