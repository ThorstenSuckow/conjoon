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
 * @see Conjoon_Text_Transformer_DateStringSanitizer
 */
require_once 'Conjoon/Text/Transformer/DateStringSanitizer.php';


/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Text_Transformer_DateStringSanitizerTest extends PHPUnit_Framework_TestCase {

    protected $_transformer = null;

    protected $_inputs = array();

    /**
     *
     * @return void
     */
    public function setUp()
    {
        $this->_transformer = new Conjoon_Text_Transformer_DateStringSanitizer();

        $this->_inputs = array(
            "Wed, 6 May 2009 20:38:58 +0000 (GMT+00:00)"
            => "Wed, 6 May 2009 20:38:58 +0000",
            "Wed, 6 May 2009 20:38:58 +0000"
            => "Wed, 6 May 2009 20:38:58 +0000"
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

    /**
     * @expectedException Conjoon_Argument_Exception
     */
    public function testInvalidArgument()
    {
        $this->_transformer->transform(array());
    }


}
