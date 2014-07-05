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


/**
 * @see Conjoon_Text_Parser_Mail_MailboxFolderPathJsonParser
 */
require_once 'Conjoon/Text/Parser/Mail/MailboxFolderPathJsonParser.php';


/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Text_Parser_Mail_MailboxFolderPathJsonParserTest
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
        $this->_parser = new Conjoon_Text_Parser_Mail_MailboxFolderPathJsonParser();

        $this->_input = array(
            '["79", "INBOXtttt", "rfwe2", "New folder (7)"]'
            => array(
                'path'    => array('INBOXtttt', 'rfwe2', 'New folder (7)'),
                'nodeId'  => 'New folder (7)',
                'rootId'  => 79
            ),
            '["root", "79", "INBOXtttt", "rfwe2", "New folder (7)"]'
            => array(
                'path'    => array('INBOXtttt', 'rfwe2', 'New folder (7)'),
                'nodeId'  => 'New folder (7)',
                'rootId'  => 79
            ),
            '["root"]'
            => array(
                'path'    => array(),
                'nodeId'  => null,
                'rootId'  => null
            ),
            '["root", "79"]'
            /**
             * @ticket CN-665
             */
            => array(
                'path'    => array(),
                'nodeId'  => null,
                'rootId'  => "79"
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
