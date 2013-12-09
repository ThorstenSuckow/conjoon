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
 * Zend_Controller_Action
 */
require_once 'Zend/Controller/Action.php';


/**
 *
 *    +----------------------------------------+
 *    | +------------------------------------+ |
 *    | |                                    | |
 *    | |     ,'";-------------------;"`.    | |
 *    | |     ;[]; ................. ;[];    | |
 *    | |     ;  ; ................. ;  ;    | |
 *    | |     ;  ; ................. ;  ;    | |
 *    | |     ;  ; ................. ;  ;    | |
 *    | |     ;  ; ................. ;  ;    | |
 *    | |     ;  ; ................. ;  ;    | |
 *    | |     ;  ; ................. ;  ;    | |
 *    | |     ;  `.                 ,'  ;    | |
 *    | |     ;    """""""""""""""""    ;    | |
 *    | |     ;    ,-------------.---.  ;    | |
 *    | |     ;    ;  ;"";       ;   ;  ;    | |
 *    | |     ;    ;  ;  ;       ;   ;  ;    | |
 *    | |     ;    ;  ;  ;       ;   ;  ;    | |
 *    | |     ;//||;  ;  ;       ;   ;||;    | |
 *    | |     ;\\||;  ;__;       ;   ;\/;    | |
 *    | |     `. _;          _  ;  _;  ;     | |
 *    | |       " """"""""""" """"" """      | |
 *    | |                                    | |
 *    | +------------------------------------+ |
 *    |                                        |
 *    |        UPLOADING/DOWNLOADING           |
 *    |                                        |
 *    |              'nuff said                |
 *    +----------------------------------------+
 *
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Groupware_FileController extends Zend_Controller_Action {

    const CONTEXT_JSON      = 'json';
    const CONTEXT_JSON_HTML = 'jsonHtml';

    /**
     * Inits this controller and sets the context-switch-directives
     * on the various actions.
     *
     */
    public function init()
    {
        $this->_helper->filterRequestData()
                      ->registerFilter('Groupware_FileController::download.file');

        $conjoonContext = $this->_helper->conjoonContext()
                          ->addActionContext('upload.file', self::CONTEXT_JSON_HTML)
                          ->initContext();
    }

    /**
     * Sets header to the mime type of the attachment as queried from the
     * database and tries to send the file contents to the client.
     * To identify the attachment, the action needs the parameters "key"
     * and "id" as found in the data model of the attachments.
     *
     */
    public function downloadFileAction()
    {
        $id     = $this->_request->getParam('id');
        $key    = $this->_request->getParam('key');
        $userId = $this->_helper->registryAccess->getUserId();
        $type   = $this->_request->getParam('type');
        $name   = $this->_request->getParam('name');

        $mimeType = false;

        $uId = $this->_request->getParam('uId');
        $path      = $this->_request->getParam('path');

        $data = null;

        if ($path) {
            $path = urldecode($path);
        }

        $downloadCookieName = $this->_request->getParam('downloadCookieName');

        if ($type == 'emailAttachment') {

            $data = $this->getAttachmentFromServer($key, $uId, $path);

            $mimeType = $data['mimeType'];

        } else {
            /**
             * @see Conjoon_Modules_Groupware_Files_File_Facade
             */
            require_once 'Conjoon/Modules/Groupware/Files/File/Facade.php';

            $facade = Conjoon_Modules_Groupware_Files_File_Facade::getInstance();

            $data = $facade->getFileDownloadDataForUserId(
                $id, $key, $userId
            );

            $mimeType = $data['mime_type'];

        }

        if (!$data) {
            /**
             * @see Conjoon_Exception
             */
            require_once 'Conjoon/Exception.php';

            // we'll throw an exception, that's okay for now
            throw new Conjoon_Exception("Sorry, but the requested file is not available.");

            return;
        }

        if ($mimeType === false) {
            /**
             * @see Conjoon_Exception
             */
            require_once 'Conjoon/Exception.php';

            throw new Conjoon_Exception(
                "Sorry, but the \"mime type\" is missing. Make sure \"mimeType\" for attachments " .
                "is available, or \"mime_type\" for files."
            );

            return;

        }

        $this->_helper->viewRenderer->setNoRender();


        $response = $this->getResponse();
        $response->clearAllHeaders();

        setcookie($downloadCookieName, 'downloading', 0,  '/');

        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
                 ->setHeader('Expires', 'Mon, 26 Jul 1997 05:00:00 GMT', true)
                 ->setHeader('Pragma', 'no-cache', true)
                 ->setHeader('Content-Description', ($name != "" ? $name : $data['name']), true)
                 ->setHeader('Content-Type', $mimeType, true)
                 ->setHeader('Content-Transfer-Encoding', 'binary', true)
                 ->setHeader(
                    'Content-Disposition',
                    'attachment; filename="'
                    .addslashes($name != "" ? $name : $data['name'])
                    .'"',
                    true
                 );

        $response->sendHeaders();
        $response->setBody($data['resource']);
    }

    /**
     * Controller action for uploading files.
     * The method allows for only one file to be processed. It will send back
     * either an error obejct to the view or a File_Dto upon successfull
     * processing.
     *
     */
    public function uploadFileAction()
    {
        // first of, extract the file key
        $fileKey = array_pop(array_keys($_FILES));

        /**
         * @see Conjoon_Modules_Groupware_Files_Facade
         */
        require_once 'Conjoon/Modules/Groupware/Files/Facade.php';

        $facade = Conjoon_Modules_Groupware_Files_Facade::getInstance();

        $upload = $facade->generateUploadObject();

        if (!$upload->isValid()) {
            // generate the error message
            $message  = $upload->getMessages();
            $messages = array();
            foreach ($message as $m) {
                $messages[] = $m;
            }

            /**
             * @see Conjoon_Error_Factory
             */
            require_once 'Conjoon/Error/Factory.php';

            $error = Conjoon_Error_Factory::createError(
                implode("\n", $messages), Conjoon_Error::LEVEL_WARNING,
                Conjoon_Error::INPUT)->getDto();
            $this->view->success = false;
            $this->view->error   = $error;
            return;
        }

        $fileDto = $facade->uploadFileToTempFolderForUser(
            $upload, $this->_helper->registryAccess->getUserId()
        );

        if (!$fileDto) {
            /**
             * @see Conjoon_Error_Factory
             */
            require_once 'Conjoon/Error/Factory.php';

            $error = Conjoon_Error_Factory::createError(
                "Sorry, I could not upload this file. Something went wrong",
                Conjoon_Error::LEVEL_WARNING, Conjoon_Error::INPUT
            )->getDto();
            $this->view->success = false;
            $this->view->error   = $error;
            return;
        }

        // we will silently add the old id to Dto so the client can identify the
        // uploaded record properly
        $fileDto->oldId    = $fileKey;
        $fileDto->folderId = $fileDto->groupwareFilesFoldersId;
        unset($fileDto->groupwareFilesFoldersId);

        $this->view->success = true;
        $this->view->files   = array($fileDto);
    }

// -------- helper
    /**
     * Helper function for fetching a single attachment
     *
     * @param string $key The key of the attachment
     * @param string $uId The message id of the message
     * @param string $path The json encoded path where the message can be found
     */
    protected function getAttachmentFromServer($key, $uId, $path)
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
            return $result->getData();
        }

        return null;
    }


}
