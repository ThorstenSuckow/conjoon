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
 * @see Conjoon_Text_Parser_Mail_MailboxFolderPathParser
 */
require_once 'Conjoon/Text/Parser/Mail/MailboxFolderPathParser.php';


/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Text_Parser_Mail_MailboxFolderPathParserTest extends PHPUnit_Framework_TestCase {


    protected $_parser = null;

    protected $_input = array();

    /**
     * Creates a new Conjoon_Text_Parser_Mail_MailboxFolderPathParser object for each test
     *
     * @return void
     */
    public function setUp()
    {
        $this->_parser = new Conjoon_Text_Parser_Mail_MailboxFolderPathParser();

        $this->_input = array(
            "/root/79/INBOXtttt/rfwe2/New folder (7)"
            => array(
                'path'    => '/INBOXtttt/rfwe2/New folder (7)',
                'nodeId'  => 'New folder (7)',
                'rootId'  => 79
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
