<?php
/**
 * conjoon
 * (c) 2002-2009 siteartwork.de/conjoon.org
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
 * It does also override "favoriteCreate", "favoriteDestroy" and "statusDestroy",
 * since in ZF 1.7.8 the status ids would be casted to integer, which must not happen
 * (see #http://www.twitpocalypse.com/)
 *
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 * @author The Zend Framework Team
 */
class Conjoon_Service_Twitter extends Zend_Service_Twitter {


    /**
     * Update user's current status
     * This implementation will send a sourceId "conjoon" to the Twitter service,
     * notifying that the corresponding update was done using conjoon.
     * This implementation will also skipp checking if the requested update is
     * longer than 140 bytes, as this will be done by the Twitter service itself, truncating
     * the status message if needed.
     *
     * @param  string $status
     * @param  int $in_reply_to_status_id
     * @return Zend_Rest_Client_Result
     * @throws Zend_Service_Twitter_Exception if message is too short or too long
     */
    public function statusUpdate($status, $in_reply_to_status_id = null)
    {
        $this->_init();
        $path = '/statuses/update.xml';
        $len  = strlen($status);
        //if ($len > 140) {
       //     include_once 'Zend/Service/Twitter/Exception.php';
        //    throw new Zend_Service_Twitter_Exception('Status must be no more than 140 characters in length');
        //} else
        if (0 == $len) {
            include_once 'Zend/Service/Twitter/Exception.php';
            throw new Zend_Service_Twitter_Exception('Status must contain at least one character');
        }

        $data = array(
            'status' => $status,
            'source' => 'conjoon'
        );

        if(is_numeric($in_reply_to_status_id) && !empty($in_reply_to_status_id)) {
            $data['in_reply_to_status_id'] = $in_reply_to_status_id;
        }

        //$this->status = $status;
        $response = $this->restPost($path, $data);
        return new Zend_Rest_Client_Result($response->getBody());
    }

    /**
     * Remove a favorite
     *
     * @param  float $id Status ID you want to de-list as a favorite
     * @return Zend_Rest_Client_Result
     */
    public function favoriteDestroy($id)
    {
        $this->_init();
        $path = '/favorites/destroy/' . (float)$id . '.xml';

        $response = $this->restPost($path);
        return new Zend_Rest_Client_Result($response->getBody());
    }

    /**
     * Mark a status as a favorite
     *
     * @param  float $id Status ID you want to mark as a favorite
     * @return Zend_Rest_Client_Result
     */
    public function favoriteCreate($id)
    {
        $this->_init();
        $path = '/favorites/create/' .  (float)$id . '.xml';

        $response = $this->restPost($path);
        return new Zend_Rest_Client_Result($response->getBody());
    }

    /**
     * Destroy a status message
     *
     * @param  float $id ID of status to destroy
     * @return Zend_Rest_Client_Result
     */
    public function statusDestroy($id)
    {
        $this->_init();
        $path = '/statuses/destroy/' . (float)$id . '.xml';

        $response = $this->restPost($path);
        return new Zend_Rest_Client_Result($response->getBody());
    }

}