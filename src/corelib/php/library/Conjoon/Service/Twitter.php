<?php


require_once 'Zend/Service/Twitter.php';

class Conjoon_Service_Twitter extends Zend_Service_Twitter {


    /**
     * Update user's current status
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

}