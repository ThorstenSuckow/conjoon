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
 * @see Conjoon_Mail
 */
require_once 'Conjoon/Mail.php';

/**
 * @see Zend_Mail_Transport_Smtp
 */
require_once 'Zend/Mail/Transport/Smtp.php';

/**
 * @see Conjoon_Modules_Groupware_Email_Address
 */
require_once 'Conjoon/Modules/Groupware/Email/Address.php';

/**
 * @see Conjoon_Version
 */
require_once 'Conjoon/Version.php';

/**
 * @see Conjoon_Mail_Sent
 */
require_once 'Conjoon/Mail/Sent.php';

/**
 * A utility class for sending emails.
 *
 * @category   Email
 * @package    Conjoon_Modules_Groupware
 * @subpackage Conjoon_Modules_Groupware_Email
 *
 * @author Thorsten-Suckow-Homberg <tsuckow@conjoon.org>
 */

class Conjoon_Modules_Groupware_Email_Sender {

    /**
     * Constructor.
     * Private to enforce static behavior
     */
    private function __construct()
    {
    }


    /**
     * Sends the draft for the specified account
     *
     * @param Conjoon_Modules_Groupware_Email_Draft $draft The draft to send
     * @param Conjoon_Modules_Groupware_Email_Account $account The account to
     * use to send this email with
     * @param array $postedAttachments an array with additional posted attachments
     * or attachments that need to get renamed
     * @param array $removeAttachmentIds An array with attachment ids that need
     * to be removed from the list of existing attachments
     *
     * @return Conjoon_Mail_Sent
     *
     * @throws Zend_Mail_Exception
     */
    public static function send(
        Conjoon_Modules_Groupware_Email_Draft $draft, Conjoon_Modules_Groupware_Email_Account $account,
        $postedAttachments = array(), $removeAttachmentIds = array()
     )
    {
        $mail = self::getAssembledMail($draft, $account, $postedAttachments, $removeAttachmentIds);

        $mailObject = $mail->getMailObject();

        $transport = $mail->getTransport();

        // Zend_Mail_Protocol_Abstract would not supress errors thrown by the native
        // stream_socket_client function, thus - depending on the setting of error_reporting -
        // a warning will bubble up if no internet conn is available while sending emails.
        // supress this error here.
        // An excpetion will be thrown right at this point if the message could not
        // be sent
        @$mailObject->send($transport);


        return $mail;
    }


    public static function getAssembledMail(
        Conjoon_Modules_Groupware_Email_Draft $draft, Conjoon_Modules_Groupware_Email_Account $account,
        $postedAttachments = array(), $removeAttachmentIds = array(), $userId
    )
    {
        $mail = new Conjoon_Mail('UTF-8');

        // let everyone know...
        $mail->addHeader('X-MailGenerator', 'conjoon ' . Conjoon_Version::VERSION);

        /**
         * Some clients need the MIME-Version header field. For example,
         * Outlook might have problems with decoding a message if no mime-version
         * is specified.
         */
        $mail->addHeader('MIME-Version', '1.0');


        // add recipients
        $to  = $draft->getTo();
        $cc  = $draft->getCc();
        $bcc = $draft->getBcc();
        foreach ($cc as $address) {
            $mail->addCc($address->getAddress(), $address->getName());
        }
        foreach ($to as $address) {
            $mail->addTo($address->getAddress(), $address->getName());
        }
        foreach ($bcc as $address) {
            $mail->addBcc($address->getAddress(), $address->getName());
        }

        $mail->setMessageId(true);

        // set sender
        $mail->setFrom($account->getAddress(), $account->getUserName());
        // set reply-to
        if ($account->getReplyAddress() != "") {
            $mail->setReplyTo($account->getReplyAddress());
        }

        // set in-reply-to
        if ($draft->getInReplyTo() != "") {
            $mail->setInReplyTo($draft->getInReplyTo());
        }

        // set references
        if ($draft->getReferences() != "") {
            $mail->setReferences($draft->getReferences());
        }

        // set date
        $mail->setDate($draft->getDate());

        // and the content
        $mail->setSubject($draft->getSubject());

        $plain = $draft->getContentTextPlain();
        $html  = $draft->getContentTextHtml();

        if ($plain === "" && $html === "") {
            $plain = " ";
        }

        if ($plain !== "") {
            $mail->setBodyText($plain);
        }
        if ($html !== "") {
            $mail->setBodyHtml($html);
        }

        self::_applyAttachments($draft, $mail, $postedAttachments, $removeAttachmentIds, $userId);

        // send!
        $config = array();
        if ($account->isOutboxAuth()) {
            $config = array(
                /**
                 * @todo allow for other auth methods as provided by ZF
                 */
                'auth'     => 'login',
                'username' => $account->getUsernameOutbox(),
                'password' => $account->getPasswordOutbox(),
                'port'     => $account->getPortOutbox()
            );

            $ssl = $account->getOutboxConnectionType();

            if ($ssl == 'SSL' || $ssl == 'TLS') {
                $config['ssl'] = $ssl;
            }
        }

        /**
         * @see \Conjoon\Mail\Transport\Smtp
         */
        require_once 'Conjoon/Mail/Transport/Smtp.php';

        $transport = new \Conjoon\Mail\Transport\Smtp(
            $account->getServerOutbox(), $config
        );

        return new Conjoon_Mail_Sent($mail, $transport);
    }

    /**
     * @static
     * @param Conjoon_Modules_Groupware_Email_Draft $draft
     * @param Conjoon_Mail $mail
     * @param array $postedAttachments
     * @param array $removeAttachmentIds
     * @param $userId
     * @throws RuntimeException
     */
    protected static function _applyAttachments(
        Conjoon_Modules_Groupware_Email_Draft $draft, Conjoon_Mail $mail,
        $postedAttachments = array(), $removeAttachmentIds = array(), $userId)
    {

        /**
         * @see Conjoon_Modules_Groupware_Files_File_Model_File
         */
        require_once 'Conjoon/Modules/Groupware/Files/File/Model/File.php';

        /**
         * @see Conjoon_Modules_Groupware_Email_Attachment_Model_Attachment
         */
        require_once 'Conjoon/Modules/Groupware/Email/Attachment/Model/Attachment.php';

        /**
         * @see Conjoon_Mime_Part
         */
        require_once 'Conjoon/Mime/Part.php';

        $fileModel       = new Conjoon_Modules_Groupware_Files_File_Model_File();
        $attachmentModel = new Conjoon_Modules_Groupware_Email_Attachment_Model_Attachment();

        $path = $draft->getPath();

        if (!empty($path)) {

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

            if (!empty($pathInfo) && $facade->isRemoteFolder($pathInfo['rootId'])) {
                if ($imapAccount && $facade->isRemoteFolder($pathInfo['rootId'])) {

                    // remote!
                    // we can ignore the removed attachments since a remote mail
                    // message will get assembled from ground up

                    foreach ($postedAttachments as $postedAttachment) {

                        if ($postedAttachment['metaType'] == 'file') {

                            $dbFile = $fileModel->getLobData(array(
                                'key' => $postedAttachment['key'],
                                'id' => $postedAttachment['orgId']
                            ));

                            if ($dbFile && !empty($dbFile)) {
                                $mail->addAttachment(self::_createAttachment(array(
                                    'encoding'  => null,
                                    'mime_type' => $dbFile['mime_type'],
                                    'key'       => $dbFile['key'],
                                    'id'        => $dbFile['id']
                                ), $postedAttachment['name'], $fileModel));

                                $dbFile = null;
                            }

                        } else if ($postedAttachment['metaType'] == 'emailAttachment') {

                            $attachment = self::getAttachmentFromServer(
                                $postedAttachment['key'],
                                $draft->getId(),
                                json_encode($path),
                                $userId
                            );

                            if ($attachment && $attachment->isSuccess() && $attachment->getData()) {

                                $attachmentData = $attachment->getData();

                                $mail->addAttachment(self::_createAttachment(array(
                                    'encoding'  => null,
                                    'mime_type' => $attachmentData['mimeType'],
                                    'key'       => $attachmentData['key'],
                                    'resource'  => $attachmentData['resource']
                                ), $postedAttachment['name'], null));

                            }
                        }
                    }

                    // remote mails done! exit
                    return;

                } else {
                    throw new RuntimeException("Cannot assemble attachments: No remote information found");
                }
            }
        }

        // first off, get all the attachments from the draft
        $draftAttachments = $draft->getAttachments();

        $postedEmailAttachmentIds   = array();
        $existingEmailAttachmentIds = array();
        $postedFilesIds             = array();

        $finalPostedFiles         = array();
        $finalPostedAttachments   = array();
        $finalExistingAttachments = array();

        $orgAttachmentIdsToPost = array();

        //get ids for emailAttachments
        for ($i = 0, $len = count($postedAttachments); $i < $len; $i++) {

            if ($postedAttachments[$i]['metaType'] == 'emailAttachment') {
                $postedEmailAttachmentIds[] = $postedAttachments[$i]['orgId'];
                $finalPostedAttachments[$postedAttachments[$i]['orgId']] =
                    $postedAttachments[$i];
            } else {
                $postedFilesIds[] = $postedAttachments[$i]['orgId'];
                $finalPostedFiles[$postedAttachments[$i]['orgId']] =
                    $postedAttachments[$i];
            }
        }
        for ($i = 0, $len = count($draftAttachments); $i < $len; $i++) {
            // intersect will be created later
            $existingEmailAttachmentIds[] = $draftAttachments[$i]->getId();

            if (in_array($draftAttachments[$i]->getId(), $removeAttachmentIds)) {
                continue;
            }

            $finalExistingAttachments[$draftAttachments[$i]->getId()] =
                $draftAttachments[$i];
        }

        // finally create the intersection of all ids that are in the
        // lists of items to remove and in the list of existing items
        $removeAttachmentIds = array_values(array_intersect($removeAttachmentIds,
            $existingEmailAttachmentIds
        ));

        // get the ids from the attachments that need to get changed
        $changeNameIds = array_values(array_intersect(
            $postedEmailAttachmentIds, $existingEmailAttachmentIds
        ));

        // get the ids from the attachments that need to get saved, i.e.
        // when a draft was created with email attachments which currently
        // beong to another email
        $copyAttachmentIds = array_values(array_diff(
            $postedEmailAttachmentIds, $existingEmailAttachmentIds
        ));

        // take care of getting the attachment ids that currently belong to
        // another item
        for ($i = 0, $len = count($copyAttachmentIds); $i < $len; $i++) {
            $id = $copyAttachmentIds[$i];
            $att = $attachmentModel->getAttachmentDataForKeyAndId(
                $finalPostedAttachments[$id]['key'], $id
            );

            if ($att && !empty($att)) {
                $mail->addAttachment(self::_createAttachment(
                    $att, $finalPostedAttachments[$id]['name'], $attachmentModel
                ));
                $att = null;
            }
        }

        // take care of renaming attachments
        $cnids = array();
        for ($i = 0, $len = count($changeNameIds); $i < $len; $i++) {
            $id = $changeNameIds[$i];

            if ($finalExistingAttachments[$id]->getFileName()
                != $finalPostedAttachments[$id]['name']) {

                $att = $attachmentModel->getAttachmentDataForKeyAndId(
                    $finalPostedAttachments[$id]['key'], $id
                );

                if ($att && !empty($att)) {
                    $mail->addAttachment(self::_createAttachment(
                        $att, $finalPostedAttachments[$id]['name'], $attachmentModel
                    ));
                    $att = null;
                }

                $cnids[] = $id;
            }
        }


        // finally, get the ids from the attachments that are neither in
        // $changeNameIds nor in $removeAttachmentIds
        $orgAttachmentIdsToPost = array_values(array_diff(
            $existingEmailAttachmentIds, $cnids,
            $removeAttachmentIds
        ));

        // take care of org attachmentIds
        for ($i = 0 , $len = count($orgAttachmentIdsToPost); $i < $len; $i++) {
            $id = $orgAttachmentIdsToPost[$i];

            $att = $attachmentModel->getAttachmentDataForKeyAndId(
                $finalExistingAttachments[$id]->getKey(),
                $finalExistingAttachments[$id]->getId()
            );

            if ($att && !empty($att)) {
                $mail->addAttachment(self::_createAttachment(
                    $att, $att['file_name'], $attachmentModel
                ));
                $att = null;
            }
        }

        // copy files to attachments
        foreach ($finalPostedFiles as $id => $file) {

            $dbFile = $fileModel->getFileDataForKeyAndId(
                $file['key'], $file['orgId']
            );

            if ($dbFile && !empty($dbFile)) {
                $mail->addAttachment(self::_createAttachment(array(
                    'encoding'  => '',
                    'mime_type' => $dbFile['mime_type'],
                    'key'       => $dbFile['key'],
                    'id'        => $dbFile['id']
                ), $file['name'], $fileModel));

                $dbFile = null;
            }
        }

    }


    protected static function _createAttachment(Array $att, $name, $model = null)
    {
        /**
         * @see Conjoon_Mime_Part
         */
        require_once 'Conjoon/Mime/Part.php';

        $validEncoding = ($att['encoding'] == 'quoted-printable'
                         || $att['encoding'] == 'base64');


        if ($model && ($model instanceof
            Conjoon_Modules_Groupware_Email_Attachment_Model_Attachment)) {
            $newAttachment = new Conjoon_Mime_Part(
                $model->getAttachmentContentAsStreamForKeyAndId(
                    $att['key'], $att['id']
                ), $validEncoding
            );
        } else if ($model && ($model instanceof
            Conjoon_Modules_Groupware_Files_File_Model_File)) {
            $newAttachment = new Conjoon_Mime_Part(
                $model->getFileContentAsStreamForKeyAndId(
                    $att['key'], $att['id']
                ), $validEncoding
            );
        } else {
            if (!array_key_exists('resource', $att)) {
               throw new RuntimeException(
                   'Expected "resource" in argument, but was not available'
               );
            }

            $newAttachment = new Conjoon_Mime_Part(
                $att['resource'], $validEncoding
            );
        }

        $newAttachment->encoding    = $validEncoding
                                      ? $att['encoding']
                                      : Zend_Mime::ENCODING_BASE64;
        $newAttachment->type        = $att['mime_type']
                                     ? $att['mime_type']
                                     : 'text/plain';
        $newAttachment->disposition = Zend_Mime::DISPOSITION_ATTACHMENT;
        $newAttachment->filename    = $name;

        return $newAttachment;
    }

    /**
     * Helper function for fetching a single attachment
     *
     * @param string $key The key of the attachment
     * @param string $uId The message id of the message
     * @param string $path The json encoded path where the message can be found
     */
    protected static function getAttachmentFromServer($key, $uId, $path, $userId)
    {
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

        if ($appUser->getId() != $userId) {
            throw new RuntimeException(
                "current user not the same as user id found in argument"
            );
        }

        $entityManager = Zend_Registry::get(Conjoon_Keys::DOCTRINE_ENTITY_MANAGER);

        $mailFolderRepository =
            $entityManager->getRepository('\Conjoon\Data\Entity\Mail\DefaultMailFolderEntity');
        $mailAccountRepository =
            $entityManager->getRepository('\Conjoon\Data\Entity\Mail\DefaultMailAccountEntity');
        $messageFlagRepository =
            $entityManager->getRepository('\Conjoon\Data\Entity\Mail\DefaultMessageFlagEntity');
        $messageRepository =
            $entityManager->getRepository('\Conjoon\Data\Entity\Mail\DefaultMessageEntity');
        $attachmentRepository =
            $entityManager->getRepository('\Conjoon\Data\Entity\Mail\DefaultAttachmentEntity');

        $protocolAdaptee = new \Conjoon\Mail\Server\Protocol\DefaultProtocolAdaptee(
            $mailFolderRepository, $messageFlagRepository,
            $mailAccountRepository, $messageRepository, $attachmentRepository
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


        $result = $serviceFacade->getAttachment($key, $uId, $path, $appUser);

        if ($result->isSuccess()) {
            return $result;
        }

        return null;
    }


}
