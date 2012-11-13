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
 * @see Conjoon_Text_Transformer_DateStringSanitizer
 */
require_once 'Conjoon/Text/Transformer/DateStringSanitizer.php';


/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Text_Transformer_DateStringSanitizerTest extends PHPUnit_Framework_TestCase {

    protected $_transformer = null;

    protected $_inputs = array();

    /**
     *
     * @return void
     */
    public function setUp()
    {
        $this->_transformer = new Conjoon_Text_Transformer_DateStringSanitizer();

        $this->_inputs = array(
            "Wed, 6 May 2009 20:38:58 +0000 (GMT+00:00)"
            => "Wed, 6 May 2009 20:38:58 +0000",
            "Wed, 6 May 2009 20:38:58 +0000"
            => "Wed, 6 May 2009 20:38:58 +0000"
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
     * Ensure everythign works as expected.
     *
     */
    public function testTransform()
    {

        foreach ($this->_inputs as $input => $output) {
            $this->assertEquals($output, $this->_transformer->transform($input));
        }
    }

    /**
     * @expectedException Conjoon_Argument_Exception
     */
    public function testInvalidArgument()
    {
        $this->_transformer->transform(array());
    }


}
