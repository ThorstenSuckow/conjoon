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
 * @see Intrabuild_Filter_Input
 */
require_once 'Intrabuild/Filter/Input.php';

/**
 * @see Intrabuild_Filter_Raw
 */
require_once 'Intrabuild/Filter/Raw.php';


/**
 * An input-filter class defining all validators and filters needed when
 * processing input data for mutating or creating data in the attachment-table.
 *
 * @uses Intrabuild_Filter_Input
 * @package    Intrabuild_Filter_Input
 * @category   Filter
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Intrabuild_Modules_Groupware_Email_Attachment_Filter_Attachment extends Intrabuild_Filter_Input {

    protected $_presence = array(
         self::CONTEXT_CREATE => array(
            'groupwareEmailItemsId',
            'fileName',
            'mimeType',
            'encoding',
            'content',
            'contentId'
        )
    );

    protected $_filters = array(
        'groupwareEmailItemsId' => array(
            'Int'
         ),
         'fileName' => array(
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
         )
    );

    protected function _init()
    {
        $this->_defaultEscapeFilter = new Intrabuild_Filter_Raw();
    }



}