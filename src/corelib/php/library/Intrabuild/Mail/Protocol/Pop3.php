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
 * Zend_Mail_Message
 */
require_once 'Zend/Mail/Protocol/Pop3.php';


/**
 * Fix for: Zend_Mail_Protocol_Pop3 would not parse termination octets in multiline messages properly
 * (see ZF-3479 http://framework.zend.com/issues/browse/ZF-3479)
 *
 *
 */
class Intrabuild_Mail_Protocol_Pop3 extends Zend_Mail_Protocol_Pop3 {


    /**
     * read a response
     *
     * @param  boolean $multiline response has multiple lines and should be read until "<nl>.<nl>"
     * @return string response
     * @throws Zend_Mail_Protocol_Exception
     */
    public function readResponse($multiline = false)
    {
        $result = @fgets($this->_socket);
        if (!is_string($result)) {
            /**
             * @see Zend_Mail_Protocol_Exception
             */
            require_once 'Zend/Mail/Protocol/Exception.php';
            throw new Zend_Mail_Protocol_Exception('read failed - connection closed?');
        }

        $result = trim($result);
        if (strpos($result, ' ')) {
            list($status, $message) = explode(' ', $result, 2);
        } else {
            $status = $result;
            $message = '';
        }

        if ($status != '+OK') {
            /**
             * @see Zend_Mail_Protocol_Exception
             */
            require_once 'Zend/Mail/Protocol/Exception.php';
            throw new Zend_Mail_Protocol_Exception('last request failed');
        }

        if ($multiline) {
            $message = '';
            $line = fgets($this->_socket);
            while ($line && $line != ".\r\n") {
                $message .= $line;
                $line = fgets($this->_socket);
            };
        }

        return $message;
    }

}
