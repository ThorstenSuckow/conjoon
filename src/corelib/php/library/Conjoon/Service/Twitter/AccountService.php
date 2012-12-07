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
 * Class for providing services related to Twitter accounts.
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Service_Twitter_AccountService  {


    /**
     * returns the callback url for the oauth service based on the specified
     * arguments.
     *
     * @param array $config
     *
     * @returns string
     *
     * @throws Conjoon_Argument_Exception
     */
    public function getOauthCallbackUrl(array $config)
    {
        /**
         * @see Conjoon_Argument_Check
         */
        require_once 'Conjoon/Argument/Check.php';

        Conjoon_Argument_Check::check(array(
            'host' => array(
                'type'       => 'string',
                'allowEmpty' => false
            ),
            'port' => array(
                'type'        => 'int',
                'allowEmpty'  => false,
                'greaterThan' => 0
            ),
            'protocol' => array(
                'type'       => 'string',
                'allowEmpty' => false
            ),
            'baseUrl' => array(
                'type'       => 'string',
                'allowEmpty' => false
            ),
            'oauthCallbackUrl' => array(
                'type'       => 'string',
                'allowEmpty' => false
            ),

        ), $config);


        $port     = $config['port'];
        $protocol = $config['protocol'];
        $host     = $config['host'];
        $baseUrl  = $config['baseUrl'];

        $oauthCallbackUrl = $config['oauthCallbackUrl'];

        $host        = trim($host, '/');
        $baseUrl     = trim($baseUrl, '/');
        $callbackUrl = ltrim($oauthCallbackUrl, '/');

        $baseUrl = $baseUrl == '' ? '/' : '/' . $baseUrl . '/';

        return $protocol . '://' . $host . ':' . $port
               . $baseUrl
               . $callbackUrl;
    }






}