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

namespace Conjoon\Text\Parser\Mail;

/**
 * @see Conjoon\Text\Parser\Mail\MessageContentParser
 */
require_once 'Conjoon/Text/Parser/Mail/MessageContentParser.php';


/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class MessageContentParserTest extends \PHPUnit_Framework_TestCase {

    protected $parser;

    protected function setUp()
    {
        parent::setUp();

        $this->parser = new MessageContentParser();
    }

    /**
     * @expectedException \Conjoon\Argument\InvalidArgumentException
     */
    public function testParseExceptionNoSplit()
    {
        $this->parser->parse("sfsfsf");
    }

    /**
     * @expectedException \Conjoon\Argument\InvalidArgumentException
     */
    public function testParseExceptionEmpty()
    {
        $this->parser->parse("");
    }

    public function testOk()
    {
        $this->assertEquals(
            array(
                'contentTextPlain' => 'contentTextPlain',
                'contentTextHtml'  => ''
            ),
            $this->parser->parse(
                "Content-Type: text/plain\n\ncontentTextPlain"
            )
        );
    }

}
