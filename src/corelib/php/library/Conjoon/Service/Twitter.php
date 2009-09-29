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
        $len = iconv_strlen(htmlspecialchars($status, ENT_QUOTES, 'UTF-8'), 'UTF-8');
        if ($len > self::STATUS_MAX_CHARACTERS) {
            include_once 'Zend/Service/Twitter/Exception.php';
            throw new Zend_Service_Twitter_Exception('Status must be no more than '. self::STATUS_MAX_CHARACTERS .' characters in length');
        } elseif (0 == $len) {
            include_once 'Zend/Service/Twitter/Exception.php';
            throw new Zend_Service_Twitter_Exception('Status must contain at least one character');
        }
        $data = array(
            'status' => $status,
            'source' => 'conjoon'
        );
        if (is_numeric($in_reply_to_status_id) && ! empty($in_reply_to_status_id)) {
            $data['in_reply_to_status_id'] = $in_reply_to_status_id;
        }
        //$this->status = $status;
        $response = $this->_post($path, $data);
        return new Zend_Rest_Client_Result($response->getBody());
    }

}