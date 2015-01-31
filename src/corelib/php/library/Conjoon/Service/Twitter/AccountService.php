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