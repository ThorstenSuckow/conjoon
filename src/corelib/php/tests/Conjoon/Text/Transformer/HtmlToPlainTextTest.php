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

namespace Conjoon\Text\Transformer;

/**
 * @see \Conjoon\Text\Transformer\HtmlToPlainText
 */
require_once 'Conjoon/Text/Transformer/HtmlToPlainText.php';

/**
 * @see \Conjoon\Text\Transformer\BlockquoteToQuote
 */
require_once 'Conjoon/Text/Transformer/BlockquoteToQuote.php';

/**
 * @see \Conjoon\Text\Transformer\NormalizeLineFeeds
 */
require_once 'Conjoon/Text/Transformer/NormalizeLineFeeds.php';

/**
 * @see \Conjoon\Text\Transformer\MultipleWhiteSpaceRemover
 */
require_once 'Conjoon/Text/Transformer/MultipleWhiteSpaceRemover.php';

/**
 * @see \Conjoon\Text\Transformer\TagWhiteSpaceRemover
 */
require_once 'Conjoon/Text/Transformer/TagWhiteSpaceRemover.php';


/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class HtmlToPlainTextTest extends \PHPUnit_Framework_TestCase {

    protected $_transformer = null;

    protected $_inputs = array();

    /**
     *
     * @return void
     */
    public function setUp()
    {
        $this->_transformer = new HtmlToPlainText(array(
            'blockquoteToQuote'
                => new \Conjoon\Text\Transformer\BlockquoteToQuote(),
            'normalizeLineFeeds'
                => new \Conjoon\Text\Transformer\NormalizeLineFeeds(),
            'multipleWhiteSpaceRemover'
                => new  \Conjoon\Text\Transformer\MultipleWhiteSpaceRemover(),
            'tagWhiteSpaceRemover'
                => new \Conjoon\Text\Transformer\TagWhiteSpaceRemover()
        ));

        $this->_inputs = array(
            "<pre>--------Original Message:-----------
             <table><tbody>
              <tr><td>   Subject:    </td> <td>             Test</td></tr>
               <tr><td> Date:</td> <td>03.02.2015</td></tr>
              </tbody>

              </table>
              <blockquote>Text which is in here does not <br> conform to </blockquote>
              <div>-- <br />send with conjoon</div>
              </pre>"
            => "--------Original Message:----------- \nSubject: Test\nDate: 03.02.2015\n\n>Text which is in here does not \n> conform to \n-- \nsend with conjoon",
            "<pre>--------Original Message:-----------
              <table><tbody>
               <tr><td>Subject:    </td> <td>             Test</td></tr>
               <tr><td> Date: </td> <td>03.02.2015</td></tr>
              </tbody>

              </table>
              <blockquote>Text which is in here does not <br> conform to </blockquote>
              </pre>"
            => "--------Original Message:----------- \nSubject: Test\nDate: 03.02.2015\n\n>Text which is in here does not \n> conform to \n"
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
     * Ensure everything works as expected.
     *
     */
    public function testTransform()
    {

        foreach ($this->_inputs as $input => $output) {
            $this->assertEquals($output, $this->_transformer->transform($input));
        }
    }

    /**
     * @expectedException \Conjoon\Argument\InvalidArgumentException
     */
    public function testInvalidArgument()
    {
        $this->_transformer->transform(array());
    }


}
