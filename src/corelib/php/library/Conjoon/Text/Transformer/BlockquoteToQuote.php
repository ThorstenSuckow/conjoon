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
 * Returns a (multilevel, HTML) Blockquote-Text quoted to plain text. The
 * transformer expects all other html tags to be stripped already, except
 * for blockquote tags
 *
 * Example:
 *
 * Input:
 * ======
 * <blockquote>Test me now</blockquote>
 *
 * Output:
 * =======
 * &gt;Test me now
 *
 * @uses Conjoon_Text_Transformer
 * @category   Text
 * @package    Conjoon_Text
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class BlockquoteToQuote extends \Conjoon_Text_Transformer {

    /**
     * @inherit Conjoon_Text_Transformer::transform
     */
    public function transform($input)
    {
        $data = array('input' => $input);

        ArgumentCheck::check(array(
            'input' => array(
                'type'       => 'string',
                'allowEmpty' => false
             )
        ), $data);

        $value = $data['input'];

        // first off, add an extra whitespace char to every "&gt;" that appears
        // at the beginning of a line, so they are not accidently identified as
        // as quotes. We will also check if the &gt; appears after a closing tag, in this case we
        // will also add an extra whitespace.
        // This also applies to text that starts with a &gt;
        $index = strpos($value, "&gt;");

        if ($index === 0) {
            $value = " ". $value;
        }

        $value = str_replace(
            array("\n&gt;", ">&gt;"),
            array("\n &gt;", "> &gt;"),
            $value
        );

        // normalize blockquote
        $value = preg_replace("/(<\/?)(blockquote)[^>]*>/i",
            "$1blockquote>",
            $value
        );

        // some browsers (IE) add linefeeds in between tags... remove them
        $value = str_replace(
            array("<blockquote>\n<blockquote>", "</blockquote>\n</blockquote>"),
            array("<blockquote><blockquote>", "</blockquote></blockquote>"),
            $value
        );

        $lines = explode("\n", $value);

        $quotes = array();
        $final  = array();
        for ($nr = 0, $len = count($lines); $nr < $len; $nr++) {
            $line = $lines[$nr];

            if (trim($line) == "") {
                $final[] = implode("", $quotes) . $line;
                continue;
            } else if (trim($line) == "<blockquote>") {
                $quotes[] = '&gt;';
                $final[] = implode("", $quotes) . preg_replace("/(<\/?)(blockquote)[^>]*>/i", "", $line);
                continue;
            } else if (trim($line) == "</blockquote>") {
                $final[] = implode("", $quotes) . preg_replace("/(<\/?)(blockquote)[^>]*>/i", "", $line);
                array_pop($quotes);
                continue;
            }

            $vLine = str_replace(
                array('<blockquote>', '</blockquote>'),
                array("\n<blockquote>\n", "\n</blockquote>\n"),
                $line
            );

            $vLines = explode("\n", $vLine);

            for ($a = 0, $lena = count($vLines); $a < $lena; $a++) {
                $tline = $vLines[$a];

                if ($tline == '<blockquote>') {
                    $quotes[] = '&gt;';
                } else if ($tline == '</blockquote>') {
                    array_pop($quotes);
                } else if ($tline == "") {
                    continue;
                } else {
                    $final[] = implode("", $quotes) . $tline;
                }
            }
        }

        return implode("\n", $final);
    }

}