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
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 * @author The Zend Framework Team
 */
class Conjoon_Service_Twitter extends Zend_Service_Twitter {

    /**
     * Types of API methods
     *
     * @var array
     */
    protected $methodTypes = array(
        'account',
        'application',
        'blocks',
        'directmessages',
        'favorites',
        'friendships',
        'search',
        'statuses',
        'users',
        /**
         * added "friends" to be able to call friends->list
         */
        'friends'
    );


    /**
     * Returns a list of users this user specified in $params "user_id" follows.
     *
     * @param array $params an array with options the service is to be called with.
     *  - user_id the id of the user for whom the followers list should be retrieved
     *  - cursor the position of the  followers list, -1 for the very first dataset
     *  - count how many followers should be retrieved
     *
     * @return Zend_Service_Twitter_Response
     */
    public function friendsList(array $params = array()) {
        $this->init();
        $path = 'friends/list';

        $_params = array();

        foreach ($params as $key => $value) {
            switch (strtolower($key)) {
                case 'user_id':
                    $_params['user_id'] = (string) $value;
                    break;
                case 'cursor':
                    $_params['cursor'] = (string) $value;
                    break;
                case 'count':
                    $_params['count'] = (string) $value;
                    break;
                default:
                    break;
            }
        }

        $response = $this->get($path, $_params);
        return new Zend_Service_Twitter_Response($response);
    }

    /**
     * Returns detailed information about the relationship between two arbitrary users.
     * Resource URL
     *
     * @param array params  At least one source and one target, whether specified by IDs or
     * screen_names, should be provided to this method. (target/source_id, target/source_screen_name)
     *
     * @return Zend_Service_Twitter_Response
     */
    public function friendshipsShow($params) {
        $this->init();
        $path     = 'friendships/show';
        $response = $this->get($path, $params);
        return new Zend_Service_Twitter_Response($response);
    }



}
