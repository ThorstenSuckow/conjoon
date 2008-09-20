<?php
/**
 * intrabuild
 * (c) 2002-2008 siteartwork.de/MindPatterns
 * license@siteartwork.de
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
 * @see Intrabuild_Validate_Date
 */
require_once 'Intrabuild/Validate/Date.php';

/**
 * @see Intrabuild_Filter_DateIso8601
 */
require_once 'Intrabuild/Filter/DateIso8601.php';

/**
 * @see Intrabuild_Filter_QuotedPrintableDecode
 */
require_once 'Intrabuild/Filter/QuotedPrintableDecode.php';

/**
 * @see Intrabuild_Filter_Base64Decode
 */
require_once 'Intrabuild/Filter/Base64Decode.php';

/**
 * An input-filter class defining all validators and filters needed when
 * processing input data for mutating or creating data in the item-table.
 *
 * @uses Intrabuild_Filter_Input
 * @package    Intrabuild_Filter_Input
 * @category   Filter
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Intrabuild_Modules_Groupware_Email_Item_Filter_Item extends Intrabuild_Filter_Input {

    const CONTEXT_MOVE = 'move';

    protected $_presence = array(
         self::CONTEXT_DELETE => array(
            'id'
         ),
         self::CONTEXT_MOVE => array(
            'groupwareEmailFoldersId',
            'id'
         ),
         self::CONTEXT_CREATE => array(
            'groupwareEmailFoldersId',
            'date',
            'subject',
            'from',
            'replyTo',
            'to',
            'cc',
            'bcc',
            'inReplyTo',
            'references',
            'contentTextPlain',
            'contentTextHtml',
        )
    );

    protected $_filters = array(
        'id' => array(
            'Int'
         ),
        'groupwareEmailFoldersId' => array(
            'Int'
         ),
         'date' => array(
            'StringTrim',
            'DateIso8601'
         ),
         'subject' => array(
            'StringTrim',
            'MimeDecodeHeader'
        ),
         'from' => array(
            'StringTrim'
         ),
         'replyTo' => array(
            'StringTrim'
         ),
         'to' => array(
            'StringTrim'
         ),
         'cc' => array(
            'StringTrim'
         ),
         'bcc' => array(
            'StringTrim'
         ),
         'inReplyTo' => array(
            'StringTrim'
         ),
         'references' => array(
            'StringTrim'
         ),
         'contentTextPlain' => array(
         ),
         'contentTextHtml' => array(
         ),
    );

    protected $_validators = array(
        'id' => array(
            'allowEmpty' => false
        ),
        'groupwareEmailFoldersId' => array(
            'allowEmpty' => false
         ),
         'date' => array(
            'Date'
         ),
         'subject' => array(
            'allowEmpty' => true,
            'default' => ''
         ),
         'from' => array(
            'allowEmpty' => true,
            'default' => ''
         ),
        'replyTo' => array(
            'allowEmpty' => true,
            'default' => ''
         ),
         'to' => array(
            'allowEmpty' => true,
            'default' => ''
         ),
         'cc' => array(
            'allowEmpty' => true,
            'default' => ''
         ),
         'bcc' => array(
            'allowEmpty' => true,
            'default' => ''
         ),
         'inReplyTo' => array(
            'allowEmpty' => true,
            'default' => ''
         ),
         'references' => array(
            'allowEmpty' => true,
            'default' => ''
         ),
         'contentTextPlain' => array(
            'allowEmpty' => true,
            'default' => ''
         ),
         'contentTextHtml' => array(
            'allowEmpty' => true,
            'default' => ''
         )
    );

    protected function _init()
    {
        $this->_defaultEscapeFilter = new Intrabuild_Filter_Raw();
    }



}