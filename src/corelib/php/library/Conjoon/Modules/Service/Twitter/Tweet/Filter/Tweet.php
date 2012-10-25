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
 * @see Conjoon_Filter_Input
 */
require_once 'Conjoon/Filter/Input.php';

/**
 * @see Zend_Filter_HtmlEntities
 */
require_once 'Zend/Filter/HtmlEntities.php';

/**
 * @see Conjoon_Filter_Raw
 */
require_once 'Conjoon/Filter/Raw.php';

/**
 * An input-filter class defining all validators and filters needed when
 * processing input data for mutating or creating Tweets.
 *
 * @uses Conjoon_Filter_Input
 * @package    Conjoon_Filter_Input
 * @category   Filter
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Conjoon_Modules_Service_Twitter_Tweet_Filter_Tweet extends Conjoon_Filter_Input {

   protected $_presence = array(
        'response' =>
            array(
                'id',
                'text',
                'createdAt',
                'source',
                'truncated',
                'userId',
                'name',
                'screenName',
                'location',
                'profileImageUrl',
                'url',
                'protected',
                'description',
                'followersCount',
                'isFollowing',
                'inReplyToStatusId',
                'inReplyToUserId',
                'inReplyToScreenName',
                'favorited'
            )
    );

    protected $_filters = array(
        'id' => array(
            'StringTrim'
         ),
        'text' => array(
            'StringTrim'
         ),
        'createdAt' => array(
            'StringTrim',
            array(
                'DateFormat',
                'Y-m-d H:i:s',
                'D M d H:i:s O Y'
            )
         ),
        'source' => array(
            'StringTrim'
         ),
         'truncated' => array(
            'FormBoolToInt'
        ),
        'userId' => array(
            'StringTrim'
        ),
        'name' => array(
            array(
                'HtmlEntities',
                array(
                    'quotestyle' => ENT_COMPAT,
                    'charset'    => 'UTF-8'
                )
            ),
            'StringTrim'
        ),
        'screenName' => array(
            array(
                'HtmlEntities',
                array(
                    'quotestyle' => ENT_COMPAT,
                    'charset'    => 'UTF-8'
                )
            ),
            'StringTrim'
         ),
        'location' => array(
            array(
                'HtmlEntities',
                array(
                    'quotestyle' => ENT_COMPAT,
                    'charset'    => 'UTF-8'
                )
            ),
            'StringTrim'
         ),
        'profileImageUrl' => array(
            'StringTrim'
         ),
        'url' => array(
            array(
                'HtmlEntities',
                array(
                    'quotestyle' => ENT_COMPAT,
                    'charset'    => 'UTF-8'
                )
            ),
            'StringTrim'
         ),
        'protected' => array(
            'FormBoolToInt'
        ),
        'isFollowing' => array(
            'FormBoolToInt'
        ),
        'description' => array(
            array(
                'HtmlEntities',
                array(
                    'quotestyle' => ENT_COMPAT,
                    'charset'    => 'UTF-8'
                )
            ),
            'StringTrim'
         ),
        'followersCount' => array(
            'Int'
        ),
        'inReplyToStatusId' => array(
            'StringTrim'
        ),
        'inReplyToUserId' => array(
            'StringTrim'
        ),
        'inReplyToScreenName' => array(
            'StringTrim'
        ),
        'favorited' => array(
            'FormBoolToInt'
        )
    );

    protected $_validators = array(
        'id' => array(
            'allowEmpty' => false
         ),
        'text' => array(
            'allowEmpty' => false
         ),
        'createdAt' => array(
            'allowEmpty' => false
         ),
        'source' => array(
            'allowEmpty' => false
         ),
         'truncated' => array(
            'allowEmpty' => true,
            'default'    => false,
        ),
        'userId' => array(
            'allowEmpty' => false
        ),
        'name' => array(
            'allowEmpty' => false
        ),
        'screenName' => array(
            'allowEmpty' => false
         ),
        'location' => array(
            'allowEmpty' => true,
            'default'    => ""
         ),
        'profileImageUrl' => array(
            'allowEmpty' => false
         ),
        'url' => array(
            'allowEmpty' => true,
            'default'    => ""
         ),
        'protected' => array(
            'allowEmpty' => true,
            'default'    => false
        ),
        'isFollowing' => array(
            'allowEmpty' => true,
            'default'    => false
        ),
        'description' => array(
            'allowEmpty' => true,
            'default'    => ""
         ),
        'followersCount' => array(
            'allowEmpty' => true,
            'default'    => 0
        ),
        'inReplyToStatusId' => array(
            'allowEmpty' => true,
            'default'    => 0
        ),
        'inReplyToUserId' => array(
            'allowEmpty' => true,
            'default'    => ""
        ),
        'inReplyToScreenName' => array(
            'allowEmpty' => true,
            'default'    => "",
        ),
        'favorited' => array(
            'allowEmpty' => true,
            'default'    => false
        )
    );


    protected function _init()
    {
        $this->_defaultEscapeFilter = new Conjoon_Filter_Raw();
    }

    /**
     * Returns an associative array with all filtered and validated
     * fields that where found in $_presence.
     *
     *
     * @throws Zend_Filter_Exception
     *
     * @see $_presence
     */
    public function getProcessedData()
    {
        $data = parent::getProcessedData();

        $value = array();
        $res = preg_match(
            "/^<a href=\"(.*)\">(.*)<\/a>/i",
            $data['source'],
            $value
        );

        if ($res === 0) {
            $data['sourceUrl'] = "";
        } else {
            $data['source']    = $value[2];
            $data['sourceUrl'] = $value[1];
        }


        return $data;
    }

}