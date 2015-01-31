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
 * @package    Conjoon_Filter
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Filter_MimeDecodeHeader implements Zend_Filter_Interface
{
    /**
     * Defined by Zend_Filter_Interface
     *
     * Returns a ivonv_mime_decoded_header value.
     *
     * @param  mixed $value
     * @return integer
     *
     * @deprecated use Conjoon_Text_Transformer_MimeDecoder
     */
    public function filter($value)
    {
        /**
         * @see Conjoon_Text_Transformer_MimeDecoder
         */
        require_once 'Conjoon/Text/Transformer/MimeDecoder.php';

        $transformer = new Conjoon_Text_Transformer_MimeDecoder();

        return $transformer->transform($value);
    }
}
