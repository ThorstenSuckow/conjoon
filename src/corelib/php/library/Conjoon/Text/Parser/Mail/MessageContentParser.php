<?php
/**
 * conjoon
 * (c) 2007-2014 conjoon.org
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

namespace Conjoon\Text\Parser\Mail;

use Conjoon\Argument\ArgumentCheck,
    Conjoon\Argument\InvalidArgumentException;

/**
 * @see \Conjoon\Argument\ArgumentCheck
 */
require_once 'Conjoon/Argument/ArgumentCheck.php';

/**
 * @see \Conjoon_Mail_Message
 */
require_once 'Conjoon/Mail/Message.php';

/**
 * @see \Conjoon_Text_Parser
 */
require_once 'Conjoon/Text/Parser.php';

/**
 * @see \Conjoon_Text_Parser_Exception
 */
require_once 'Conjoon/Text/Parser/Exception.php';

/**
 * Parses a raw email message's body for content text/plain and content text/html
 * and returns the result in an array with the keys
 * "contentTextPlain" and "contentTextHtml".
 * Based on the input text, the value of one or both keys may be empty.
 *
 * @uses \Conjoon_Text_Parser
 * @category   Text
 * @package    Conjoon_Text
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class MessageContentParser extends \Conjoon_Text_Parser {

    const ICONV_OLD   = 'old';
    const ICONV_UTF_8 = 'UTF-8';

    /**
     * @var array
     */
    protected $_oldEncodings = array();

    protected $_lastIconvError;

    protected $_attachmentCounter = 0;

    /**
     * @inherit Conjoon_Text_Parser::parse
     */
    public function parse($input)
    {
        $data = array('message' => $input);

        ArgumentCheck::check(array(
            'message' => array(
                'allowEmpty' => false,
                'type'       => 'string'
            )
        ), $data);

        $message = $data['message'];

        if (strpos((string) $input, "\n\n") === false) {
            throw new InvalidArgumentException(
                "Malformed message. Could not find splitter."
            );
        }

        $parts = explode("\n\n", $message, 2);

        if (count($parts) != 2) {
            return array(
                'contentTextPlain' => '',
                'contentTextHtml'  => ''
            );
        }

        $this->_setIconvEncoding(self::ICONV_UTF_8);

        $emailItem = array();

        $message = new \Conjoon_Mail_Message(array(
            'headers'    => $parts[0],
            'noToplines' => true,
            'content'    => $parts[1]
        ));

        $encodingInformation = $this->_getEncodingInformation($message);

        $contentType = $encodingInformation['contentType'];

        try {
            switch ($contentType) {
                case 'text/plain':
                    $emailItem['contentTextPlain'] = $this->_decode(
                        $message->getContent(), $encodingInformation);
                    break;

                case 'text/html':
                    $emailItem['contentTextHtml'] = $this->_decode(
                        $message->getContent(), $encodingInformation);
                    break;

                case 'multipart/mixed':
                    $this->_parseMultipartMixed($message, $emailItem);
                    break;

                case 'multipart/alternative':
                    $this->_parseMultipartAlternative($message, $emailItem);
                    break;

                case 'multipart/related':
                    $this->_parseMultipartRelated($message, $emailItem);
                    break;

                case 'multipart/signed':
                    $this->_parseMultipartSigned($message, $emailItem);
                    break;

                case 'multipart/report':
                    $this->_parseMultipartReport($message, $emailItem);
                    break;

                default:
                    $emailItem['contentTextPlain'] = $this->_decode(
                        $message->getContent(), $encodingInformation);
                    break;
            }
        } catch (\Exception $e) {

            throw new \Conjoon_Text_Parser_Exception(
                "Exception thrown by previous exception: " . $e->getMessage(),
                0, $e
            );

        }


        if (!isset($emailItem['contentTextPlain'])) {
            $emailItem['contentTextPlain'] = '';
        }

        if (!isset($emailItem['contentTextHtml'])) {
            $emailItem['contentTextHtml'] = '';
        }

        $this->_setIconvEncoding(self::ICONV_OLD);

        return $emailItem;
    }

    /**
     *
     * @param $message
     *
     * @return array
     */
    protected function _getEncodingInformation($message)
    {
        $contentTransferEncoding = "";
        $charset                 = "";
        $contentType             = "";
        // also look up name for attachments
        $name = "";

        try {
            $contentTransferEncoding = $message->contentTransferEncoding;
        } catch (\Zend_Mail_Exception $e) {
            //
        }

        try {
            $contentType = $message->contentType;

            if (strpos($contentType, ';') !== false) {
                $contentType = strtok($message->contentType, ';');

                while (($value = strtok(';'))!== false) {
                    $value = trim($value);
                    if (strpos($value, 'charset') === 0) {
                        $charset = trim($value);
                    } else if (strpos($value, 'name') === 0) {
                        $name = trim($value);
                    }
                }

                if ($charset != "") {
                    // probably no ";" used as separator, but line-feed or space.
                    if (strpos($charset, "\r\n") !== false || strpos($charset, "\n") !== false
                        || strpos($charset, "\r") !== false || strpos($charset, " ") !== false) {
                        $sep = "__IB_".time()."_EOL__";
                        $charset = str_replace(array(" ","\r\n", "\n", "\r"), $sep, $charset);
                        $charsets = explode($sep, $charset);
                        $charset = $charsets[0];
                    }
                    $charset = str_replace(array('charset=', '"', "'"), '' , $charset);
                }
                if ($name != "") {
                    $name = str_replace(array('name=', '"', "'"), '' , $name);
                }
            }

            if ($name == "") {
                try {
                    $contentDisposition = $message->contentDisposition;

                    if ($contentDisposition && strpos($contentDisposition, ';') !== false) {
                        $p = explode(';', $contentDisposition, 2);
                        if(isset($p[1])) {
                            $n = trim($p[1]);
                            if (strpos($n, "name=") === 0) {
                                $n    = substr($n, 5);
                                $name = trim($n, "\"'");
                            } else if (strpos($n, "filename=") === 0) {
                                $n    = substr($n, 9);
                                $name = trim($n, "\"'");
                            }
                        }
                    }
                } catch (\Zend_Mail_Exception $e) {
                    //
                }
            }

        } catch (\Zend_Mail_Exception $e) {
            // ignore
        }

        return array(
            'contentType'             => strtolower($contentType),
            'charset'                 => strtolower($charset),
            'name'                    => $name,
            'contentTransferEncoding' => strtolower($contentTransferEncoding)
        );
    }

    protected function _setIconvErrorHandler()
    {
        $this->_lastIconvError = false;
        set_error_handler(array($this, '_iconvErrorHandler'));
    }

    protected function _restoreErrorHandler()
    {
        $this->_lastIconvError = false;
        restore_error_handler();
    }


    protected function _iconvErrorHandler()
    {
        $this->_lastIconvError = true;
    }

    protected function _getEncodingList()
    {
        return 'UCS-4, UCS-4BE, UCS-4LE, UCS-2, UCS-2BE, UCS-2LE, UTF-32, UTF-32BE, UTF-32LE, UTF-16, UTF-16BE, UTF-16LE, UTF-8, UTF-7, UTF7-IMAP,  ASCII, EUC-JP, SJIS, eucJP-win, CP51932, JIS, ISO-2022-JP,  ISO-2022-JP-MS, Windows-1252, ISO-8859-1, ISO-8859-2, ISO-8859-3, ISO-8859-4,  ISO-8859-5, ISO-8859-6, ISO-8859-7, ISO-8859-8, ISO-8859-9, ISO-8859-10, ISO-8859-13,  ISO-8859-14, ISO-8859-15, ISO-8859-16, EUC-CN, CP936, HZ, EUC-TW, BIG-5, EUC-KR,  UHC, ISO-2022-KR, Windows-1251, CP866, KOI8-R, ArmSCII-8';
    }

    /**
     *
     * @param $text
     * @param array $encodingInformation
     *
     * @return bool|string
     */
    protected function _decode($text, array $encodingInformation)
    {
        $charset                 = $encodingInformation['charset'];
        $contentTransferEncoding = $encodingInformation['contentTransferEncoding'];

        switch ($contentTransferEncoding) {
            case 'base64':
                $text = base64_decode($text);
                break;
            case 'quoted-printable':
                $text = quoted_printable_decode($text);
                break;
        }

        // try to replace those curved quotes with their correct entities!
        // see http://en.wikipedia.org/wiki/Quotation_mark_glyphs
        // [quote]
        // A few mail clients send curved quotes using the windows-1252 codes,
        // but mark the text as ISO-8859-1, causing problems for decoders that
        // do not make the dubious assumption that C1 control codes in ISO-8859-1
        // text were meant to be windows-1252 printable characters
        // [/quote]
        if (strtolower($charset) == 'iso-8859-1') {
            $charset = 'windows-1252';
        }

        $this->_setIconvErrorHandler();
        if ($charset != "") {
            $conv = iconv($charset, 'UTF-8', $text);

            // first off, check if the charset is windows-1250 if  encoding fails
            // broaden to windows-1252 then
            if (($conv === false || $this->_lastIconvError) && strtolower($charset) == 'windows-1250') {
                $this->_lastIconvError = false;
                $conv = iconv('windows-1252', 'UTF-8', $text);
            }

            // check if the charset is us-ascii and broaden to windows-1252
            // if encoding attempt fails
            if (($conv === false || $this->_lastIconvError) && strtolower($charset) == 'us-ascii') {
                $this->_lastIconvError = false;
                $conv = iconv('windows-1252', 'UTF-8', $text);
            }

            // fallback! if we have mb-extension installed, we'll try to detect the encoding, if
            // first try with iconv didn't work
            if (($conv === false || $this->_lastIconvError) && function_exists('mb_detect_encoding')) {
                $this->_lastIconvError = false;
                $peekEncoding = mb_detect_encoding($text, $this->_getEncodingList(), true);
                $conv = iconv($peekEncoding, 'UTF-8', $text);
            }
            if ($conv === false || $this->_lastIconvError) {
                $this->_lastIconvError = false;
                $conv = iconv($charset, 'UTF-8//TRANSLIT', $text);
            }
            if ($conv === false || $this->_lastIconvError) {
                $this->_lastIconvError = false;
                $conv = iconv($charset, 'UTF-8//IGNORE', $text);
            }
            if ($conv !== false && !$this->_lastIconvError) {
                $text = $conv;
            }

        } else {
            $conv = false;
            if (function_exists('mb_detect_encoding')) {
                $this->_lastIconvError = false;
                $peekEncoding = mb_detect_encoding($text, $this->_getEncodingList(), true);
                $conv = iconv($peekEncoding, 'UTF-8', $text);
            }
            if ($conv === false || $this->_lastIconvError) {
                $this->_lastIconvError = false;
                $conv = iconv('UTF-8', 'UTF-8//IGNORE', $text);
            }
            if ($conv !== false && !$this->_lastIconvError) {
                $text = $conv;
            }
        }
        $this->_restoreErrorHandler();

        return $text;
    }

    /**
     *
     * @param $message
     * @param $emailItem
     *
     */
    private function _parseMultipartMixed($message, &$emailItem)
    {
        $len = $message->countParts()+1;

        for ($i = 1; $i < $len; $i++) {
            $part = $message->getPart($i);

            $encodingInformation = $this->_getEncodingInformation($part);
            $contentType         = $encodingInformation['contentType'];

            // skip to attachments if encodingInformation detects "name" value
            if (isset($encodingInformation['name']) && $encodingInformation['name'] != "") {
                $contentType = "___";
            }

            switch ($contentType) {
                case 'text/plain':
                    if (!isset($emailItem['contentTextPlain'])) {
                        $emailItem['contentTextPlain'] = $this->_decode($part->getContent(), $encodingInformation);
                    } else {
                        $this->_parseAttachments($part, $emailItem);;
                    }
                    break;

                case 'text/html':
                    if (!isset($emailItem['contentTextHtml'])) {
                        $emailItem['contentTextHtml'] = $this->_decode($part->getContent(), $encodingInformation);
                    } else {
                        $this->_parseAttachments($part, $emailItem);;
                    }
                    break;

                case 'multipart/related':
                    $this->_parseMultipartRelated($part, $emailItem);
                    break;

                case 'multipart/alternative':
                    $this->_parseMultipartAlternative($part, $emailItem);
                    break;

                default:
                    $this->_parseAttachments($part, $emailItem);
                    break;
            }
        }
    }

    /**
     *
     * @param $message
     * @param $emailItem
     * @return mixed
     *
     * @throws \Zend_Mail_Exception
     */
    private function _parseMultipartAlternative($message, &$emailItem)
    {
        try {
            $len = $message->countParts()+1;
        } catch (\Zend_Exception $e) {
            /**
             * @todo Zed_Mime_decode does not throw a Zen_Mime_Exception, but a
             * Zend_Exception if the boundaries end is missing.
             * submit a bug report for this
             */
            try {
                // this is a fallback for "end is missing", if a mime message does not contain
                // the closing boundary
                $ct       = $message->getContent();
                $boundary = $message->getHeaderField('content-type', 'boundary');
                if ($boundary) {
                    $p = strpos($ct, '--' . $boundary . '--');
                    if ($p === false) {
                        $ct .= "\r\n" . '--' . $boundary . '--';
                        $message = new \Conjoon_Mail_Message(array(
                            'headers'    => 'Content-Type: '
                                . $message->contentType,
                            'noTopLines' => true,
                            'content'    => $ct
                        ));

                        $len = 2;
                    }
                } else {
                    throw new \Zend_Mail_Exception('');
                }
            } catch (\Zend_Mail_Exception $e) {
                $encodingInformation = $this->_getEncodingInformation($message);
                $contentType         = $encodingInformation['contentType'];
                if ($contentType == 'text/plain') {
                    $emailItem['contentTextPlain'] = $this->_decode($ct, $encodingInformation);
                } else if ($contentType == 'text/html') {
                    $emailItem['contentTextHtml'] = $this->_decode($ct, $encodingInformation);
                }
                return;
            }
        }

        for ($i = 1; $i < $len; $i++) {
            $part = $message->getPart($i);

            $encodingInformation = $this->_getEncodingInformation($part);
            $contentType         = $encodingInformation['contentType'];

            switch ($contentType) {
                case 'text/plain':
                    if (!isset($emailItem['contentTextPlain'])) {
                        $emailItem['contentTextPlain'] = $this->_decode($part->getContent(), $encodingInformation);
                    } else {
                        $this->_parseAttachments($part, $emailItem);
                    }
                    break;

                case 'text/html':
                    if (!isset($emailItem['contentTextHtml'])) {
                        $emailItem['contentTextHtml'] = $this->_decode($part->getContent(), $encodingInformation);
                    } else {
                        $this->_parseAttachments($part, $emailItem);
                    }
                    break;

                case 'multipart/related':
                    $this->_parseMultipartRelated($part, $emailItem);
                    break;
            }
        }
    }

    /**
     *
     * @param $message
     * @param $emailItem
     *
     */
    private function _parseMultipartSigned($message, &$emailItem)
    {
        $len = $message->countParts()+1;

        for ($i = 1; $i < $len; $i++) {
            $part = $message->getPart($i);

            $encodingInformation = $this->_getEncodingInformation($part);
            $contentType         = $encodingInformation['contentType'];

            switch ($contentType) {
                case 'text/plain':
                    if (!isset($emailItem['contentTextPlain'])) {
                        $emailItem['contentTextPlain'] = $this->_decode($part->getContent(), $encodingInformation);
                    } else {
                        $this->_parseAttachments($part, $emailItem);
                    }
                    break;

                case 'text/html':
                    if (!isset($emailItem['contentTextHtml'])) {
                        $emailItem['contentTextHtml'] = $this->_decode($part->getContent(), $encodingInformation);
                    } else {
                        $this->_parseAttachments($part, $emailItem);
                    }
                    break;

                case 'multipart/alternative':
                    $this->_parseMultipartAlternative($part, $emailItem);
                    break;

                default:
                    $this->_parseAttachments($part, $emailItem);
                    break;
            }
        }
    }

    /**
     *
     * @param $message
     * @param $emailItem
     *
     */
    private function _parseMultipartReport($message, &$emailItem)
    {
        $len = $message->countParts()+1;

        $defCharsetForDeliveryStatus = null;
        for ($i = 1; $i < $len; $i++) {
            $part = $message->getPart($i);

            $encodingInformation = $this->_getEncodingInformation($part);
            $contentType         = $encodingInformation['contentType'];

            switch ($contentType) {
                case 'text/plain':
                    if (!isset($emailItem['contentTextPlain'])) {
                        $defCharsetForDeliveryStatus = $encodingInformation['charset'];
                        $emailItem['contentTextPlain'] = $this->_decode($part->getContent(), $encodingInformation);
                    } else {
                        $this->_parseAttachments($part, $emailItem);
                    }
                    break;

                case 'text/html':
                    if (!isset($emailItem['contentTextHtml'])) {
                        $emailItem['contentTextHtml'] = $this->_decode($part->getContent(), $encodingInformation);
                    } else {
                        $this->_parseAttachments($part, $emailItem);
                    }
                    break;

                default:
                    $this->_parseAttachments($part, $emailItem);
                    break;
            }
        }
    }

    /**
     *
     * @param $message
     * @param $emailItem
     *
     */
    private function _parseMultipartRelated($message, &$emailItem)
    {
        $len = $message->countParts()+1;

        for ($i = 1; $i < $len; $i++) {
            $part = $message->getPart($i);

            $encodingInformation = $this->_getEncodingInformation($part);
            $contentType         = $encodingInformation['contentType'];

            switch ($contentType) {
                case 'text/plain':
                    if (!isset($emailItem['contentTextPlain'])) {
                        $emailItem['contentTextPlain'] = $this->_decode($part->getContent(), $encodingInformation);
                    } else {
                        $this->_parseAttachments($part, $emailItem);
                    }
                    break;

                case 'text/html':
                    if (!isset($emailItem['contentTextHtml'])) {
                        $emailItem['contentTextHtml'] = $this->_decode($part->getContent(), $encodingInformation);
                    } else {
                        $this->_parseAttachments($part, $emailItem);
                    }
                    break;

                case 'multipart/alternative':
                    $this->_parseMultipartAlternative($part, $emailItem);
                    break;

                default:
                    $this->_parseAttachments($part, $emailItem);
                    break;
            }
        }
    }

    private function _parseAttachments($part, &$emailItem)
    {
        $encodingInformation = $this->_getEncodingInformation($part);

        $fileName                = $encodingInformation['name'];
        $contentType             = $encodingInformation['contentType'];
        $contentTransferEncoding = $encodingInformation['contentTransferEncoding'];

        if ($contentType == 'message/rfc822' || $contentType == 'rfc822') {
            try {
                $nm = new \Conjoon_Mail_Message(array('raw' => $part->getContent()));
                $n = $nm->subject;

                /**
                 * @see \Conjoon_Text_Transformer_MimeDecoder
                 */
                require_once 'Conjoon/Text/Transformer/MimeDecoder.php';

                $transformer = new \Conjoon_Text_Transformer_MimeDecoder();


                $fileName = $transformer->transform($n).'.eml';
            } catch (\Zend_Mail_Exception $e) {
                // ignore
            }
        }

        if ($fileName === "") {
            $fileName = 'attachment['.($this->_attachmentCounter++).']';
        }

        try {
            $contentId = $part->contentId;
        } catch (\Zend_Mail_Exception $e) {
            $contentId = "";
        }

        $emailItem['attachments'][] = array(
            'mimeType'  => $contentType,
            'encoding'  => $contentTransferEncoding,
            'content'   => $part->getContent(),
            'fileName'  => $fileName,
            'contentId' => $contentId
        );

    }


    /**
     * Sets the iconv-internal-encodings, since Zend_Mime does not allow
     * for passing an indivdual charset for decoding.
     * This is a simple helper which allows for either setting the encoding
     * to utf-8 or reset the endoding to the old value.
     *
     * @param string $type
     *
     */
    protected function _setIconvEncoding($type)
    {
        if ($type != self::ICONV_UTF_8) {
            if (!empty($this->_oldEncodings)) {
                iconv_set_encoding('input_encoding',    $this->_oldEncodings['input_encoding']);
                iconv_set_encoding('output_encoding',   $this->_oldEncodings['output_encoding']);
                iconv_set_encoding('internal_encoding', $this->_oldEncodings['internal_encoding']);
            }
        } else {
            if(empty($this->_oldEncodings)) {
                $this->_oldEncodings = array(
                    'input_encoding'    => iconv_get_encoding('input_encoding'),
                    'output_encoding'   => iconv_get_encoding('output_encoding'),
                    'internal_encoding' => iconv_get_encoding('internal_encoding')
                );
            }

            iconv_set_encoding('input_encoding',    'UTF-8');
            iconv_set_encoding('output_encoding',   'UTF-8');
            iconv_set_encoding('internal_encoding', 'UTF-8');
        }
    }

}