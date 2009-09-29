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
 * @see Zend_Cache
 */
require_once 'Zend/Cache.php';

/**
 * @see Conjoon_Builder
 */
require_once 'Conjoon/Builder.php';


class Conjoon_Modules_Groupware_Email_Message_Builder extends Conjoon_Builder {

    protected $_validGetOptions = array('groupwareEmailItemsId', 'userId');

    protected $_buildClass = 'Conjoon_Modules_Groupware_Email_Message_Dto';

    /**
     *
     * @param array $options An associative array with the following
     * key value/pairs:
     *   - groupwareEmailItemsId: The id of the email message to return
     *   - userId: the id of the user to whom this email message belongs @param Array $options
     */
    protected function _buildId(Array $options)
    {
        return $options['groupwareEmailItemsId'] . '_' . $options['userId'];
    }

    /**
     *
     * @param array $options An associative array with the following
     * key value/pairs:
     *   - groupwareEmailItemsId: The id of the email message to return
     *   - userId: the id of the user to whom this email message belongs @param Array $options
     *
     * @return Conjoon_Modules_Groupware_Email_Message_Dto
     */
    protected function _build(Array $options)
    {
        $groupwareEmailItemsId = $options['groupwareEmailItemsId'];
        $userId                = $options['userId'];

        /**
         * @see Conjoon_BeanContext_Decorator
         */
        require_once 'Conjoon/BeanContext/Decorator.php';

        /**
         * @see Conjoon_Modules_Groupware_Email_Message_Filter_MessageResponse
         */
        require_once 'Conjoon/Modules/Groupware/Email/Message/Filter/MessageResponse.php';

        $messageDecorator = new Conjoon_BeanContext_Decorator(
            'Conjoon_Modules_Groupware_Email_Message_Model_Message',
            new Conjoon_Modules_Groupware_Email_Message_Filter_MessageResponse(
                array(),
                Conjoon_Filter_Input::CONTEXT_RESPONSE
            )
        );

        $message = $messageDecorator->getEmailMessageAsDto($groupwareEmailItemsId, $userId);

        if (!$message) {
            return null;
        }

        require_once 'Conjoon/Modules/Groupware/Email/Attachment/Filter/AttachmentResponse.php';

        $attachmentDecorator = new Conjoon_BeanContext_Decorator(
            'Conjoon_Modules_Groupware_Email_Attachment_Model_Attachment',
            new Conjoon_Modules_Groupware_Email_Attachment_Filter_AttachmentResponse(
                array(),
                Conjoon_Filter_Input::CONTEXT_RESPONSE
            )
        );

        $attachments = $attachmentDecorator->getAttachmentsForItemAsDto($groupwareEmailItemsId);

        $message->attachments = $attachments;



        return $message;
    }

}