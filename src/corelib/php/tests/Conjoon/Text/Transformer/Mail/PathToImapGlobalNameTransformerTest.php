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
 * @see Conjoon_Text_Transformer_Mail_PathToImapGlobalNameTransformer
 */
require_once 'Conjoon/Text/Transformer/Mail/PathToImapGlobalNameTransformer.php';


/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Text_Transformer_Mail_PathToImapGlobalNameTransformerTest
    extends PHPUnit_Framework_TestCase {

    protected $_transformer = null;

    protected $_transformerNoTail = null;

    protected $_inputs = array();

    protected $_inputsNoTail = array();

    /**
     *
     * @return void
     */
    public function setUp()
    {
        $this->_transformer =
            new Conjoon_Text_Transformer_Mail_PathToImapGlobalNameTransformer(
                array('delimiter' => '.')
            );

        $this->_transformerNoTail =
            new Conjoon_Text_Transformer_Mail_PathToImapGlobalNameTransformer(
                array('delimiter' => '.', 'popTail' => true)
            );

        $this->_inputs = array(
            "/INBOX/[Merge] Test/Messages"
            => "INBOX.[Merge] Test.Messages"
        );

        $this->_inputsNoTail = array(
            "/INBOX/[Merge] Test/Messages"
            => "INBOX.[Merge] Test"

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
     * @expectedException Conjoon_Argument_Exception
     */
    public function testConstructException()
    {
        new Conjoon_Text_Transformer_Mail_PathToImapGlobalNameTransformer();
    }

    /**
     * @expectedException Conjoon_Text_Transformer_Exception
     */
    public function testPathEqualsSlash()
    {
        $this->_transformer->transform('/');
    }

    /**
     * @expectedException Conjoon_Text_Transformer_Exception
     */
    public function testPathEqualsDelimiter()
    {
        $this->_transformer->transform('.');
    }

    /**
     * Ensure everythign works as expected.
     *
     */
    public function testTransformWithTail()
    {

        foreach ($this->_inputs as $input => $output) {
            $this->assertEquals($output, $this->_transformer->transform($input));
        }
    }

    /**
     * Ensure everythign works as expected.
     *
     */
    public function testTransformWithoutTail()
    {

        foreach ($this->_inputsNoTail as $input => $output) {
            $this->assertEquals($output, $this->_transformerNoTail->transform($input));
        }
    }

}
