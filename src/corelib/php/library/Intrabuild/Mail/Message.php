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
 * Zend_Mail_Message
 */
require_once 'Zend/Mail/Message.php';


/**
 * Fix for http://framework.zend.com/issues/browse/ZF-3318
 *
 * This class will be used as default Message-class for Zend_Mail until above 
 * bug has been fixed.
 */
class Intrabuild_Mail_Message extends Zend_Mail_Message {
    
    /**
     * Cache content and split in parts if multipart
     *
     * @return null
     * @throws Zend_Mail_Exception
     */
    protected function _cacheContent()
    {
        // caching content if we can't fetch parts
        if ($this->_content === null && $this->_mail) {
            $this->_content = $this->_mail->getRawContent($this->_messageNum);
        }

        if (!$this->isMultipart()) {
            return;
        }

        // split content in parts
        // fix! @see http://framework.zend.com/issues/browse/ZF-3318
        $boundary = trim(str_replace('"', "", $this->getHeaderField('content-type', 'boundary')));
        if (!$boundary) {
            /**
             * @see Zend_Mail_Exception
             */
            require_once 'Zend/Mail/Exception.php';
            throw new Zend_Mail_Exception('no boundary found in content type to split message');
        }
        $parts = Zend_Mime_Decode::splitMessageStruct($this->_content, $boundary);
        $counter = 1;
        foreach ($parts as $part) {
            $this->_parts[$counter++] = new self(array('headers' => $part['header'], 'content' => $part['body']));
        }
    }    
    
}
