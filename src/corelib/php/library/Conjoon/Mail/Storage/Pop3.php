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
* @see Zend_Mail_Storage_Pop3
*/
require_once 'Zend/Mail/Storage/Pop3.php';

/**
* @see Conjoon_Mail_Message
*/
require_once 'Conjoon/Mail/Message.php';

/**
* @see Conjoon_Mail_Protocol_Pop3
*/
require_once 'Conjoon/Mail/Protocol/Pop3.php';

/**
 * @see Conjoon_Mail_Service_Pop3StorageService
 */
require_once 'Conjoon/Mail/Service/Pop3StorageService.php';

/**
 * Fix for http://framework.zend.com/issues/browse/ZF-3318
 *
 * This class uses custom implementation of message class until fix for above
 * bug gets released.
 *
 * The class uses Conjoon_Mail_Protocol_Pop3 as the default driver for
 * POP3 connections
 */
class Conjoon_Mail_Storage_Pop3 extends Zend_Mail_Storage_Pop3
    implements Conjoon_Mail_Service_Pop3StorageService {

    /**
     * used message class, change it in an extened class to extend the returned message class
     * @var string
     */
    protected $_messageClass = 'Conjoon_Mail_Message';


    /**
     * create instance with parameters
     * Supported paramters are
     *   - host hostname or ip address of POP3 server
     *   - user username
     *   - password password for user 'username' [optional, default = '']
     *   - port port for POP3 server [optional, default = 110]
     *   - ssl 'SSL' or 'TLS' for secure sockets
     *
     * @param  $params array  mail reader specific parameters
     *
     * @throws Conjoon_Mail_Service_MailServiceException
     */
    public function __construct($params)
    {
        try {
            if (is_array($params)) {
                $params = (object)$params;
            }

            $this->_has['fetchPart'] = false;
            $this->_has['top']       = null;
            $this->_has['uniqueid']  = null;

            if ($params instanceof Conjoon_Mail_Protocol_Pop3) {
                $this->_protocol = $params;
                return;
            }

            if (!isset($params->user)) {
                /**
                 * @see Zend_Mail_Storage_Exception
                 */
                require_once 'Zend/Mail/Storage/Exception.php';
                throw new Zend_Mail_Storage_Exception('need at least user in params');
            }

            $host     = isset($params->host)     ? $params->host     : 'localhost';
            $password = isset($params->password) ? $params->password : '';
            $port     = isset($params->port)     ? $params->port     : null;
            $ssl      = isset($params->ssl)      ? $params->ssl      : false;

            $this->_protocol = new Conjoon_Mail_Protocol_Pop3();
            $this->_protocol->connect($host, $port, $ssl);
            $this->_protocol->login($params->user, $password);

        } catch (Zend_Mail_Exception $e) {
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

        try {
            $content = $this->_protocol->retrieve($id);
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

}