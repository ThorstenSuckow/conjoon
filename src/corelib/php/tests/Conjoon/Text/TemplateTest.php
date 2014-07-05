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


/**
 * @see Conjoon_Text_Template
 */
require_once 'Conjoon/Text/Template.php';

/**
 * @see Conjoon_Text_Template_TextResource
 */
require_once 'Conjoon/Text/Template/TextResource.php';

/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Text_TemplateTest extends PHPUnit_Framework_TestCase {

    protected $_textResource = null;

    protected $_vars = array();

    protected $_config = array();

    protected $_result = "";

    /**
     *
     * @return void
     */
    public function setUp()
    {
        $this->_text = "A {A} B {B} C {C}";

        $this->_vars = array(
            'a' => 'A', 'B' => 'B!', 'c' => 'c'
        );

        $this->_textResource = new Conjoon_Text_Template_TextResource(
            $this->_text
        );

        $this->_result = "A A B B! C c";

        $this->_config = array(
            Conjoon_Text_Template::TEMPLATE_RESOURCE =>
               $this->_textResource,
            Conjoon_Text_Template::VARS     => $this->_vars
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
     * Ensure exception.
     *
     * @expectedException Conjoon_Argument_Exception
     */
    public function testConstruct_ArgumentException_1()
    {
        new Conjoon_Text_Template(array());
    }

    /**
     * Ensure exception.
     *
     * @expectedException Conjoon_Argument_Exception
     */
    public function testConstruct_ArgumentException_2()
    {
        new Conjoon_Text_Template();
    }

    /**
     * Ensure exception.
     *
     * @expectedException Conjoon_Argument_Exception
     */
    public function testConstruct_ArgumentException_3()
    {
        new Conjoon_Text_Template(array(
            Conjoon_Text_Template::TEMPLATE_RESOURCE => null,
            Conjoon_Text_Template::VARS     => array()
        ));
    }

    /**
     * Ensure exception.
     *
     * @expectedException Conjoon_Argument_Exception
     */
    public function testConstruct_ArgumentException_4()
    {
        new Conjoon_Text_Template(array(
            Conjoon_Text_Template::TEMPLATE_RESOURCE =>
                new Conjoon_Text_Template_TextResource("Test")
        ));
    }


    /**
     * Test okay.
     *
     */
    public function testGetVars()
    {
        $tmpl = new Conjoon_Text_Template($this->_config);

        $this->assertEquals($this->_vars, $tmpl->getVars());
    }

    public function testGetTemplateResource()
    {
        $tmpl = new Conjoon_Text_Template($this->_config);

        $this->assertEquals($this->_textResource, $tmpl->getTemplateResource());
    }

    public function testGetParseStrategy()
    {
        $tmpl = new Conjoon_Text_Template($this->_config);

        $this->assertInstanceOf(
            'Conjoon_Text_Template_DefaultParseStrategy',
            $tmpl->getParseStrategy()
        );
    }

    public function testGetParsedTemplate()
    {
        $tmpl = new Conjoon_Text_Template($this->_config);

        $this->assertEquals($this->_result, $tmpl->getParsedTemplate());
    }

    public function test__toString()
    {
        $tmpl = new Conjoon_Text_Template($this->_config);

        $this->assertEquals($this->_result, $tmpl->__toString());
    }




}
