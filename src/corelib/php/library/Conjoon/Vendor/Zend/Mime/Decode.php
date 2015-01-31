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
 * $Author: T. Suckow $
 * $Id: Transformer.php 1985 2014-07-05 13:00:08Z T. Suckow $
 * $Date: 2014-07-05 15:00:08 +0200 (Sa, 05 Jul 2014) $
 * $Revision: 1985 $
 * $LastChangedDate: 2014-07-05 15:00:08 +0200 (Sa, 05 Jul 2014) $
 * $LastChangedBy: T. Suckow $
 * $URL: http://svn.conjoon.org/trunk/src/corelib/php/library/Conjoon/Text/Transformer.php $
 */

/**
 * @see Zend_Mime_Decode
 */
require_once 'Zend/Mime/Decode.php';

/**
 * @category   Conjoon_Vendor_Zend
 * @package    Conjoon_Vendor_Zend_Mime
 */
class Conjoon_Vendor_Zend_Mime_Decode extends Zend_Mime_Decode
{

    /**
     * This method is almost a 1:1 copy of the original implementation.
     * It provides a fix for ZF-10168. This is needed here since original
     * implementation does not use late static binding.
     * @see splitMime
     *
     * decodes a mime encoded String and returns a
     * struct of parts with header and body
     *
     * @param  string $message  raw message content
     * @param  string $boundary boundary as found in content-type
     * @param  string $EOL EOL string; defaults to {@link Zend_Mime::LINEEND}
     * @return array|null parts as array('header' => array(name => value), 'body' => content), null if no parts found
     * @throws Zend_Exception
     *
     * Original licensing information of this code:
     * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
     * @license    http://framework.zend.com/license/new-bsd     New BSD License
     */
    public static function splitMessageStruct($message, $boundary, $EOL = Zend_Mime::LINEEND)
    {
        $parts = self::splitMime($message, $boundary);
        if (count($parts) <= 0) {
            return null;
        }
        $result = array();
        foreach ($parts as $part) {
            self::splitMessage($part, $headers, $body, $EOL);
            $result[] = array('header' => $headers,
                'body'   => $body    );
        }
        return $result;
    }

    /**
     * This method is almost a 1:1 copy of the original implementation.
     * It provides a fix for ZF-10168.
     * See http://framework.zend.com/issues/browse/ZF-10168
     *
     * Explode MIME multipart string into seperate parts
     *
     * Parts consist of the header and the body of each MIME part.
     *
     * @param  string $body     raw body of message
     * @param  string $boundary boundary as found in content-type
     * @return array parts with content of each part, empty if no parts found
     * @throws Zend_Exception
     *
     * Original licensing information of this code:
     * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
     * @license    http://framework.zend.com/license/new-bsd     New BSD License
     */
    public static function splitMime($body, $boundary)
    {
        // TODO: we're ignoring \r for now - is this function fast enough and is it safe to asume noone needs \r?
        $body = str_replace("\r", '', $body);

        $start = 0;
        $res = array();
        // find every mime part limiter and cut out the
        // string before it.
        // the part before the first boundary string is discarded:
        $p = strpos($body, '--' . $boundary . "\n", $start);
        if ($p === false) {
            // no parts found!
            return array();
        }

        // position after first boundary line
        $start = $p + 3 + strlen($boundary);

        while (($p = strpos($body, '--' . $boundary . "\n", $start)) !== false) {
            $res[] = substr($body, $start, $p-$start);
            $start = $p + 3 + strlen($boundary);
        }

        // no more parts, find end boundary
        $p = strpos($body, '--' . $boundary . '--', $start);
        /* fixed */
        if ($p === false) {
           $p = strlen($boundary);
        }
        if ($p===false) {
            throw new Zend_Exception('Not a valid Mime Message: End Missing');
        }

        // the remaining part also needs to be parsed:
        $res[] = substr($body, $start, $p - $start);
        return $res;
    }

}
