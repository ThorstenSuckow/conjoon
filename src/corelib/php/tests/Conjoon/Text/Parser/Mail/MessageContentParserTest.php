<?php
/**
 * conjoon
 * (c) 2007-2014 conjoon.org
 * licensing@conjoon.org
 *
 * conjoon
 * Copyright (C) 2014 Thorsten Suckow-Homberg/conjoon.org
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
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
