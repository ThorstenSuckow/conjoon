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
 *
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Modules_Groupware_Email_Attachment_Facade {

    /**
     * @var Conjoon_Modules_Groupware_Email_Attachment_Facade $_instance
     */
    private static $_instance = null;

    /**
     * @var Conjoon_Modules_Groupware_Email_Attachment_Model_Attachment
     */
    private $_attachmentModel = null;

    /**
     * @var Conjoon_Modules_Groupware_Email_Item_Model_Item
     */
    private $_itemModel = null;

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    public static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

// -------- public api

    /**
     * Generates a unique id for an attachment. This id is a string with a length
     * of 32 chars, alphanumeric.
     *
     * @param integer $id The user id for which the unique id should be generated.
     *
     * @return string
     */
    public function generateAttachmentKey($userId)
    {
        return md5(uniqid(mt_rand(), true));
    }

    /**
     * Returns download data for the required attachment, i.e. mime type,
     * filename and content. The data will be returned in an associative array
     * and is already prepared for sending to the client, i.e. the content to
     * send has already been decoded if necessary.
     *
     * @param mixed $attachmentKey
     * @param integer $attachmentId
     * @param inter $userId
     *
     * @return array an assoc array with the keys "name", "mimeType" and
     * "content", or null if item is not available.
     *
     * @throws InvalidArgumentException
     */
    public function getAttachmentDownloadDataForUserId($attachmentKey, $attachmentId, $userId)
    {
        $attachmentKey = trim((string)$attachmentKey);
        $attachmentId  = (int)$attachmentId;
        $userId        = (int)$userId;

        if ($attachmentKey == "") {
            throw new InvalidArgumentException(
                "Invalid argument supplied for attachmentKey - $attachmentKey"
            );
        }

        if ($attachmentId <= 0) {
            throw new InvalidArgumentException(
                "Invalid argument supplied for attachmentId - $attachmentId"
            );
        }
        if ($userId <= 0) {
            throw new InvalidArgumentException(
                "Invalid argument supplied for userId - $userId"
            );
        }

        if (!$this->isAttachmentDownloadableForUserId(
            $attachmentKey, $attachmentId, $userId)) {
            return null;
        }

        $attachmentModel = $this->_getAttachmentModel();

        $data = $attachmentModel->getAttachmentForKeyAndId(
            $attachmentKey, $attachmentId
        );

        if (empty($data)) {
            return null;
        }

        return array(
            'name'    => $data['file_name'],
            'content' => ($data['encoding'] == 'quoted-printable'
                          ? quoted_printable_decode($data['content'])
                            : ($data['encoding'] == 'base64'
                             ? base64_decode($data['content'])
                           : $data['content'])),
            'mimeType' => $data['mime_type']
                          ? $data['mime_type'] : 'text/plain'
        );
    }

    /**
     * Returns true if the user may download the attachment with the specified
     * id, otherwise false.
     *
     * @param string  $attachmentKey The key of the attachment to download
     * @param integer $attachmentId The id of the attachment to download
     * @param integer $userId The id of the user who requests the attachment
     * being downloaded
     *
     * @return boolean true if he may download the attachment, otherwise false
     *
     * @throws InvalidArgumentException
     */
    public function isAttachmentDownloadableForUserId($attachmentKey, $attachmentId, $userId)
    {
        $attachmentKey = trim((string)$attachmentKey);
        $attachmentId  = (int)$attachmentId;
        $userId        = (int)$userId;

        if ($attachmentKey == "") {
            throw new InvalidArgumentException(
                "Invalid argument supplied for attachmentKey - $attachmentKey"
            );
        }

        if ($attachmentId <= 0) {
            throw new InvalidArgumentException(
                "Invalid argument supplied for attachmentId - $attachmentId"
            );
        }
        if ($userId <= 0) {
            throw new InvalidArgumentException(
                "Invalid argument supplied for userId - $userId"
            );
        }

        $model = $this->_getAttachmentModel();

        $emailId = $model->getEmailItemIdForAttachmentKeyAndId(
            $attachmentKey, $attachmentId
        );

        if ($emailId == 0) {
            return false;
        }

        // will return an item only if the user has access over this item
        $item = $this->_getItemModel()->getItemForUser($emailId, $userId);

        if (empty($item)) {
            return false;
        }

        return true;
    }


// -------- api

    /**
     *
     * @return Conjoon_Modules_Groupware_Email_Attachment_Model_Attachment
     */
    private function _getAttachmentModel()
    {
        if (!$this->_attachmentModel) {
             /**
             * @see Conjoon_Modules_Groupware_Email_Attachment_Model_Attachment
             */
            require_once 'Conjoon/Modules/Groupware/Email/Attachment/Model/Attachment.php';

            $this->_attachmentModel = new Conjoon_Modules_Groupware_Email_Attachment_Model_Attachment();
        }

        return $this->_attachmentModel;
    }

    /**
     *
     * @return Conjoon_Modules_Groupware_Email_Item_Model_Item
     */
    private function _getItemModel()
    {
        if (!$this->_itemModel) {
             /**
             * @see Conjoon_Modules_Groupware_Email_Item_Model_Item
             */
            require_once 'Conjoon/Modules/Groupware/Email/Item/Model/Item.php';

            $this->_itemModel = new Conjoon_Modules_Groupware_Email_Item_Model_Item();
        }

        return $this->_itemModel;
    }


}