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
 * @see Conjoon_Text_Parser_Mail_MessageHeaderParser
 */
require_once 'Conjoon/Text/Parser/Mail/MessageHeaderParser.php';


/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Text_Parser_Mail_MessageHeaderParserTest extends PHPUnit_Framework_TestCase {


    protected $_parser = null;

    protected $_input = array();

    /**
     * Creates a new Conjoon_Text_Parser_Mail_MessageHeaderParser object for each test
     *
     * @return void
     */
    public function setUp()
    {
        $this->_parser = new Conjoon_Text_Parser_Mail_MessageHeaderParser();

        $this->_input = array(
            "From: toaddress@domain.tld\n"
            . "Reply-To: replyname@domain.tld\n"
            . "To: demo-registration@conjoon.org\n"
            . "Subject:  . . . reg for [someuser@domainname.tld]\n"
            . "Date: Mon, 19 Nov 2012 13:01:38 +0100\n"
            . "Content-Type: text/plain; charset=iso-8859-1\n"
            . "Content-Transfer-Encoding: quoted-printable\n"
            . "Content-Disposition: inline\n"
            . "MIME-Version: 1.0\n"
           . "Message-Id: <uniqueid@somegatewy.tld>"
            => array(
                'from'       => 'toaddress@domain.tld',
                'replyTo'    => 'replyname@domain.tld',
                'to'         => 'demo-registration@conjoon.org',
                'subject'    => '. . . reg for [someuser@domainname.tld]',
                'date'       => 'Mon, 19 Nov 2012 13:01:38 +0100',
                'cc'         => '',
                'references' => '',
                'inReplyTo'  => '',
                'messageId'  => '<uniqueid@somegatewy.tld>'
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
