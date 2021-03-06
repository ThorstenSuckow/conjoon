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
 * @see \Conjoon\Vendor\Zend\Controller\Action\MailModule\BaseController
 */
require_once 'Conjoon/Vendor/Zend/Controller/Action/MailModule/BaseController.php';

/**
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Groupware_EmailSendController extends
    \Conjoon\Vendor\Zend\Controller\Action\MailModule\BaseController {

    const CONTEXT_JSON = 'json';

    /**
     * Inits this controller and sets the context-switch-directives
     * on the various actions.
     *
     */
    public function init()
    {
        $conjoonContext = $this->_helper->conjoonContext();

        $conjoonContext->addActionContext('send',      self::CONTEXT_JSON)
                       ->addActionContext('bulk.send', self::CONTEXT_JSON)
                       ->initContext();
    }

    /**
     * Sends an email to the specified recipients.
     * The action expects the following arguments to be passed:
     *
     * - format: The format the email should be send. Can default to
     *           "text/plain", "text/html" - or "multipart" if the email should
     *           be send both as html and plain-text.
     * - id: The id of the messge if this was loaded from an already existing
     *       draft (i.e. a draft, an email that is being replied to which is being forwarded).
     * -type: The type of teh action: if this equals to "reply", "reply_all" or "forward",
     * this message references an existing one
     * Can default to 0 or -1 if the emil was created from the scratch
     * - groupwareEmailAccountsId: An integer specifying the id of the email account of the
     *              user which will be used to send the message
     * - groupwareEmailFoldersId: The id of the folder from which this email was opened. Equals
     *             to -1 or 0 if the messge was created from scratch
     * - subject: The subject of the message
     * - message: The message as edited in the browser. Will most likely have
     *            HTML tags in it depending on the editor used
     * - to: An json encoded array with all addresses being specified in the "to"
     *       field. Addresses may be separated by a comma "," or a semicolon ";"
     * - cc: An json encoded array with all addresses being specified in the "cc"
     *       field. Addresses may be separated by a comma "," or a semicolon ";"
     * - bcc: An json encoded array with all addresses being specified in the "bcc"
     *        field. Addresses may be separated by a comma "," or a semicolon ";"
     * - attachments: An array with attachments, structure according to
     * com.conjoon.cudgets.data.FileRecord. ids for files will be stored in
     * the orgId property
     * The view awaits a fully configured email item as the response.
     */
    public function sendAction()
    {
        require_once 'Conjoon/Modules/Groupware/Email/Draft/Filter/DraftInput.php';

        $data = array();
        try {
            // the filter will transform the "message" into bodyHtml and bodyText, depending
            // on the passed format. both will only be filled if format equals to "multipart"
            $filter = new Conjoon_Modules_Groupware_Email_Draft_Filter_DraftInput(
                $_POST,
                Conjoon_Filter_Input::CONTEXT_CREATE
            );
            $data = $filter->getProcessedData();
        } catch (Exception $e) {
             require_once 'Conjoon/Error.php';
             $error = Conjoon_Error::fromFilter($filter, $e);
             $this->view->success = false;
             $this->view->error   = $error->getDto();
             $this->view->item    = null;
             return;
        }

        // input filter does not work properly sometimes with refrenced data
        // check here for referencedData, throw an exception if not set
        if (!isset($data['referencedData']) || !is_array($data['referencedData'])) {
            throw new Exception("referencedData missing.");
        }

        /**
         * @see Conjoon_Modules_Groupware_Email_Address
         */
        require_once 'Conjoon/Modules/Groupware/Email/Address.php';

        /**
         * @see Conjoon_Modules_Groupware_Email_Draft
         */
        require_once 'Conjoon/Modules/Groupware/Email/Draft.php';


        /**
         * @see Conjoon_BeanContext_Inspector
         */
        require_once 'Conjoon/BeanContext/Inspector.php';


        $postedAttachments = $data['attachments'];
        $data['attachments'] = array();

        $removeAttachmentIds = $data['removedAttachments'];
        unset($data['removedAttachments']);

        // create the message object here
        $to  = array();
        $cc  = array();
        $bcc = array();

        foreach ($data['cc'] as $dcc) {
            $add        = new Conjoon_Modules_Groupware_Email_Address($dcc);
            $cc[]       = $add;
        }
        foreach ($data['bcc'] as $dbcc) {
            $add         = new Conjoon_Modules_Groupware_Email_Address($dbcc);
            $bcc[]       = $add;
        }
        foreach ($data['to'] as $dto) {
            $add        = new Conjoon_Modules_Groupware_Email_Address($dto);
            $to[]       = $add;
        }

        $data['cc']  = $cc;
        $data['to']  = $to;
        $data['bcc'] = $bcc;

        // get the specified account for the user

        /**
         * @see Conjoon_BeanContext_Decorator
         */
        require_once 'Conjoon/BeanContext/Decorator.php';

        /**
         * @see Conjoon_Keys
         */
        require_once 'Conjoon/Keys.php';

        $accountDecorator = new Conjoon_BeanContext_Decorator(
            'Conjoon_Modules_Groupware_Email_Account_Model_Account'
        );

        $auth   = Zend_Registry::get(Conjoon_Keys::REGISTRY_AUTH_OBJECT);
        $userId = $auth->getIdentity()->getId();

        $account = $accountDecorator->getAccountAsEntity($data['groupwareEmailAccountsId'], $userId);

        // no account found?
        if (!$account) {

            $this->view->error = $this->getErrorDto(
                'Error while sending email',
                'Could not find specified account.',
                Conjoon_Error::LEVEL_ERROR
            );

            $this->view->success = false;
            $this->view->item    = null;
            return;
        }

        $message = Conjoon_BeanContext_Inspector::create(
                'Conjoon_Modules_Groupware_Email_Draft',
                $data,
                true
        );

        $updateCache = false;

        // check whether we need to apply attachments for a previously saved
        // draft
        if ($message->getId() > 0 && !$this->isRemotePath($data['path'], $userId)) {


            $updateCache = true;

            /**
             * @see Conjoon_Modules_Groupware_Email_Attachment_Filter_AttachmentResponse
             */
            require_once 'Conjoon/Modules/Groupware/Email/Attachment/Filter/AttachmentResponse.php';
            $attDecorator = new Conjoon_BeanContext_Decorator(
                'Conjoon_Modules_Groupware_Email_Attachment_Model_Attachment',
                new Conjoon_Modules_Groupware_Email_Attachment_Filter_AttachmentResponse(
                    array(),
                    Conjoon_Modules_Groupware_Email_Attachment_Filter_AttachmentResponse::CONTEXT_RESPONSE
                )
            );
            $atts = $attDecorator->getAttachmentsForItemAsEntity($message->getId());

            $message->setAttachments($atts);
        }

        /**
         * @see Conjoon_Modules_Groupware_Email_Sender
         */
        require_once 'Conjoon/Modules/Groupware/Email/Sender.php';

        try {

            $transport = $this->getTransportForAccount($account);

            $assembleInformation = Conjoon_Modules_Groupware_Email_Sender::getAssembledMail(
                $message, $account, $postedAttachments, $removeAttachmentIds,
                $this->getCurrentAppUser()->getId(),
                $transport,
                $data['type']
            );

            $assembledMail = $assembleInformation['message'];
            $postedAttachments =  $assembleInformation['postedAttachments'];

            $mail = Conjoon_Modules_Groupware_Email_Sender::send($assembledMail);
        } catch (Exception $e) {

            $errorMessage = $e->getMessage();

            // check here if a message is set. We rely heavily on stream_socket_client
            // in Zend_Mail_Protocol_Abstract which may not set the error message all
            // the time. If no internet conn is available, the message will be missing
            // on windows systems, for example
            if ($errorMessage == "") {
                $errorMessage = "The message with the subject \""
                    . $message->getSubject()."\" could not be sent. "
                    . "Please check the internet connection of "
                    . "the server this software runs on.";
            }

            $this->view->error = $this->getErrorDto(
                'Error while sending email', $errorMessage, Conjoon_Error::LEVEL_ERROR
            );

            $this->view->success = false;
            $this->view->item    = null;
            return;
        }

        // check here if the referenced data contains an IMAP message.
        // if this is the case, update the message with the \Answered
        // flag if applicable
        $referencedData       = $message->getReferencedData();
        $referencedRemoteItem = null;

        $contextReferencedItem  = null;

        $isRemoteItemReferenced = false;

        // check if the mesage was loaded from a remote draft
        // if this is the case, remove the draft from the rmeote server

        $imapAccount = $message->getId() > 0
                       ? $this->isRemotePath($message->getPath(), $userId)
                       : null;

        if ($message->getId() > 0 && $imapAccount) {

            $uId = $message->getId();
            $path = $message->getPath();

            /**
             * @see Conjoon_Text_Parser_Mail_MailboxFolderPathJsonParser
             */
            require_once 'Conjoon/Text/Parser/Mail/MailboxFolderPathJsonParser.php';

            $parser = new Conjoon_Text_Parser_Mail_MailboxFolderPathJsonParser();

            $pathInfo = $parser->parse(json_encode($path));

            /**
             * @see Conjoon_Modules_Groupware_Email_Folder_Facade
             */
            require_once 'Conjoon/Modules/Groupware/Email/Folder/Facade.php';

            $facade = Conjoon_Modules_Groupware_Email_Folder_Facade::getInstance();

            // if remote, where is the referenced mail stored?
            $globalName = $facade->getAssembledGlobalNameForAccountAndPath(
                $imapAccount, $pathInfo['path']);

            /**
             * @see Conjoon_Modules_Groupware_Email_ImapHelper
             */
            require_once 'Conjoon/Modules/Groupware/Email/ImapHelper.php';

            /**
             * @see Conjoon_Mail_Storage_Imap
             */
            require_once 'Conjoon/Mail/Storage/Imap.php';

            $protocol = Conjoon_Modules_Groupware_Email_ImapHelper
            ::reuseImapProtocolForAccount($imapAccount);

            $storage = new Conjoon_Mail_Storage_Imap($protocol);

            // get the number of the message by it's unique id
            $storage->selectFolder($globalName);
            $messageNumber = $storage->getNumberByUniqueId($uId);

            $storage->removeMessage($messageNumber);
            $storage->close();

        }

        if (!empty($referencedData) && isset($referencedData['uId']) &&
            $referencedData['uId'] > 0) {

            $uId            = $referencedData['uId'];
            $referencedPath = $referencedData['path'];

            // check if folder is remote folder
            /**
             * @see Conjoon_Text_Parser_Mail_MailboxFolderPathJsonParser
             */
            require_once 'Conjoon/Text/Parser/Mail/MailboxFolderPathJsonParser.php';

            $parser = new Conjoon_Text_Parser_Mail_MailboxFolderPathJsonParser();

            $pathInfo = $parser->parse(json_encode($referencedPath));

            /**
             * @see Conjoon_Modules_Groupware_Email_Folder_Facade
             */
            require_once 'Conjoon/Modules/Groupware/Email/Folder/Facade.php';

            $facade = Conjoon_Modules_Groupware_Email_Folder_Facade::getInstance();

            // get the account for the root folder first
            $imapAccount =
                $facade->getImapAccountForFolderIdAndUserId($pathInfo['rootId'],
                    $userId);

            if ($imapAccount && !empty($pathInfo) && $facade->isRemoteFolder($pathInfo['rootId'])) {

                $isRemoteItemReferenced = true;

                // if remote, where is the referenced mail stored?
                $globalName = $facade->getAssembledGlobalNameForAccountAndPath(
                    $imapAccount, $pathInfo['path']);

                /**
                 * @see Conjoon_Modules_Groupware_Email_ImapHelper
                 */
                require_once 'Conjoon/Modules/Groupware/Email/ImapHelper.php';
                /**
                 * @see Conjoon_Mail_Storage_Imap
                 */
                require_once 'Conjoon/Mail/Storage/Imap.php';

                $protocol = Conjoon_Modules_Groupware_Email_ImapHelper
                ::reuseImapProtocolForAccount($imapAccount);

                $storage = new Conjoon_Mail_Storage_Imap($protocol);

                // get the number of the message by it's unique id
                $storage->selectFolder($globalName);
                $messageNumber = $storage->getNumberByUniqueId($uId);

                $flags = array('\Seen');
                if ($data['type'] == 'reply' || $data['type'] == 'reply_all') {
                    $flags = array('\Seen', '\Answered');
                } else if ($data['type'] == 'forward') {
                    $flags = array('\Seen', '$Forwarded');
                }

                $protocol->store($flags, $messageNumber, null, '+');

                $referencedRemoteItem = $this->getSingleImapListItem(
                    $imapAccount, $userId, $messageNumber, $globalName);

                // force a reconnect if internal noop fails
                Conjoon_Modules_Groupware_Email_ImapHelper
                ::reuseImapProtocolForAccount($imapAccount);

            }

        }

        $folderMappingError = null;

        if ($account->getProtocol() == 'IMAP') {

            /**
             * @see Conjoon_Modules_Groupware_Email_ImapHelper
             */
            require_once 'Conjoon/Modules/Groupware/Email/ImapHelper.php';

            /**
             * @see Zend_Registry
             */
            require_once 'Zend/Registry.php';

            /**
             *@see Conjoon_Keys
             */
            require_once 'Conjoon/Keys.php';

            $entityManager = Zend_Registry::get(Conjoon_Keys::DOCTRINE_ENTITY_MANAGER);

            $mailAccountRepository =
                $entityManager->getRepository('\Conjoon\Data\Entity\Mail\DefaultMailAccountEntity');

            $accEntity = $mailAccountRepository->findById($account->getId());

            $mappings   = $accEntity->getFolderMappings();
            $globalName = "";
            for ($i = 0, $len = count($mappings); $i < $len; $i++) {
                if ($mappings[$i]->getType() == 'SENT') {
                    $globalName = $mappings[$i]->getGlobalName();
                }
            }

            if ($globalName != "") {

                /**
                 * @see Conjoon_Mail_Storage_Imap
                 */
                require_once 'Conjoon/Mail/Storage/Imap.php';

                $protocol = Conjoon_Modules_Groupware_Email_ImapHelper
                ::reuseImapProtocolForAccount($account->getDto());
                $storage = new Conjoon_Mail_Storage_Imap($protocol);

                try {

                    $storage->selectFolder($globalName);
                    $response = $storage->appendMessage(
                        $mail->getSentHeaderText() .
                        "\n\n" .
                        $mail->getSentBodyText(),
                        $globalName
                    );

                    $lastMessage = -1;
                    $ret         = null;
                    if (is_array($response) && isset($response[0])) {
                        $ret = explode(' ', $response[0]);
                    }
                    if (is_array($ret) && count($ret) == 2 && is_numeric($ret[0])
                        && trim(strtolower($ret[1])) == 'exists') {
                        $lastMessage = $ret[0];
                    }
                    if ($lastMessage == -1) {
                        $lastMessage = $storage->countMessages();
                    }
                    if ($lastMessage == -1) {
                        throw new RuntimeException("Could not find message id.");
                    }

                    // immediately setting the \Seen flag does not seemt
                    // to work. Do so by hand.
                    $storage->setFlags($lastMessage, array('\Seen'));

                } catch (\Exception $e) {
                    $folderMappingError = true;
                }

            } else {
                $folderMappingError = true;
            }

            if ($folderMappingError) {

                $folderMappingError = $this->getErrorDto(
                    'Missing folder mapping',
                    'The email was sent, but a "sent" version could not be stored to the configured IMAP account. Make sure you have configured the folder mappings for this account properly.',
                    Conjoon_Error::LEVEL_ERROR
                );

            } else {

                $item = $this->getSingleImapListItem(
                    $account->getDto(), $userId, $lastMessage, $globalName);


            }

            // check here if a remote item was referenced.
            // if this is not the case, get the local itemand update it's
            // references
            if (!$isRemoteItemReferenced && !$referencedRemoteItem &&
                isset($referencedData) &&  isset($referencedData['uId'])
                && $referencedData['uId'] > 0) {

                $uId         = $referencedData['uId'];
                $localFolder = $referencedData['path'][count($referencedData['path']) -1 ];

                /**
                 * @see Conjoon_Modules_Groupware_Email_Item_Model_Item
                 */
                require_once 'Conjoon/Modules/Groupware/Email/Item/Model/Item.php';

                $iModel = new Conjoon_Modules_Groupware_Email_Item_Model_Item();

                $iModel->updateReferenceFromARemoteItem(
                    $uId, $localFolder, $userId, $data['type']);

                /**
                 * @see Conjoon_Modules_Groupware_Email_Item_Filter_ItemResponse
                 */
                require_once 'Conjoon/Modules/Groupware/Email/Item/Filter/ItemResponse.php';

                // if the email was send successfully, save it into the db and
                // return the params savedId (id of the newly saved email)
                // and savedFolderId (id of the folder where the email was saved in)
                $itemDecorator = new Conjoon_BeanContext_Decorator(
                    'Conjoon_Modules_Groupware_Email_Item_Model_Item',
                    new Conjoon_Modules_Groupware_Email_Item_Filter_ItemResponse(
                        array(),
                        Conjoon_Filter_Input::CONTEXT_RESPONSE
                    ),
                    false
                );

                $contextReferencedItem = $itemDecorator->getItemForUserAsDto(
                    $uId, $userId
                );
            }

            $this->view->error   = null;
            $this->view->newVersion = $message->getId() > 0;
            if ($message->getId() > 0) {
                $this->view->previousId = $message->getId();
            }

            $this->view->folderMappingError = $folderMappingError;
            $this->view->success = true;
            $this->view->item    = isset($item) ? $item : null;
            $this->view->contextReferencedItem  = $contextReferencedItem
                                                  ? $contextReferencedItem
                                                  : $referencedRemoteItem;
            return;
        }

        /**
         * @see Conjoon_Modules_Groupware_Email_Item_Filter_ItemResponse
         */
        require_once 'Conjoon/Modules/Groupware/Email/Item/Filter/ItemResponse.php';

        // if the email was send successfully, save it into the db and
        // return the params savedId (id of the newly saved email)
        // and savedFolderId (id of the folder where the email was saved in)
        $itemDecorator = new Conjoon_BeanContext_Decorator(
            'Conjoon_Modules_Groupware_Email_Item_Model_Item',
            new Conjoon_Modules_Groupware_Email_Item_Filter_ItemResponse(
                array(),
                Conjoon_Filter_Input::CONTEXT_RESPONSE
            ),
            false
        );

        $item = $itemDecorator->saveSentEmailAsDto(
            $message, $account, $userId, $mail, $data['type'],
            ($referencedRemoteItem ? -1 : $data['referencedData']['uId']),
            $postedAttachments,
            $removeAttachmentIds
        );

        if (!$item) {

            $this->view->error = $this->getErrorDto(
                'Error while saving email',
                'The email was sent, but it could not be stored into the database.',
                Conjoon_Error::LEVEL_ERROR
            );

            $this->view->success = false;
            $this->view->item    = null;
            return;
        }

        if ($updateCache) {

            /**
             * @see Conjoon_Modules_Groupware_Email_Message_Facade
             */
            require_once 'Conjoon/Modules/Groupware/Email/Message/Facade.php';

            // update cache
            Conjoon_Modules_Groupware_Email_Message_Facade::getInstance()
                ->removeMessageFromCache(
                    $item->id,
                    $this->_helper->registryAccess()->getUserId(),
                    $data['path']
            );
        }


        // if the sent email referenced an existing message, tr to fetch this message
        // and send it along as context-referenced item
        if (!$referencedRemoteItem) {
            $contextReferencedItem = $itemDecorator->getReferencedItemAsDto(
                $item->id,
                $userId
            );
        }

        $this->view->error   = null;
        $this->view->foldermappingError = $folderMappingError;
        $this->view->success = true;
        $this->view->item    = $item;
        $this->view->contextReferencedItem  = $referencedRemoteItem
                                              ? $referencedRemoteItem
                                              : (empty($contextReferencedItem)
                                                ? null
                                                : $contextReferencedItem);
    }

    /**
     * Bulk sends emails. Awaits the parameter ids as a numeric array with the ids of
     * the emails which should get send.
     *
     */
    public function bulkSendAction()
    {
        /*@REMOVE@*/
        if (!$this->_helper->connectionCheck()) {

            /**
             * @see Conjoon_Error_Factory
             */
            require_once 'Conjoon/Error/Factory.php';

            $this->view->success                = false;
            $this->view->sentItems              = array();
            $this->view->error                  = null;
            $this->view->contextReferencedItems = array();
            $this->view->error                  = Conjoon_Error_Factory::createError(
                "Unexpected connection failure while trying to bulk-send emails. "
                ."Please try again.",
                Conjoon_Error::LEVEL_WARNING,
                Conjoon_Error::DATA
            )->getDto();

            return;
        }
        /*@REMOVE@*/

        $toSend = $_POST['ids'];

        if ($this->_helper->conjoonContext()->getCurrentContext() == self::CONTEXT_JSON) {
            require_once 'Zend/Json.php';
            $toSend = Zend_Json::decode($toSend, Zend_Json::TYPE_ARRAY);
        }

        $date = null;
        if (isset($_POST['date'])) {
            require_once 'Conjoon/Filter/DateIso8601.php';
            $dateFilter = new Conjoon_Filter_DateIso8601();
            $date = $dateFilter->filter((int)$_POST['date']);
        }


        /**
         * @see Conjoon_Filter_EmailRecipients
         */
        require_once 'Conjoon/Filter/EmailRecipients.php';

        /**
         * @see Conjoon_Modules_Groupware_Email_Item_Filter_ItemResponse
         */
        require_once 'Conjoon/Modules/Groupware/Email/Item/Filter/ItemResponse.php';

        /**
         * @see Conjoon_Modules_Groupware_Email_Address
         */
        require_once 'Conjoon/Modules/Groupware/Email/Address.php';

        /**
         * @see Conjoon_Modules_Groupware_Email_Draft
         */
        require_once 'Conjoon/Modules/Groupware/Email/Draft.php';

        /**
         * @see Conjoon_BeanContext_Inspector
         */
        require_once 'Conjoon/BeanContext/Inspector.php';

        /**
         * @see Conjoon_BeanContext_Decorator
         */
        require_once 'Conjoon/BeanContext/Decorator.php';

        /**
         * @see Conjoon_Util_Array
         */
        require_once 'Conjoon/Util/Array.php';

        /**
         * @see Conjoon_Keys
         */
        require_once 'Conjoon/Keys.php';

        /**
         * @see Conjoon_Modules_Groupware_Email_Draft_Model_Draft
         */
        require_once 'Conjoon/Modules/Groupware/Email/Draft/Model/Draft.php';

        /**
         * @see Conjoon_Modules_Groupware_Email_Draft_Filter_DraftInput
         */
        require_once 'Conjoon/Modules/Groupware/Email/Draft/Filter/DraftInput.php';

        /**
         * @see Conjoon_Modules_Groupware_Email_Sender
         */
        require_once 'Conjoon/Modules/Groupware/Email/Sender.php';

        $auth   = Zend_Registry::get(Conjoon_Keys::REGISTRY_AUTH_OBJECT);
        $userId = $auth->getIdentity()->getId();

        $draftFilter = new Conjoon_Modules_Groupware_Email_Draft_Filter_DraftInput(
            array(),
            Conjoon_Filter_Input::CONTEXT_CREATE
        );

        $draftModel = new Conjoon_Modules_Groupware_Email_Draft_Model_Draft();

        $accountDecorator = new Conjoon_BeanContext_Decorator(
            'Conjoon_Modules_Groupware_Email_Account_Model_Account'
        );

        $recipientsFilter = new Conjoon_Filter_EmailRecipients();

        $newVersions            = array();
        $sendItems              = array();
        $contextReferencedItems = array();

        $errors = array();

        foreach ($toSend as $pathInfo) {

            $remoteInfo = array();

            $id   = $pathInfo['id'];
            $path = $pathInfo['path'];

            $account = null;

            $remoteAttachments = array();

            // check if path is remote!
            $isRemote = $this->isRemotePath($path, $userId);
            if ($isRemote) {
                $rawDraft = $this->getRawImapMessage($id, $path);

                if (empty($rawDraft)) {
                    continue;
                }

                //we have to post the existing attachments of the remote draft
                // again when assembling the message to send.
                // Otherwise the sender would not know which attachments should get send
                // along with the message
                $remoteAttachments = $rawDraft['attachments'];

                foreach ($remoteAttachments as &$remoteAttachment) {
                    $remoteAttachment['metaType'] = 'emailAttachment';
                    $remoteAttachment['name'] = $remoteAttachment['fileName'];
                }

                $remoteInfo = array(
                    'uid'     => $id,
                    'account' => $isRemote,
                    'path'    => $path
                );
                $rawDraft['groupwareEmailAccountsId'] = $isRemote->id;

                $account = $accountDecorator->getAccountAsEntity(
                    $rawDraft['groupwareEmailAccountsId'],
                    $userId
                );

            } else {
                $rawDraft = $draftModel->getDraft($id, $userId);

                $account = $accountDecorator->getAccountAsEntity(
                    $rawDraft['groupware_email_accounts_id'],
                    $userId
                );
            }

            // no account found?
            if (!$account) {
                /**
                 * @todo think about using the standard account as a fallback or use at last
                 * an error message to inform the user that the account used to write this email
                 * is not available anymore
                 */
                continue;
            }


            $id = (int)$id;

            if ($id <= 0) {
                continue;
            }

            if (empty($rawDraft)) {
                continue;
            }

            Conjoon_Util_Array::camelizeKeys($rawDraft);


            $rawDraft['to']  = $recipientsFilter->filter($rawDraft['to']);
            $rawDraft['cc']  = $recipientsFilter->filter($rawDraft['cc']);
            $rawDraft['bcc'] = $recipientsFilter->filter($rawDraft['bcc']);

            // create the message object here
            $to  = array();
            $cc  = array();
            $bcc = array();

            foreach ($rawDraft['cc'] as $dcc) {
                $add        = new Conjoon_Modules_Groupware_Email_Address($dcc);
                $cc[]       = $add;
            }
            foreach ($rawDraft['bcc'] as $dbcc) {
                $add         = new Conjoon_Modules_Groupware_Email_Address($dbcc);
                $bcc[]       = $add;
            }
            foreach ($rawDraft['to'] as $dto) {
                $add        = new Conjoon_Modules_Groupware_Email_Address($dto);
                $to[]       = $add;
            }

            $rawDraft['to']  = $to;
            $rawDraft['cc']  = $cc;
            $rawDraft['bcc'] = $bcc;

            $message = Conjoon_BeanContext_Inspector::create(
                'Conjoon_Modules_Groupware_Email_Draft',
                $rawDraft,
                true
            );

            if ($date !== null) {
                $message->setDate($date);
            }

            try {

                $transport = $this->getTransportForAccount($account);

                $assembleInformation = Conjoon_Modules_Groupware_Email_Sender::getAssembledMail(
                    $message,
                    $account,
                    $remoteAttachments,
                    array(),
                    $this->getCurrentAppUser()->getId(),
                    $transport
                );

                $assembledMail = $assembleInformation['message'];
                $postedAttachments =  $assembleInformation['postedAttachments'];

                $mail = Conjoon_Modules_Groupware_Email_Sender::send($assembledMail);
            } catch (Exception $e) {
                $errors[] = array(
                    'subject'     => $message->getSubject(),
                    'accountName' => $account->getName(),
                    'reason'      => $e->getMessage()
                );
                continue;
            }

            if ($isRemote) {

                /**
                 * @see Conjoon_Modules_Groupware_Email_ImapHelper
                 */
                require_once 'Conjoon/Modules/Groupware/Email/ImapHelper.php';

                $uId     = $remoteInfo['uid'];
                $account = $remoteInfo['account'];
                $path    = $remoteInfo['path'];

                // check if folder is remote folder
                /**
                 * @see Conjoon_Text_Parser_Mail_MailboxFolderPathJsonParser
                 */
                require_once 'Conjoon/Text/Parser/Mail/MailboxFolderPathJsonParser.php';

                $parser = new Conjoon_Text_Parser_Mail_MailboxFolderPathJsonParser();

                $pathInfo = $parser->parse(json_encode($path));

                /**
                 * @see Conjoon_Modules_Groupware_Email_Folder_Facade
                 */
                require_once 'Conjoon/Modules/Groupware/Email/Folder/Facade.php';

                $facade = Conjoon_Modules_Groupware_Email_Folder_Facade::getInstance();

                // get the account for the root folder first
                $imapAccount =
                    $facade->getImapAccountForFolderIdAndUserId($pathInfo['rootId'],
                        $userId);

                if ($imapAccount && !empty($pathInfo) && $facade->isRemoteFolder($pathInfo['rootId'])) {

                    // if remote, where is the referenced mail stored?
                    $globalName = $facade->getAssembledGlobalNameForAccountAndPath(
                        $imapAccount, $pathInfo['path']);

                    /**
                     * @see Conjoon_Modules_Groupware_Email_ImapHelper
                     */
                    require_once 'Conjoon/Modules/Groupware/Email/ImapHelper.php';

                    /**
                     * @see Conjoon_Mail_Storage_Imap
                     */
                    require_once 'Conjoon/Mail/Storage/Imap.php';

                    $protocol = Conjoon_Modules_Groupware_Email_ImapHelper
                    ::reuseImapProtocolForAccount($imapAccount);

                    $storage = new Conjoon_Mail_Storage_Imap($protocol);

                    // get the number of the message by it's unique id
                    $storage->selectFolder($globalName);
                    $messageNumber = $storage->getNumberByUniqueId($uId);

                    // move from sent folder
                    $sentGlobalName = $this->getGlobalNameForFolderType(
                        $imapAccount, 'SENT'
                    );
                    if (!$sentGlobalName) {
                        continue;
                    }
                    $storage->copyMessage($messageNumber, $sentGlobalName);
                    $storage->selectFolder($sentGlobalName);
                    $newMessageNumber = $storage->countMessages();
                    $storage->selectFolder($globalName);
                    $storage->removeMessage($storage->getNumberByUniqueId($uId));
                    $storage->close();
                }

                // put email into sent folder
                $protocol = Conjoon_Modules_Groupware_Email_ImapHelper
                    ::reuseImapProtocolForAccount($imapAccount);
                $storage = new Conjoon_Mail_Storage_Imap($protocol);

                // read out single item
                $item = $this->getSingleImapListItem(
                    $account, $userId, $newMessageNumber, $sentGlobalName
                );
                $newVersions[$uId] = $item['id'];
                $sendItems[] = $item;

            } else {

                // if the email was send successfully, save it into the db and
                // return the params savedId (id of the newly saved email)
                // and savedFolderId (id of the folder where the email was saved in)
                $itemDecorator = new Conjoon_BeanContext_Decorator(
                    'Conjoon_Modules_Groupware_Email_Item_Model_Item',
                    new Conjoon_Modules_Groupware_Email_Item_Filter_ItemResponse(
                        array(),
                        Conjoon_Filter_Input::CONTEXT_RESPONSE
                    ),
                    false
                );

                $item = $itemDecorator->saveSentEmailAsDto($message, $account, $userId, $mail, '');

                if (!$item) {
                    continue;
                }

                $sendItems[] = $item;
                $cri = $itemDecorator->getReferencedItemAsDto($item->id, $userId);
                if (!empty($cri)) {
                    $contextReferencedItems[]= $cri;
                }
            }
        }

        if (!empty($errors)) {
            /**
             * @see Conjoon_Error
             */
            require_once 'Conjoon/Error.php';

            $m = array();

            $m[] = "One or more messages could not be sent:";

            for ($i = 0, $len = count($errors); $i < $len; $i++) {
                $m[] = "Message ".($i+1).":";
                $m[] = "Subject: \""      . $errors[$i]['subject'] ."\"";
                $m[] = "Account: "        . $errors[$i]['accountName'];
                $m[] = "Failure reason: " . $errors[$i]['reason'];
            }

            $errorMessage = implode("\n", $m);

            $error = new Conjoon_Error();
            $error->setLevel(Conjoon_Error::LEVEL_WARNING);
            $error->setType(Conjoon_Error::DATA);
            $error->setMessage($errorMessage);

        }

        $this->view->newVersions = $newVersions;
        $this->view->success     = true;
        $this->view->sentItems   = $sendItems;
        $this->view->error       = isset($error)
                                   ? $error->getDto()
                                   : null;

        $this->view->contextReferencedItems = $contextReferencedItems;

    }


    /**
     *
     */
    protected function getSingleImapListItem($accountDto, $userId, $messageNumber, $globalName)
    {
        /**
         * @see Conjoon_Modules_Groupware_Email_Item_ItemListRequestFacade
         */
        require_once 'Conjoon/Modules/Groupware/Email/Item/ItemListRequestFacade.php';

        /**
         * @see Conjoon_Modules_Groupware_Email_Folder_Facade
         */
        require_once 'Conjoon/Modules/Groupware/Email/Folder/Facade.php';

        $folderFacade = Conjoon_Modules_Groupware_Email_Folder_Facade::getInstance();

        $rootFolder = $folderFacade->getRootFolderForAccountId(
            $accountDto, $userId
        );

        $itemFacade = Conjoon_Modules_Groupware_Email_Item_ItemListRequestFacade::getInstance();

        $delimiter = Conjoon_Modules_Groupware_Email_ImapHelper
        ::getFolderDelimiterForImapAccount($accountDto);

        $list =  $itemFacade->getEmailItemList(
            array(
                'rootId' => $rootFolder[0]->id,
                'path'   => explode($delimiter, $globalName)
            ),
            $userId, array(), false, $messageNumber, $messageNumber
        );

        return $list[0];
    }


    protected function isRemotePath($path, $userId)
    {
        // check if folder is remote folder
        /**
         * @see Conjoon_Text_Parser_Mail_MailboxFolderPathJsonParser
         */
        require_once 'Conjoon/Text/Parser/Mail/MailboxFolderPathJsonParser.php';

        $parser = new Conjoon_Text_Parser_Mail_MailboxFolderPathJsonParser();

        $pathInfo = $parser->parse(json_encode($path));

        /**
         * @see Conjoon_Modules_Groupware_Email_Folder_Facade
         */
        require_once 'Conjoon/Modules/Groupware/Email/Folder/Facade.php';

        $facade = Conjoon_Modules_Groupware_Email_Folder_Facade::getInstance();

        // get the account for the root folder first
        $imapAccount =
            $facade->getImapAccountForFolderIdAndUserId($pathInfo['rootId'],
                $userId);

        if ($imapAccount && !empty($pathInfo)
            && $facade->isRemoteFolder($pathInfo['rootId'])) {
            return $imapAccount;
        }

        return null;

    }

    /**
     * @param $account
     * @param $id
     * @param $userId
     */
    protected function getRawImapMessage($uId, $path)
    {
        $path = json_encode($path);

        /**
         * @see Zend_Registry
         */
        require_once 'Zend/Registry.php';

        /**
         *@see Conjoon_Keys
         */
        require_once 'Conjoon/Keys.php';

        $auth = Zend_Registry::get(Conjoon_Keys::REGISTRY_AUTH_OBJECT);

        /**
         * @see Conjoon_User_AppUser
         */
        require_once 'Conjoon/User/AppUser.php';

        $appUser = new \Conjoon\User\AppUser($auth->getIdentity());

        $entityManager = Zend_Registry::get(Conjoon_Keys::DOCTRINE_ENTITY_MANAGER);

        $mailAccountRepository =
            $entityManager->getRepository('\Conjoon\Data\Entity\Mail\DefaultMailAccountEntity');
        $mailFolderRepository =
            $entityManager->getRepository('\Conjoon\Data\Entity\Mail\DefaultMailFolderEntity');
        $mesageFlagRepository =
            $entityManager->getRepository('\Conjoon\Data\Entity\Mail\DefaultMessageFlagEntity');
        $messageRepository =
            $entityManager->getRepository('\Conjoon\Data\Entity\Mail\DefaultMessageEntity');
        $attachmentRepository =
            $entityManager->getRepository('\Conjoon\Data\Entity\Mail\DefaultAttachmentEntity');

        $protocolAdaptee = new \Conjoon\Mail\Server\Protocol\DefaultProtocolAdaptee(
            $mailFolderRepository, $mesageFlagRepository, $mailAccountRepository,
            $messageRepository, $attachmentRepository
        );

        /**
         * @see \Conjoon\Mail\Server\Protocol\DefaultProtocol
         */
        $protocol = new \Conjoon\Mail\Server\Protocol\DefaultProtocol($protocolAdaptee);

        /**
         * @see \Conjoon\Mail\Server\DefaultServer
         */
        require_once 'Conjoon/Mail/Server/DefaultServer.php';

        $server = new \Conjoon\Mail\Server\DefaultServer($protocol);

        /**
         * @see \Conjoon\Mail\Client\Service\DefaultMessageServiceFacade
         */
        require_once 'Conjoon/Mail/Client/Service/DefaultMessageServiceFacade.php';

        $serviceFacade = new \Conjoon\Mail\Client\Service\DefaultMessageServiceFacade(
            $server, $mailAccountRepository, $mailFolderRepository
        );

        $result = $serviceFacade->getUnformattedMessage(
            $uId, $path, $appUser
        );

        if ($result->isSuccess()) {
            $d = $result->getData();
            return $d['message'];
        }

        return array();
    }


    protected function getGlobalNameForFolderType($account, $type)
    {
        $entityManager = Zend_Registry::get(Conjoon_Keys::DOCTRINE_ENTITY_MANAGER);

        $mailAccountRepository =
            $entityManager->getRepository('\Conjoon\Data\Entity\Mail\DefaultMailAccountEntity');

        $accEntity = $mailAccountRepository->findById($account->id);

        $mappings   = $accEntity->getFolderMappings();
        $globalName = "";
        for ($i = 0, $len = count($mappings); $i < $len; $i++) {
            if ($mappings[$i]->getType() == $type) {
                $globalName = $mappings[$i]->getGlobalName();
                break;
            }
        }

        return $globalName;
    }

}
