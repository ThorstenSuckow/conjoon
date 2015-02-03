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
 * @see Conjoon_Text_Transformer
 */
require_once 'Conjoon/Text/Transformer.php';

/**
 * @see \Conjoon\Argument\ArgumentCheck
 */
require_once 'Conjoon/Argument/ArgumentCheck.php';

use \Conjoon\Argument\ArgumentCheck as ArgumentCheck;

/**
 * Returns a plain text version of a html text.
 * Blockquotes will be transformed to ascii quotes (leading ">")
 * Some line feeds and whitespaces will be included to format the text as
 * much as possible to keep it readable.
 *
 * Example:
 *
 * Input:
 * ======
 * <pre>--------Original Message:-----------
 * <table><tbody>
 *  <tr><td>Subject:    </td> <td>             Test</td></tr>
 *  <tr><td> Date: </td> <td>03.02.2015</td></tr>
 * </tbody>
 *
 * </table>
 * <blockquote>Text which is in here does not <br> conform to </blockquote>
 * </pre>
 *
 * Output:
 * =======
 * "--------Original Message:----------- \nSubject: Test\nDate: 03.02.2015\n\n>Text which is in here does not \n> conform to \n"
 *
 *
 * @uses Conjoon_Text_Transformer
 * @category   Text
 * @package    Conjoon_Text
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class HtmlToPlainText extends \Conjoon_Text_Transformer {


    /**
     * @type TagWhiteSpaceRemover
     */
    protected $tagWhiteSpaceRemover;

    /**
     * @type MultipleWhiteSpaceRemover
     */
    protected $multipleWhiteSpaceRemover;

    /**
     * @type NormalizeLineFeeds
     */
    protected $normalizeLineFeedsTransformer;

    /**
     * @type BlockquoteToQuote
     */
    protected $blockquoteToQuoteTransformer;

    /**
     * @inheritdoc
     */
    public function __construct(Array $options = array()) {

        ArgumentCheck::check(array(
            'blockquoteToQuote' => array(
                'type'       => 'instanceof',
                'class'      => '\Conjoon\Text\Transformer\BlockquoteToQuote',
                'allowEmpty' => false
            ),
            'normalizeLineFeeds' => array(
                'type'       => 'instanceof',
                'class'      => '\Conjoon\Text\Transformer\NormalizeLineFeeds',
                'allowEmpty' => false
            ),
            'multipleWhiteSpaceRemover' => array(
                'type'       => 'instanceof',
                'class'      => '\Conjoon\Text\Transformer\MultipleWhiteSpaceRemover',
                'allowEmpty' => false
            ),
            'tagWhiteSpaceRemover' => array(
                'type'       => 'instanceof',
                'class'      => '\Conjoon\Text\Transformer\TagWhiteSpaceRemover',
                'allowEmpty' => false
            )
        ), $options);


        $this->blockquoteToQuoteTransformer  = $options['blockquoteToQuote'];
        $this->normalizeLineFeedsTransformer = $options['normalizeLineFeeds'];
        $this->multipleWhiteSpaceRemover     = $options['multipleWhiteSpaceRemover'];
        $this->tagWhiteSpaceRemover          = $options['tagWhiteSpaceRemover'];
    }

    /**
     * @inherit Conjoon_Text_Transformer::transform
     */
    public function transform($input)
    {
        $data = array('input' => $input);

        ArgumentCheck::check(array(
            'input' => array(
                'type'       => 'string',
                'allowEmpty' => true
             )
        ), $data);

        $value = $data['input'];

        $value = $this->blockquoteToQuoteTransformer->transform(
                str_replace('&nbsp;', ' ',
                    str_replace(
                        array("<br>", "<br/>", "<br />", "<BR>", "<BR/>", "<BR />"),
                        "\n",

                        preg_replace('/\s+<\/td>/', '</td>',
                            preg_replace('/<td>\s+/', '<td>',
                                $this->multipleWhiteSpaceRemover->transform(
                                    $this->tagWhiteSpaceRemover->transform(
                                        $this->normalizeLineFeedsTransformer->transform(
                                            $value
                                        )
                                    )
                                )
                            )
                    )
                )
            )
        );

        // add some linebreaks to a few block elements
        $value = str_replace(
            array('<table><tr>', '<table><tbody><tr>', '</tr></tbody></table>',
                '<TABLE><TR>', '<TABLE><TBODY><TR>', '</TR></TBODY></TABLE>',
                '</tr></table>', '</TR></TABLE>',
                '<tr>', '<TR>',
                '<table>', '<TABLE>',
                '</table>', '</TABLE>'),
            "\n",
            $value
        );

        $value = preg_replace('/<\/td><td>/', '</td><td> ', $value);

        // now strip all tags!
        $value = strip_tags($value);

        // ...and convert all special html entities back!
        return htmlspecialchars_decode($value);
    }

}