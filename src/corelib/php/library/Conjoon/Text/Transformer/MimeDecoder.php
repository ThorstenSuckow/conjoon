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
 * @see Conjoon_Text_Transformer
 */
require_once 'Conjoon/Text/Transformer.php';

/**
 * iconv* is used internally by Zend_Mail for decoding, such as decoding
 * header-parts which are available in a format compliant to RFC2047
 * (@link http://www.faqs.org/rfcs/rfc2047).
 *
 * There might be cases when a mail does submit the encoded headers lowercased,
 * i.e. instead of upper-case literals for hexadecimal values, they are lowercased.
 * iconv_mime_decode_mimeheader can for example not decode he following string:
 * =?UTF-8?Q?Ihr_pers=c3=b6nlicher_Newsletter_vom_19.05.2008?=
 * since the hexadecimal values are lower- instead of uppercased.
 * This transformer replaces all ocuurences from =a[...]..=f[...] with their
 * uppercase representative.
 * Some mail clients might also break the line right in a decoded char, e.g.
 * "=?UTF-8?Q?[0000741]:_TICKET_-_l=C3=A4sst_sich_im_IE_7.0_nicht_=C3?=\r\n=?UTF-8?Q?=B6ffnen?="
 * Notice the line break right in "=C3\r\n?UTF-8?Q?=B6", which is not allowed
 * since its a decoded german umlaut ("ö"). The transformer will also take this
 * disallowed breaks into account and tries to fix them.
 * It will also take header values into account which are separated by white spaces.
 *
 * Example:
 *
 * Input:
 * ======
 * 1) "=?windows-1252?Q?Fwd=3A_Aktualisierte_Einladung=3A_Employment_Options_at_?=\n\t"
 *    . "=?windows-1252?Q?conjoon_=40_Di_8=2E_Sep=2E_17=3A00_=96_18=3A00_=28Thorsten_Suckow=2DHomberg?=\n\t"
 *    . "=?windows-1252?Q?=29?="
 * 2) "=?ISO-8859-15?Q?=DC=DCPIJM=D6N=DF=DF=DF?="
 *
 * Output:
 * =======
 * 1) "Fwd: Aktualisierte Einladung: Employment Options at conjoon @ Di 8. Sep. 17:00 – 18:00 (Thorsten Suckow-Homberg)",
 * 2) "ÜÜPIJMÖNßßß"
 *
 *
 * @uses Conjoon_Text_Transformer
 * @category   Text
 * @package    Conjoon_Text
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Text_Transformer_MimeDecoder extends Conjoon_Text_Transformer {

    /**
     * Callback helper for preg_replace_callback
     *
     * @param array $matches
     *
     * @return string
     */
    protected function strtoupperCallback(Array $matches) {
        return strtoupper($matches[0]);
    }


    /**
     * @inherit Conjoon_Text_Transformer::transform
     */
    public function transform($input)
    {
        $data = array('input' => $input);

        /**
         * @see Conjoon_Argument_Check
         */
        require_once 'Conjoon/Argument/Check.php';

        Conjoon_Argument_Check::check(array(
            'input' => array(
                'allowEmpty' => true,
                'type'       => 'string'
            )
        ), $data);

        $value = $data['input'];

        if ($value === "") {
            return "";
        }

        $bid = '?q?';
        $q = stripos($value, '?q?');
        if ($q === false) {
            $q = stripos($value, '?b?');
            if ($q === false) {
                return $value;
            }
            $bid = '?b?';
        }

        $ms = array(trim($value));

        $usedSpace = false;

        if (strpos($value, "\r\n") !== false) {
            $ms = explode("\r\n", $value);
        } else if (strpos($value, "\r") !== false) {
            $ms = explode("\r", $value);
        } else if (strpos($value, "\n") !== false) {
            $ms = explode("\n", $value);
        } else if (strpos(trim($value), " ") !== false) {
            $usedSpace = true;
            $ms = explode(" ", trim($value));
        }

        $len = count($ms);

        $spec = "";

        if ($len > 1) {

            $index = -1;
            for ($i = 0; $i < $len; $i++) {
                $ms[$i] = $ms[$i];
                if (stripos($ms[$i], $bid) !== false) {
                    if (strpos($ms[$i], '=') === 0) {
                        $spec = ' ';
                    } else {
                        $fo = strpos($ms[$i], '=');
                        $old = $ms[$i];
                        $ms[$i] = substr($ms[$i], 0, $fo);
                        array_push($ms, substr($old, $fo));


                    }
                    break;
                }
                $index = $i;
            }

            if ($index != -1) {
                $spec = implode(" ", array_slice($ms, 0, $index+2)) . $spec;
                $ms   = array_slice($ms, $index+2);

            }

            $ms[0] = substr($ms[0], 0, -2);
            $chId  = substr($ms[0], 0, $q+3);

            for ($i = 1, $len_i = count($ms); $i < $len_i; $i++) {

                $f = strrpos($ms[$i], '?=');

                if ($f != false) {
                    $ms[$i] = trim($ms[$i]);
                }
                if ($ms[$i] == "") {
                    continue;
                }
                $ms[$i] = str_replace($chId, "", $ms[$i]);

                if ($f !== false) {
                    $ms[$i] = substr($ms[$i], 0, -2);
                }
            }
            $tmp = array_shift($ms);
            $ms = $tmp . ($usedSpace ? "" : "")
                  . implode(($usedSpace ? " " : ""), $ms) . '?=';
        } else {
            $ms = implode("", $ms);
        }

        $s = preg_replace_callback(
            "/=(\d[a-f]|[a-f]{0,2}|[[:xdigit:]])/",
            array($this, "strtoupperCallback"),
            $ms
        );

        $oldEncodings = array(
            'input_encoding'    => iconv_get_encoding('input_encoding'),
            'output_encoding'   => iconv_get_encoding('output_encoding'),
            'internal_encoding' => iconv_get_encoding('internal_encoding')
        );

        iconv_set_encoding('input_encoding',    'UTF-8');
        iconv_set_encoding('output_encoding',   'UTF-8');
        iconv_set_encoding('internal_encoding', 'UTF-8');

        $ret = @iconv_mime_decode_headers('A: ' . $s);

        if (!is_array($ret) || (is_array($ret) && !isset($ret['A']))) {
            $delimPos = stripos($s, $bid);
            $mimeChar = strtolower(substr($s, 2, $delimPos-2));
            @iconv_set_encoding('internal_encoding', $mimeChar);
            $ret = @iconv_mime_decode_headers('A: ' . $s);
        }

        iconv_set_encoding('input_encoding',    $oldEncodings['input_encoding']);
        iconv_set_encoding('output_encoding',   $oldEncodings['output_encoding']);
        iconv_set_encoding('internal_encoding', $oldEncodings['internal_encoding']);

        if (!is_array($ret) || (is_array($ret) && !isset($ret['A']))) {
            return $value;
        }

        return trim($spec . $ret['A']);
    }


}