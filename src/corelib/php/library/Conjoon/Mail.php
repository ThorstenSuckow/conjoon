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
            $this->_replyTo = $replyTo;
            $this->addHeader('Reply-To', $replyTo);
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
            $this->_references = $references;
            $this->addHeader('References', $references, false);
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
     * Sets the in-reply-to header for an email
     *
     * @param  string    $inReplyTo
     * @return Zend_Mail Provides fluent interface
     * @throws Zend_Mail_Exception if set multiple times
     */
    public function setInReplyTo($inReplyTo)
    {
        if ($this->_inReplyTo === null) {
            $this->_inReplyTo = $inReplyTo;
            $this->addHeader('In-Reply-To', $inReplyTo);
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
     * Formats e-mail address.
     * Overwritten so we can strip previously added single quotes as a replacement
     * for double quotes.
     * single quotes will only be stripped if there are only single quotes at the start
     * and the end of the string.
     *
     * @param string $email
     * @param string $name
     * @return string
     */
    protected function _formatAddress($email, $name)
    {
        if ($name === '' || $name === null || $name === $email) {
            return $email;
        } else {

            if (substr($name, 0, 1) == "'" && substr($name, -1) == "'" && substr_count($name, "'") == 2) {
                $name = trim($name, "'");
            }

            $encodedName = $this->_encodeHeader($name);
            if ($encodedName === $name && strpos($name, ',') !== false) {
                $format = '"%s" <%s>';
            } else {
                $format = '%s <%s>';
            }
            return sprintf($format, $encodedName, $email);
        }
    }

}
