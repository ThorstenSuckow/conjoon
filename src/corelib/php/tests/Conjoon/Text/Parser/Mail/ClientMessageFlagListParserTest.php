<?php
/**
 * conjoon
 * (c) 2007-2015 conjoon.org
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
