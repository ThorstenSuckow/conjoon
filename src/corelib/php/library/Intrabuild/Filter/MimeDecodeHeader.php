<?php
/**
 * intrabuild
 * (c) 2002-2008 siteartwork.de/MindPatterns
 * license@siteartwork.de
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
 * @see Zend_Date
 */

/**
 * iconv* is used internally by Zend_Mail for decoding, such as decoding header-parts
 * which are available in a format compliant to RFC2047 (@link http://www.faqs.org/rfcs/rfc2047).
 *
 * There might be cases when a mail does submit the encoded headers lowercased, i.e.
 * instead of upper-case literals for hexadecimal values, they are lowercased.
 * iconv_mime_decode_mimeheader can for example not decode he following string:
 * =?UTF-8?Q?Ihr_pers=c3=b6nlicher_Newsletter_vom_19.05.2008?=
 * since the hexadecimal values are lower- instead of uppercased.
 * This filter replaces all ocuurences from =a[...]..=f[...] with their uppercase
 * representative.
 * Some mail clients might also break the line right in a decoded char, e.g.
 * "=?UTF-8?Q?[0000741]:_TICKET_-_l=C3=A4sst_sich_im_IE_7.0_nicht_=C3?=\r\n=?UTF-8?Q?=B6ffnen?="
 * Notice the line break right in "=C3\r\n?UTF-8?Q?=B6", which is not allowed
 * since its a decoded german umlaut ("ö"). The filter will also take this disallowed
 * breaks into account and tries to fix them.
 * It will also take header values into account which are separated by white spaces.
 *
 *
 *
 * @category   Filter
 * @package    Intrabuild_Filter
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Intrabuild_Filter_MimeDecodeHeader implements Zend_Filter_Interface
{
    /**
     * Defined by Zend_Filter_Interface
     *
     * Returns a ivonv_mime_decoded_header value.
     *
     * @param  mixed $value
     * @return integer
     */
    public function filter($value)
    {
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

        if (strpos($value, "\r\n") !== false) {
            $ms = explode("\r\n", $value);
        } else if (strpos($value, "\r") !== false) {
            $ms = explode("\r", $value);
        } else if (strpos($value, "\n") !== false) {
            $ms = explode("\n", $value);
        } else if (strpos(trim($value), " ") !== false) {
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
                    }
                    break;
                }
                $index = $i;
            }

            if ($index != -1) {
                $spec = implode(" ", array_slice($ms, 0, $index+1)) . $spec;
                $ms   = array_slice($ms, $index+1);
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
            $ms = implode("", $ms) . '?=';
        } else {
            $ms = implode("", $ms);
        }

        $s = preg_replace(
            "/=(\d[a-f]|[a-f]{0,2}|[[:xdigit:]])/e",
            "strtoupper('\\0')",
            $ms
        );

        $ret = @iconv_mime_decode_headers('A: '.$s);

        if (!is_array($ret) || (is_array($ret) && !isset($ret['A']))) {
            return $value;
        }

        return $spec . $ret['A'];
    }
}
