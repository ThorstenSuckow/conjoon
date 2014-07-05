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

/**
* @see Zend_Mail_Storage_Imap
*/
require_once 'Zend/Mail/Storage/Imap.php';

/**
* @see Conjoon_Mail_Message
*/
require_once 'Conjoon/Mail/Message.php';

/**
 * @see Conjoon_Mail_Service_ImapStorageService
 */
require_once 'Conjoon/Mail/Service/ImapStorageService.php';


/**
 * Fix for http://framework.zend.com/issues/browse/ZF-3318
 *
 * This class uses custom implementation of message class until fix for above
 * bug gets released.
 */
class Conjoon_Mail_Storage_Imap extends Zend_Mail_Storage_Imap
    implements Conjoon_Mail_Service_ImapStorageService {

    /**
     * used message class, change it in an extened class to extend the returned message class
     * @var string
     */
    protected $_messageClass = 'Conjoon_Mail_Message';

    /**
     * Zend Framework does not return server response
     *
     * append a new message to mail storage
     *
     * @param  string                                     $message message as string or instance of message class
     * @param  null|string|Zend_Mail_Storage_Folder       $folder  folder for new message, else current folder is taken
     * @param  null|array                                 $flags   set flags for new message, else a default set is used
     * @throws Zend_Mail_Storage_Exception
     */
     // not yet * @param string|Zend_Mail_Message|Zend_Mime_Message $message message as string or instance of message class
    public function appendMessage($message, $folder = null, $flags = null)
    {
        if ($folder === null) {
            $folder = $this->_currentFolder;
        }

        if ($flags === null) {
            $flags = array(Zend_Mail_Storage::FLAG_SEEN);
        }

        $res = $this->_protocol->append($folder, $message, $flags);

        // TODO: handle class instances for $message
        if (!$res) {
            /**
             * @see Zend_Mail_Storage_Exception
             */
            require_once 'Zend/Mail/Storage/Exception.php';
            throw new Zend_Mail_Storage_Exception('cannot create message, please check if the folder exists and your flags');
        }

        return $res;
    }

    /**
     * Zend Framework does not return server response
     *
     * copy an existing message
     *
     * @param  int                             $id     number of message
     * @param  string|Zend_Mail_Storage_Folder $folder name or instance of targer folder
     * @return null
     * @throws Zend_Mail_Storage_Exception
     */
    public function copyMessage($id, $folder)
    {
        $res = $this->_protocol->copy($folder, $id);
        if (!$res) {
            /**
             * @see Zend_Mail_Storage_Exception
             */
            require_once 'Zend/Mail/Storage/Exception.php';
            throw new Zend_Mail_Storage_Exception('cannot copy message, does the folder exist?');
        }

        return $res;
    }

    /**
     * @inheritdoc
     */
    public function getRawMessage($id)
    {
        /**
         * @see Conjoon_Argument_Check
         */
        require_once 'Conjoon/Argument/Check.php';

        $data = array('id' => $id);

        Conjoon_Argument_Check::check(array(
            'id' => array(
                'type'        => 'integer',
                'allowEmpty'  => false,
                'greaterThan' => 0
            )
        ), $data);

        $id = $data['id'];

        try{
            $tmp = $this->_protocol->fetch(
                array('RFC822.HEADER', 'RFC822.TEXT'), $id
            );

            $content =
                $tmp['RFC822.HEADER']
                . "\n\n"
                . $tmp['RFC822.TEXT'];

            return $content;
        } catch (Zend_Mail_Protocol_Exception $e) {
            /**
             * @see Conjoon_Mail_Service_MailServiceException
             */
            require_once 'Conjoon/Mail/Service/MailServiceException.php';

            throw new Conjoon_Mail_Service_MailServiceException(
                "Exception thrown by previous exception: "
                . $e->getMessage(), 0, $e
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function getHeaderListForGlobalName($globalName)
    {
        /**
         * @see Conjoon_Argument_Check
         */
        require_once 'Conjoonn/Argument/Check.php';

        $data = array('globalName' => $globalName);

        Conjoon_Argument_Check::check(array(
            'globalName' => array(
                'type'        => 'string',
                'allowEmpty'  => false
            )
        ), $data);

        $globalName = $data['globalName'];


        try {
            $this->selectFolder($globalName);
        } catch (Zend_Mail_Storage_Exception $e) {
            /**
             * @see Conjoon_Mail_Service_MailServiceException
             */
            require_once 'Conjoon/Mail/Service/MailServiceException.php';

            throw new Conjoon_Mail_Service_MailServiceException(
                "Exception thrown by previous exception: "
                . $e->getMessage(), 0, $e
            );
        }

        $count = $this->countMessages();

        $headers = array();
        for ($i = 1, $len = $count +1; $i < $len; $i++) {

            try {
                $headers[] = $this->_protocol->fetch('RFC822.HEADER', $i);
            } catch (Zend_Mail_Protocol_Exception $e) {
                /**
                 * @see Conjoon_Mail_Service_MailServiceException
                 */
                require_once 'Conjoon/Mail/Service/MailServiceException.php';

                throw new Conjoon_Mail_Service_MailServiceException(
                    "Exception thrown by previous exception: "
                     . $e->getMessage(), 0, $e
                );
            }
        }

        return $headers;
    }

    /**
     * @inheritdoc
     */
    public function getHeaderListAndMetaInformationForGlobalName($globalName, $from = 1, $to = -1)
    {
        /**
         * @see Conjoon_Argument_Check
         */
        require_once 'Conjoon/Argument/Check.php';

        $data = array('globalName' => $globalName);

        Conjoon_Argument_Check::check(array(
            'globalName' => array(
                'type'        => 'string',
                'allowEmpty'  => false
            )
        ), $data);

        $globalName = $data['globalName'];

        try {
            $this->selectFolder($globalName);
        } catch (Zend_Mail_Storage_Exception $e) {
            /**
             * @see Conjoon_Mail_Service_MailServiceException
             */
            require_once 'Conjoon/Mail/Service/MailServiceException.php';

            throw new Conjoon_Mail_Service_MailServiceException(
                "Exception thrown by previous exception: "
                 . $e->getMessage(), 0, $e
            );
        }

        $count = $this->countMessages();
        $to = $to == -1 ? $count : $to;

        $headers = array();

        try {
            $result = $this->_protocol->fetch(
                array('RFC822.HEADER', 'BODYSTRUCTURE', 'FLAGS', 'UID'), $from, $to
            );

        } catch (Zend_Mail_Protocol_Exception $e) {
            /**
             * @see Conjoon_Mail_Service_MailServiceException
             */
            require_once 'Conjoon/Mail/Service/MailServiceException.php';

            throw new Conjoon_Mail_Service_MailServiceException(
                "Exception thrown by previous exception: "
                . $e->getMessage(), 0, $e
            );
        }

        foreach ($result as $tmp) {

            $headers[] = array(
                'header'        => $tmp['RFC822.HEADER'],
                'bodystructure' => $tmp['BODYSTRUCTURE'],
                'flags'         => $tmp['FLAGS'],
                'uid'           => $tmp['UID'],
            );

        }

        return $headers;
    }
}