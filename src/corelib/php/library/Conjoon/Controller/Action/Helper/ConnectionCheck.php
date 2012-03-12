<?php
/**
 * conjoon
 * (c) 2002-2012 siteartwork.de/conjoon.org
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
 * @see Zend_Controller_Action_Helper_Abstract
 */
require_once 'Zend/Controller/Action/Helper/Abstract.php';

/**
 * This helper checks whether a connection can be established to a specific
 * ip. This helper's intended usage is to be called in actions that may
 * query other domains where a network access is required. To skip expensive
 * request timeouts, this helper can proxy a check for a given ip and if it returns
 * false, the action can be skipped and instead an error message or default data
 * can be returned by the action.
 * The configuration should be done in the bootstrapper for the application, using
 * "setConfig()".
 *
 * NOTE:
 * This helper was developed during my time in the Euro Youth Hostel, Munich.
 * Network access was granted given a very weak WLAN, where connection aborts
 * might occure randomly and frequently. This helper should not be used as a general
 * proxy for checking if network access is available, instead, always implement your
 * business layer in a way this helper wouldn't even be necessary, and proxy this
 * helper afterwards to save yourself the time waiting until a request timeout occures.
 *
 *
 * @uses       Zend_Controller_Action_Helper_Abstract
 * @category   Conjoon
 * @package    Conjoon_Controller
 * @subpackage Conjoon_Controller_Action_Helper
 */
class Conjoon_Controller_Action_Helper_ConnectionCheck extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * @var string $_config
     */
    protected static $_config = array();

    /**
     * Sets the configuration for this class. Should be called statically
     * in the bootstrap so the helper is configured once for every time it
     * is used.
     *
     * @param Array $config An array with the following config options:
     * - ip
     * - timeout
     * - port
     * - enabled
     *
     * @throws Zend_Controller_Action_Exception if the class has already
     * been configured
     */
    public static function setConfig(Array $config)
    {
        if (!empty(self::$_config)) {
            /**
             * @see Zend_Controller_Action_Exception
             */
            require_once 'Zend/Controller/Action/Exception.php';

            throw new Zend_Controller_Action_Exception(
                "class has already been configured"
            );
        }

        self::$_config = $config;
    }

    /**
     * Checks whether a connection is available. If this class has not been
     * configured using "setConfig()", this method will always return true.
     * This method will also return true if the enabled config option for this
     * class has been set to 0/false.
     *
     * @return boolean Whether or not it can be assumed that network
     * connections to other resources can be established
     */
    public function isConnectionAvailable()
    {
        if (empty(self::$_config)) {
            /**
             * @see Conjoon_Log
             */
            require_once 'Conjoon/Log.php';

            Conjoon_Log::log(
                "Connection check for \"".get_class($this->getActionController())
                ."::".$this->getRequest()->getActionName()
                ."\" not configured", Zend_Log::INFO
            );
            return true;
        }

        $c =& self::$_config;

        if (!$c['enabled']) {
            return true;
        }

        $errno   = "";
        $errstr  = "";

        $fp = @fsockopen($c['ip'],  $c['port'], $errno, $errstr, $c['timeout']);
        if (!$fp) {
            /**
             * @see Conjoon_Log
             */
            require_once 'Conjoon/Log.php';

            Conjoon_Log::log(
                "Connection check for \"".get_class($this->getActionController())
                ."::".$this->getRequest()->getActionName()
                ."\" returned: \"$errno\", \"$errstr\"", Zend_Log::INFO
            );

            return false;
        }

        @fclose($fp);
        return true;
    }

    /**
     * Method overloading.
     *
     * @return boolean
     */
    public function direct()
    {
        return $this->isConnectionAvailable();
    }

}
