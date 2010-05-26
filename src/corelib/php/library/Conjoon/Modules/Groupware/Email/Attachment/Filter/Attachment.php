<?php
/**
 * conjoon
 * (c) 2002-2010 siteartwork.de/conjoon.org
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

    protected $_presence = array(
         self::CONTEXT_CREATE => array(
            'groupwareEmailItemsId',
            'fileName',
            'mimeType',
            'encoding',
            'content',
            'contentId',
            'key'
        )
    );

    protected $_filters = array(
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
         )
    );

    protected $_validators = array(
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
         )
    );

    protected function _init()
    {
        $this->_defaultEscapeFilter = new Conjoon_Filter_Raw();
    }



}