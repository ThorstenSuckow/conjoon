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
 * @see Conjoon_Text_Transformer_Mail_EmailAddressNameQuoteTransformer
 */
require_once 'Conjoon/Text/Transformer/Mail/EmailAddressNameQuoteTransformer.php';


/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Text_Transformer_Mail_EmailAddressNameQuoteTransformerTest
    extends PHPUnit_Framework_TestCase {

    protected $_transformer = null;

    protected $_inputs = array();

    /**
     *
     * @return void
     */
    public function setUp()
    {
        $this->_transformer = new Conjoon_Text_Transformer_Mail_EmailAddressNameQuoteTransformer();

        $this->_inputs = array(
            "Peter \"Griffin\""
            => "\"Peter \\\"Griffin\\\"\"",
            "Peter @ somewhere "
            => "\"Peter @ somewhere \"",
            "Peter Griffin"
            => "Peter Griffin"

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

}
