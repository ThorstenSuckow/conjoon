<?php
/**
 * intrabuild
 * (c) 2002-2008 siteartwork.de/MindPatterns
 * license@siteartwork.de
 *
 * $Author: T. Suckow $
 * $Id: Message.php 160 2008-09-21 11:54:33Z T. Suckow $
 * $Date: 2008-09-21 13:54:33 +0200 (So, 21 Sep 2008) $
 * $Revision: 160 $
 * $LastChangedDate: 2008-09-21 13:54:33 +0200 (So, 21 Sep 2008) $
 * $LastChangedBy: T. Suckow $
 * $URL: file:///F:/svn_repository/intrabuild_rep/trunk/src/corelib/php/library/Intrabuild/Mail/Message.php $
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
