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
 * @see Zend_Service_Twitter
 */
require_once 'Zend/Service/Twitter.php';

/**
 * This class main purpose is to send a source id "conjoon" with any status update,
 * thus notifying the Twitter service that the update was done using conjoon.
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 * @author The Zend Framework Team
 */
class Conjoon_Service_Twitter extends Zend_Service_Twitter {

    /**
     * @see #ZF-9215 - "page" parameter does not work, instead
     * use "cursor"
     *
     * User friends
     *
     * @param  int|string $id Id or username of user for whom to fetch friends
     * @throws Zend_Http_Client_Exception if HTTP request fails or times out
     * @return Zend_Rest_Client_Result
     */
    public function userFriends(array $params = array())
    {
        $this->_init();
        $path = '/1/statuses/friends';
        $_params = array();

        foreach ($params as $key => $value) {
            switch (strtolower($key)) {
                case 'id':
                    $path .= '/' . $value;
                    break;
                case 'cursor':
                    $_params['cursor'] = (string) $value;
                    break;
                default:
                    break;
            }
        }
        $path .= '.xml';
        $response = $this->_get($path, $_params);
        return new Zend_Rest_Client_Result($response->getBody());
    }

}