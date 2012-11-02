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
 * @see Conjoon_Text_Transformer_EmailAddressToHtml
 */
require_once 'Conjoon/Text/Transformer/EmailAddressToHtml.php';


/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Text_Transformer_EmailAddressToHtmlTest extends PHPUnit_Framework_TestCase {

    protected $_transformer = null;

    protected $_inputs = array();

    /**
     *
     * @return void
     */
    public function setUp()
    {
        $this->_transformer = new Conjoon_Text_Transformer_EmailAddressToHtml();

        $this->_inputs = array(
            "This is a text. You can answer user@domain.tld if you like."
            => "This is a text. You can answer "
                . "<a href=\"mailto:user@domain.tld\">user@domain.tld</a> "
                . "if you like.",
            "Notify this one! email.address-where.yo@domain.sub-here.tld."
            => "Notify this one! <a href=\"mailto:email.address-where"
                . ".yo@domain.sub-here.tld\">email.address-where.yo@domain."
                . "sub-here.tld</a>.",
            "address@without.text"
            => "<a href=\"mailto:address@without.text\">address@without.text</a>"
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
