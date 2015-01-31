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
 * @see Zend_Filter_Interface
 */
require_once 'Zend/Filter/Interface.php';


/**
 * @category   Filter
 * @package    Conjoon_Filter
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Filter_BlockquoteToQuote implements Zend_Filter_Interface
{

    /**
     * Defined by Zend_Filter_Interface
     *
     *
     *
     * @param  mixed $value
     * @return integer
     */
    public function filter($value)
    {
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