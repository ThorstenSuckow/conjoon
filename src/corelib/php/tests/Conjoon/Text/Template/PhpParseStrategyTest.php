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
 * @see Conjoon_Text_Template_PhpFileResource
 */
require_once 'Conjoon/Text/Template/PhpFileResource.php';

/**
 * @see Conjoon_Text_Template_PhpParseStrategy
 */
require_once 'Conjoon/Text/Template/PhpParseStrategy.php';


/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Text_Template_PhpParseStrategyTest extends PHPUnit_Framework_TestCase {


    protected $_strategy = null;

    protected $_textResource = "";



    /**
     * Creates a new Conjoon_Text_Template_DefaultParseStrategy object for each test
     *
     * @return void
     */
    public function setUp()
    {
        $this->_strategy = new Conjoon_Text_Template_PhpParseStrategy();

        $this->_textResource = new Conjoon_Text_Template_PhpFileResource(
            dirname(__FILE__) . "/_files/phpTemplateFile.phtml"
        );

    }

    /**
     * Creates a new Conjoon_Text_Template_DefaultParseStrategy object for each test
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->_strategy);
        unset($this->_textResource);
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
            "<td>sayWhat</td>",
            $this->_strategy->parse(
                $this->_textResource, array('sayWhat' => 'sayWhat')
            )
        );
    }

    /**
     * Test okay.
     *
     */
    public function testVariablesKeepExisting()
    {
        $this->_textResource->sayWhat = "YO";

        $this->_strategy->parse(
            $this->_textResource, array('sayWhat' => 'sayWhat')
        );

        $this->assertEquals("YO", $this->_textResource->sayWhat);
    }


}
