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
 * =?UTF-8?Q?Ihr_pers=c3=b6nlicher_Xing-Newsletter_vom_19.05.2008?=
 * since the hexadecimal values are lower- instead of uppercased.
 * This filter replaces all ocuurences from =a..=f with their uppercase representative.
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
        if (strpos($value, '?Q?') === false && 
            strpos($value, '?B?') === false) {
            return $value;    
        }
        
        $ms = explode("\r\n", $value);
        if (count($ms) == 1) {
            $ms = explode("\r", $value);
            if (count($ms) == 1) {
                $ms = explode("\n", $value);
            }
        }
        
        for ($i = 0, $len = count($ms); $i < $len; $i++) {
            $ms[$i] = trim($ms[$i]);
        }
        
        $ms = implode("", $ms);
        
        $s = str_replace(
            array(
                '=a', '=b', '=c', '=d', '=e', '=f', 
            ) , 
            array(
                '=A', '=B', '=C', '=D', '=E', '=F', 
            ), 
            $ms
        );
        
        $ret = @iconv_mime_decode_headers('A: '.$s);
        
        if (!is_array($ret) || (is_array($ret) && !isset($ret['A']))) {
            return $value;    
        }
       
        return $ret['A'];
    }
}
