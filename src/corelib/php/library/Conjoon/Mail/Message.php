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
 * Zend_Mail_Message
 */
require_once 'Zend/Mail/Message.php';

/**
 * @see Conjoon_Vendor_Zend_Mime_Decode
 */
require_once 'Conjoon/Vendor/Zend/Mime/Decode.php';


/**
 * This class is the default Message-class for the conjoon project.
 *
 * @uses Zend_Mail_Message
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Mail_Message extends Zend_Mail_Message {

    /**
     * This method is almost a 1:1 copy of the original implementation.
     * It provides a fix for ZF-10168 by utilizing Conjoon_Vendor_Zend_Mime_Decode
     * @see Conjoon_Vendor_Zend_Mime_Decode
     *
     * Cache content and split in parts if multipart
     *
     * @return null
     * @throws Zend_Mail_Exception
     *
     * Original licensing information of this code:
     * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
     * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
        $boundary = $this->getHeaderField('content-type', 'boundary');
        if (!$boundary) {
            /**
             * @see Zend_Mail_Exception
             */
            require_once 'Zend/Mail/Exception.php';
            throw new Zend_Mail_Exception('no boundary found in content type to split message');
        }

        $parts = Conjoon_Vendor_Zend_Mime_Decode::splitMessageStruct($this->_content, $boundary);
        if ($parts === null) {
            return;
        }
        $partClass = $this->getPartClass();
        $counter = 1;
        foreach ($parts as $part) {
            $this->_parts[$counter++] = new $partClass(array('headers' => $part['header'], 'content' => $part['body']));
        }
    }


}
