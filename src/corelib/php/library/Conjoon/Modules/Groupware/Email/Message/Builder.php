<?php
/**
 * conjoon
 * (c) 2007-2015 conjoon.org
 * licensing@conjoon.org
 *
 * conjoon
 * Copyright (C) 2014 Thorsten Suckow-Homberg/conjoon.org
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
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
     * @return Conjoon_Modules_Groupware_Email_Message_Model_Message
     */
    protected function _getModel()
    {
        /**
         * @see Conjoon_Modules_Groupware_Email_Message_Model_Message
         */
        require_once 'Conjoon/Modules/Groupware/Email/Message/Model/Message.php';

        return new Conjoon_Modules_Groupware_Email_Message_Model_Message();
    }

    /**
     *
     * @param array $options An associative array with the following
     * key value/pairs:
     *   - groupwareEmailItemsId: The id of the email message to return
     *   - userId: the id of the user to whom this email message belongs @param Array $options
     * @param Conjoon_BeanContext_Decoratable $model
     *
     * @return Conjoon_Modules_Groupware_Email_Message_Dto
     */
    protected function _build(Array $options, Conjoon_BeanContext_Decoratable $model)
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
            $model,
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