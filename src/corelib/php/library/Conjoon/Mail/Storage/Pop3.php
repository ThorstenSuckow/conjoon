<?php
/**
 * conjoon
 * (c) 2002-2010 siteartwork.de/conjoon.org
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
 * Fix for http://framework.zend.com/issues/browse/ZF-3318
 *
 * This class uses custom implementation of message class until fix for above
 * bug gets released.
 *
 * The class uses Conjoon_Mail_Protocol_Pop3 as the default driver for
 * POP3 connections
 */
class Conjoon_Mail_Storage_Pop3 extends Zend_Mail_Storage_Pop3 {

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
     * @throws Zend_Mail_Storage_Exception
     * @throws Zend_Mail_Protocol_Exception
     */
    public function __construct($params)
    {
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
    }


    /*
     * Get raw messagee
     *
     * @param  int $id   number of message
     * @return string raw message
     * @throws Zend_Mail_Protocol_Exception
     */
    public function getRawMessage($id)
    {
        $content = $this->_protocol->retrieve($id);
        return $content;
    }

}