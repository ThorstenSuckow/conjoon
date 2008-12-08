<?php
/**
 * conjoon
 * (c) 2002-2009 siteartwork.de/conjoon.org
 * licensing@conjoon.org
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
 * @see Zend_Mail
 */
require_once 'Zend/Mail.php';


/**
 * @uses Zend_Mail
 */
class Conjoon_Mail extends Zend_Mail {

    protected $_replyTo = null;

    protected $_references = null;

    protected $_inReplyTo = null;

    protected $_messageId = null;

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
     * Sets the references header for an email
     *
     * @param  string    $references
     * @return Zend_Mail Provides fluent interface
     * @throws Zend_Mail_Exception if set multiple times
     */
    public function setReferences($references)
    {
        if ($this->_references === null) {
            $references = strtr($references, "\r\n\t", '???');
            $this->_references = $references;
            $this->_storeHeader('References', $references, false);
        } else {
            /**
             * @see Zend_Mail_Exception
             */
            require_once 'Zend/Mail/Exception.php';
            throw new Zend_Mail_Exception('References Header set twice');
        }
        return $this;
    }


    /**
     * Returns the references header field.
     *
     * @param  boolean $plain true to return the value as stored in
     * the $_references-property, otherwise the encoded value
     *
     * @return string
     */
    public function getReferences($plain = true)
    {
        if ($this->_references === null) {
            return null;
        }

        if ($plain) {
            return $this->_references;
        }

        $headers = $this->getHeaders();

        return $headers['References'];
    }

    /**
     * Sets the Message-id header for an email
     *
     * @param  string    $messageId
     * @return Zend_Mail Provides fluent interface
     * @throws Zend_Mail_Exception if set multiple times
     */
    public function setMessageId($messageId)
    {
        if ($this->_messageId === null) {
            $this->_messageId = $messageId;
            $this->_storeHeader('Message-ID', $messageId, false);
        } else {
            /**
             * @see Zend_Mail_Exception
             */
            require_once 'Zend/Mail/Exception.php';
            throw new Zend_Mail_Exception('Message-ID Header set twice');
        }
        return $this;
    }


    /**
     * Returns the Message-ID header field.
     *
     * @return string
     */
    public function getMessageId()
    {
        if ($this->_messageId === null) {
            return null;
        }

        $headers = $this->getHeaders();

        return $headers['Message-ID'];
    }

    /**
     * Sets the in-reply-to header for an email
     *
     * @param  string    $inReplyTo
     * @return Zend_Mail Provides fluent interface
     * @throws Zend_Mail_Exception if set multiple times
     */
    public function setInReplyTo($inReplyTo)
    {
        if ($this->_inReplyTo === null) {
            $inReplyTo = strtr($inReplyTo, "\r\n\t", '???');
            $this->_inReplyTo = $inReplyTo;
            $this->_storeHeader('In-Reply-To', $inReplyTo, false);
        } else {
            /**
             * @see Zend_Mail_Exception
             */
            require_once 'Zend/Mail/Exception.php';
            throw new Zend_Mail_Exception('In-Reply-To Header set twice');
        }
        return $this;
    }


    /**
     * Returns the references header field.
     *
     * @param  boolean $plain true to return the value as stored in
     * the $_references-property, otherwise the encoded value
     *
     * @return string
     */
    public function getInReplyTo($plain = true)
    {
        if ($this->_inReplyTo === null) {
            return null;
        }

        if ($plain) {
            return $this->_inReplyTo;
        }

        $headers = $this->getHeaders();

        return $headers['In-Reply-To'];
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

    /**
     * Helper function for adding a recipient and the corresponding header
     * Overriden since ZF1.6.1 would add quotes to the name of the recipient
     * no matter what. Since the conjoon framework passes names to an
     * instance of this class already quoted (even with escaped quotes if
     * necessary, this implementation checks first if quotes need to be used
     * and quotes after that accordingly.     *
     *
     * @param string $headerName
     * @param string $name
     * @param string $email
     */
    protected function _addRecipientAndHeader($headerName, $name, $email)
    {
        $email = strtr($email,"\r\n\t",'???');
        $this->_addRecipient($email, ('To' == $headerName) ? true : false);
        if ($name != '') {
            $name = $this->_quoteIfNecessary($name) . ' ';
        }

        $this->_storeHeader($headerName, $name .'<'. $email . '>', true);
    }

    /**
     * Sets From-header and sender of the message
     * Overidden to check if the name of the sender is already quoted, so the name
     * part would not accidently be quoted again.
     *
     *
     * @param  string    $email
     * @param  string    $name
     * @return Zend_Mail Provides fluent interface
     * @throws Zend_Mail_Exception if called subsequent times
     */
    public function setFrom($email, $name = '')
    {
        if ($this->_from === null) {
            $email = strtr($email,"\r\n\t",'???');
            $this->_from = $email;
            if ($name != '') {
                $name = $this->_quoteIfNecessary($name). ' ';
            }
            $this->_storeHeader('From', $name.'<'.$email.'>', true);
        } else {
            /**
             * @see Zend_Mail_Exception
             */
            require_once 'Zend/Mail/Exception.php';
            throw new Zend_Mail_Exception('From Header set twice');
        }
        return $this;
    }

    protected function _quoteIfNecessary($value)
    {
        if (substr($value, 0, 1) != '"' || substr($value, -1) != '"') {
            $value = '"' . $this->_encodeHeader($value) . '"';
        }

        return $value;
    }

}
