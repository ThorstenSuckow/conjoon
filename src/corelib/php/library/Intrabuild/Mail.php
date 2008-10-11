<?php
/**
 * intrabuild
 * (c) 2002-2008 siteartwork.de/MindPatterns
 * license@siteartwork.de
 *
 * $Author: T. Suckow $
 * $Id: Message.php 73 2008-08-21 22:15:14Z T. Suckow $
 * $Date: 2008-08-22 00:15:14 +0200 (Fr, 22 Aug 2008) $
 * $Revision: 73 $
 * $LastChangedDate: 2008-08-22 00:15:14 +0200 (Fr, 22 Aug 2008) $
 * $LastChangedBy: T. Suckow $
 * $URL: file:///F:/svn_repository/intrabuild/trunk/src/corelib/php/library/Intrabuild/Mail/Message.php $
 */


/**
 * @see Zend_Mail
 */
require_once 'Zend/Mail.php';


/**
 * @uses Zend_Mail
 */
class Intrabuild_Mail extends Zend_Mail {

    protected $_replyTo = null;

    /**
     * Sets the Reply-to header for an email
     *
     * @param  string    $replyTo
     * @return Zend_Mail Provides fluent interface
     * @throws Zend_Mail_Exception if set multiple times
     */
    public function setReplyTo($replyTo)
    {
        if ($this->_replyTo === null) {
            $replyTo = strtr($replyTo, "\r\n\t", '???');
            $this->_replyTo = $replyTo;
            $this->_storeHeader('Reply-To', $replyTo, false);
        } else {
            /**
             * @see Zend_Mail_Exception
             */
            require_once 'Zend/Mail/Exception.php';
            throw new Zend_Mail_Exception('Reply-To Header set twice');
        }
        return $this;
    }

    /**
     * Returns the reply-to header field.
     *
     * @param  boolean $plain true to return the value as stored in
     * the $_replyTo-property, otherwise the encoded value
     *
     * @return string
     */
    public function getReplyTo($plain = true)
    {
        if ($this->_replyTo === null) {
            return null;
        }

        if ($plain) {
            return $this->_replyTo;
        }

        $headers = $this->getHeaders();

        return $headers['Reply-To'];
    }

    /**
     * Fix for ZF1688
     * Encode header fields
     *
     * Encodes header content according to RFC1522 if it contains non-printable
     * characters.
     *
     * @param  string $value
     * @return string
     */
    protected function _encodeHeader($value)
    {
        if (Zend_Mime::isPrintable($value)) {
            return $value;
        } else {

            $mimePrefs = array(
                'scheme'           => 'Q',
                'input-charset'    => $this->_charset,
                'output-charset'   => $this->_charset,
                'line-length'      => 74,
                'line-break-chars' => "\n"
            );

            $value = iconv_mime_encode('DUMMY', $value, $mimePrefs);
            $value = preg_replace("#^DUMMY\:\ #", "", $value);

            return $value;
        }
    }


}
