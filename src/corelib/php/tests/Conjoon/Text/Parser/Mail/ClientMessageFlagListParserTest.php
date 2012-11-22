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
 * @see Conjoon_Text_Parser_Mail_ClientMessageFlagListParser
 */
require_once 'Conjoon/Text/Parser/Mail/ClientMessageFlagListParser.php';


/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Text_Parser_Mail_ClientMessageFlagListParserTest
    extends PHPUnit_Framework_TestCase {


    protected $_parser = null;

    protected $_input = array();

    /**
     * Creates a new Conjoon_Text_Parser_Mail_MailboxFolderPathParser object for
     * each test
     *
     * @return void
     */
    public function setUp()
    {
        $this->_parser = new Conjoon_Text_Parser_Mail_ClientMessageFlagListParser();

        $this->_input = array(
            '[{"id":"173","isRead":false},{"id":"172","isRead":false}]'
            => array(
                   array('id' => '173', 'isRead' => false),
                   array('id' => '172', 'isRead' => false)
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
            $this->assertSame(
                $output,
                $this->_parser->parse($input)
            );
        }
    }


}
