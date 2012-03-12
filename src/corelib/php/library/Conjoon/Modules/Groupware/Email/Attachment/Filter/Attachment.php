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
 * @see Conjoon_Filter_Raw
 */
require_once 'Conjoon/Filter/Raw.php';


/**
 * An input-filter class defining all validators and filters needed when
 * processing input data for mutating or creating data in the attachment-table.
 *
 * @uses Conjoon_Filter_Input
 * @package    Conjoon_Filter_Input
 * @category   Filter
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Conjoon_Modules_Groupware_Email_Attachment_Filter_Attachment extends Conjoon_Filter_Input {

    const CONTEXT_DOWNLOAD_REQUEST = 'download_request';

    protected $_presence = array(
         self::CONTEXT_CREATE => array(
            'groupwareEmailItemsId',
            'fileName',
            'mimeType',
            'encoding',
            'content',
            'contentId',
            'key'
        ),
        self::CONTEXT_DOWNLOAD_REQUEST => array(
            'id',
            'key',
            'downloadCookieName'
        )
    );

    protected $_filters = array(
        'id' => array(
            'Int'
        ),
        'groupwareEmailItemsId' => array(
            'Int'
         ),
         'fileName' => array(
            'StringTrim'
         ),
         'key' => array(
            'StringTrim'
         ),
         'mimeType' => array(
            'StringTrim'
         ),
         'encoding' => array(
            'StringTrim'
         ),
         'content' => array(),
         'contentId' => array(
            'StringTrim'
         ),
         'downloadCookieName' => array(
            'StringTrim'
         )
    );

    protected $_validators = array(
        'id' => array(
            'allowEmpty' => false,
            'default'    => 0
         ),
        'groupwareEmailItemsId' => array(
            'allowEmpty' => false
         ),
         'fileName' => array(
            'allowEmpty' => false
         ),
         'mimeType' => array(
            'allowEmpty' => true,
            'default' => ''
         ),
         'encoding' => array(
            'allowEmpty' => true,
            'default' => ''
         ),
         'content' => array(
            'allowEmpty' => true,
            'default' => ''
         ),
         'contentId' => array(
            'allowEmpty' => true,
            'default' => ''
         ),
         'key' => array(
            'allowEmpty' => false,
            array('StringLength', 32, 32)
         ),
         'downloadCookieName' => array(
            'allowEmpty' => false
         )
    );

    protected function _init()
    {
        $this->_defaultEscapeFilter = new Conjoon_Filter_Raw();
    }



}