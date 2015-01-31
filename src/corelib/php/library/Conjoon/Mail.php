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
 * @see Zend_Mail
 */
require_once 'Zend/Mail.php';


/**
 * Note:
 * When querying the message id by using the accessor getMessageId(), the value
 * is returned without being it wrapped in "<", ">"
 *
 *
 * @uses Zend_Mail
 */
class Conjoon_Mail extends Zend_Mail {

    protected $_references = null;

    protected $_inReplyTo = null;

    protected $_messageId = null;

    /**
     * Sets the references header for an email
     * This method also provides functionality to ensure that the passed
     * string is splitted properly if it is longer than 998 characters.
     *
     * @param  string    $references
     * @return Zend_Mail Provides fluent interface
     * @throws Zend_Mail_Exception if set multiple times
     */
    public function setReferences($references)
    {
        if ($this->_references === null) {
            $this->_references = $references;
            $this->addHeader('References', $references , false);
            // after implicit encoding from addHeader, apply wordwrap to make
            // sure the lines for this header field are no longer than
            // 998 characters (excl. linefeed)
            $this->_headers['References'] = array(wordwrap(
                $this->getReferences($references), 77, "\n\t"
            ));
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

        return $headers['References'][0];
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
