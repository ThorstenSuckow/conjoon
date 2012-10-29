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
