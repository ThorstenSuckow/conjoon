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
 * @see Conjoon_Text_Parser_Mail_EmailAddressIdentityParser
 */
require_once 'Conjoon/Text/Parser/Mail/EmailAddressIdentityParser.php';


/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Text_Parser_Mail_EmailAddressIdentityParserTest extends PHPUnit_Framework_TestCase {


    protected $_parser = null;

    protected $_input = array();

    /**
     * Creates a new Conjoon_Text_Parser_Mail_EmailAddressIdentityParser object for each test
     *
     * @return void
     */
    public function setUp()
    {
        $this->_parser = new Conjoon_Text_Parser_Mail_EmailAddressIdentityParser();

        $this->_input = array(
            "\"Thorsten Suckow-Homberg\" <tsuckow@conjoon.org>, yo@mtv.com"
            => array(
                array("tsuckow@conjoon.org", "Thorsten Suckow-Homberg"),
                array("yo@mtv.com")
            )
        );

    }

    /**
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->_parser);
    }

// +---------------------------------------------------------------------------
// | Tests
// +---------------------------------------------------------------------------

    /**
     * Ensure everythingworks as expected.
     *
     */
    public function testParse()
    {
        foreach ($this->_input as $input => $output) {
            $this->assertEquals(
                $output,
                $this->_parser->parse($input)
            );
        }
    }


}
