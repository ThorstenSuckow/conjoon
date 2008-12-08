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
 * @see Conjoon_Filter_Input
 */
require_once 'Conjoon/Filter/Input.php';

/**
 * @see Conjoon_Filter_Raw
 */
require_once 'Conjoon/Filter/Raw.php';

/**
 * @see Conjoon_Validate_Date
 */
require_once 'Conjoon/Validate/Date.php';

/**
 * @see Conjoon_Filter_DateIso8601
 */
require_once 'Conjoon/Filter/DateIso8601.php';

/**
 * @see Conjoon_Filter_QuotedPrintableDecode
 */
require_once 'Conjoon/Filter/QuotedPrintableDecode.php';

/**
 * @see Conjoon_Filter_Base64Decode
 */
require_once 'Conjoon/Filter/Base64Decode.php';

/**
 * An input-filter class defining all validators and filters needed when
 * processing input data for mutating or creating data in the item-table.
 *
 * @uses Conjoon_Filter_Input
 * @package    Conjoon_Filter_Input
 * @category   Filter
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Conjoon_Modules_Groupware_Email_Item_Filter_Item extends Conjoon_Filter_Input {

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
        $this->_defaultEscapeFilter = new Conjoon_Filter_Raw();
    }

    public function getProcessedData()
    {
        $data = parent::getProcessedData();

        if ($this->_context == self::CONTEXT_DELETE || $this->_context == self::CONTEXT_MOVE) {
            return $data;
        }

        $recs = array(
            $data['to'],
            $data['cc'],
            $data['bcc']
        );

        /**
         * @see Conjoon_Filter_EmailRecipients
         */
        require_once 'Conjoon/Filter/EmailRecipients.php';

        /**
         * @see Conjoon_Filter_EmailRecipientsToString
         */
        require_once 'Conjoon/Filter/EmailRecipientsToString.php';

        $emailRecipientsFilter         = new Conjoon_Filter_EmailRecipients();
        $emailSenderFilter             = new Conjoon_Filter_EmailRecipients(false, false);
        $emailRecipientsToStringFilter = new Conjoon_Filter_EmailRecipientsToString();
        $emailSenderToStringFilter     = new Conjoon_Filter_EmailRecipientsToString(false);

        $data['recipients'] = $emailRecipientsToStringFilter->filter(
            $emailRecipientsFilter->filter(
                $recs
            )
        );

        // it should be safe to store the sender without additional slashes
        $data['sender'] = $emailSenderToStringFilter->filter(
            $emailSenderFilter->filter(
                $data['from']
            )
        );

        return $data;
    }

}