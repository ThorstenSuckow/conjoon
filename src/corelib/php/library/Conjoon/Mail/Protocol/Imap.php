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
 * Zend_Mail_Protocol_Imap
 */
require_once 'Zend/Mail/Protocol/Imap.php';


/**
 *
 *
 */
class Conjoon_Mail_Protocol_Imap extends Zend_Mail_Protocol_Imap {


    /**
     * split a given line in tokens. a token is literal of any form or a list
     *
     * @bug Fix for: Zend_Mail_Protocol_Imap does not propery parse IMAP
     * BODYSTRUCTURE responses
     * (see ZF-9624 http://framework.zend.com/issues/browse/ZF-9624)
     *
     * @param  string $line line to decode
     * @return array tokens, literals are returned as string, lists as array
     * @throws Zend_Mail_Protocol_Exception
     */
    protected function _decodeLine($line)
    {
        $tokens = array();
        $stack  = array();

        /*
            We start to decode the response here. The unterstood tokens are:
                literal
                "literal" or also "lit\\er\"al"
                {bytes}<NL>literal
                (literals*)
            All tokens are returned in an array. Literals in braces (the last unterstood
            token in the list) are returned as an array of tokens. I.e. the following response:
                "foo" baz {3}<NL>bar ("f\\\"oo" bar)
            would be returned as:
                array('foo', 'baz', 'bar', array('f\\\"oo', 'bar'));

            // TODO: add handling of '[' and ']' to parser for easier handling of response text
        */
        //  replace any trailling <NL> including spaces with a single space
        $line = rtrim($line) . ' ';
        while (($pos = strpos($line, ' ')) !== false) {
            $token = substr($line, 0, $pos);

            $closingPos = strpos($token,')(');
            if (($closingPos !== false) && ($token[0] != '"')) {
                $pos   = $pos - strlen($token) + $closingPos;
                $token = substr($line, 0, $pos+1);
            }
            while ($token[0] == '(') {
                array_push($stack, $tokens);
                $tokens = array();
                $token = substr($token, 1);
            }
            if ($token[0] == '"') {
                if (preg_match('%^\(*"((.|\\\\|\\")*?)" *%', $line, $matches)) {
                    $tokens[] = $matches[1];
                    $line = substr($line, strlen($matches[0]));
                    continue;
                }
            }
            if ($token[0] == '{') {
                $endPos = strpos($token, '}');
                $chars = substr($token, 1, $endPos - 1);
                if (is_numeric($chars)) {
                    $token = '';
                    while (strlen($token) < $chars) {
                        $token .= $this->_nextLine();
                    }
                    $line = '';
                    if (strlen($token) > $chars) {
                        $line = substr($token, $chars);
                        $token = substr($token, 0, $chars);
                    } else {
                        $line .= $this->_nextLine();
                    }
                    $tokens[] = $token;
                    $line = trim($line) . ' ';
                    continue;
                }
            }
            if ($stack && $token[strlen($token) - 1] == ')') {
                // closing braces are not seperated by spaces, so we need to count them
                $braces = strlen($token);
                $token = rtrim($token, ')');
                // only count braces if more than one
                $braces -= strlen($token) + 1;
                // only add if token had more than just closing braces
                if (rtrim($token) != '') {
                    $tokens[] = rtrim($token);
                }
                $token = $tokens;
                $tokens = array_pop($stack);
                // special handline if more than one closing brace
                while ($braces-- > 0) {
                    $tokens[] = $token;
                    $token = $tokens;
                    $tokens = array_pop($stack);
                }
            }
            $tokens[] = $token;
            $line = substr($line, $pos + 1);
        }

        // maybe the server forgot to send some closing braces
        while ($stack) {
            $child = $tokens;
            $tokens = array_pop($stack);
            $tokens[] = $child;
        }

        return $tokens;

    }

}
