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
class Conjoon_Filter_QuoteToBlockquote implements Zend_Filter_Interface
{

    /**
     * Defined by Zend_Filter_Interface
     *
     * Returns a ">" quoted replaced with blockquots, i.e.
     * <pre>
     *  > test
     * </pre>
     *
     * becomes
     * <pre>
     *  <blockquote>test</blockquote>
     * </pre>
     *
     *
     * @param  mixed $value
     * @return integer
     */
    public function filter($value)
    {
        $lines = explode("\n", $value);
        $len = count($lines);

        if ($len == 0 || strpos($value, '&gt;') === false) {
            return $value;
        }

        $text = "";
        for ($i = 0; $i < $len; $i++) {

            $quotes = "";
            $a = $i;
            while($a < $len && (strpos($lines[$a], '&gt;') === 0)) {
                $quotes .= $lines[$a]."\n";
                $a++;
            }
            $text .= $this->_quote($quotes);
            $i = $a;


            $text .= ($a == $len ? "" : $lines[$i]."\n");
        }

        return $text;
    }

    private function _quote($value)
    {
        // the replacement text we will return to parse()
        $quoted = "";

        // normalize ">" - this will group all ">" and remove whitespaces in
        // between them
        $value = preg_replace(
            "/^((\Q&gt;\E)+)(( (\Q&gt;\E)*)*)/em",
            "'$1'.str_replace(' &gt;', '&gt;', '\\3').''",
            $value
        );

        $matches = array();

        preg_match_all('/^((\Q&gt;\E)+) *?(.*?$)/ms', $value, $matches, PREG_SET_ORDER);

        $currentIntend = 0;

        // loop through each list-item element.
        foreach ($matches as $key => $match) {

            $intendation = strlen($match[1])/4;

            while ($intendation > $currentIntend) {
                $currentIntend++;
                $quoted .= "<blockquote>";
            }

            while ($currentIntend > $intendation) {
                $currentIntend--;
                $quoted .= "</blockquote>";
            }

            $quoted .= $match[3]."\n";
        }

        //$quoted = trim($quoted);

        while ($currentIntend > 0) {
            $currentIntend--;
            $quoted .= "</blockquote>";
        }

        return $quoted;
    }

}