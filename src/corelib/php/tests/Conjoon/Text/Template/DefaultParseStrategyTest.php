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


/**
 * @see Conjoon_Text_Template_DefaultParseStrategy
 */
require_once 'Conjoon/Text/Template/DefaultParseStrategy.php';


/**
 * @see Conjoon_Text_Template_TextResource
 */
require_once 'Conjoon/Text/Template/TextResource.php';

/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Text_Template_DefaultParseStrategyTest extends PHPUnit_Framework_TestCase {


    protected $_strategy = null;

    protected $_textResource = "";

    protected $_result = "";

    protected $_vars = array('a' => 'A', 'B' => 'B!', 'c' => 'c');

    /**
     * Creates a new Conjoon_Text_Template_DefaultParseStrategy object for each test
     *
     * @return void
     */
    public function setUp()
    {
        $this->_strategy = new Conjoon_Text_Template_DefaultParseStrategy();

        $this->_result = "A A B B! C c";

        $this->_textResource = new Conjoon_Text_Template_TextResource("A {A} B {B} C {C}");

    }

    /**
     * Creates a new Conjoon_Text_Template_DefaultParseStrategy object for each test
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->_strategy);
    }

// +---------------------------------------------------------------------------
// | Tests
// +---------------------------------------------------------------------------

    /**
     * Test okay.
     *
     */
    public function testParse()
    {
        $this->assertEquals(
            $this->_result,
            $this->_strategy->parse($this->_textResource, $this->_vars)
        );
    }


}
