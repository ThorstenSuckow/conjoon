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
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
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

        $downloadCookieName = $this->_request->getParam('downloadCookieName');

        if ($type == 'emailAttachment') {
            /**
             * @see Conjoon_Modules_Groupware_Email_Attachment_Facade
             */
            require_once 'Conjoon/Modules/Groupware/Email/Attachment/Facade.php';

            $facade = Conjoon_Modules_Groupware_Email_Attachment_Facade::getInstance();

            $data = $facade->getAttachmentDownloadDataForUserId(
                $key, $id, $userId
            );
        } else {
            /**
             * @see Conjoon_Modules_Groupware_Files_Facade
             */
            require_once 'Conjoon/Modules/Groupware/Files/Facade.php';

            $facade = Conjoon_Modules_Groupware_Files_Facade::getInstance();

            $data = $facade->getFileDownloadDataForUserId(
                $key, $id, $userId
            );

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

        $this->_helper->viewRenderer->setNoRender();


        $response = $this->getResponse();
        $response->clearAllHeaders();

        setcookie($downloadCookieName, 'downloading', 0,  '/');

        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
                 ->setHeader('Expires', 'Mon, 26 Jul 1997 05:00:00 GMT', true)
                 ->setHeader('Pragma', 'no-cache', true)
                 ->setHeader('Content-Description', ($name != "" ? $name : $data['name']), true)
                 ->setHeader('Content-Type', $data['mimeType'], true)
                 ->setHeader('Content-Transfer-Encoding', 'binary', true)
                 ->setHeader(
                    'Content-Disposition',
                    'attachment; filename="'
                    .addslashes($name != "" ? $name : $data['name'])
                    .'"',
                    true
                 );

        $response->sendHeaders();
        $response->setBody($data['content']);
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
         * @see Zend_Registry
         */
        require_once 'Zend/Registry.php';

        /**
         * @see Conjoon_Keys
         */
        require_once 'Conjoon/Keys.php';

        /**
         * @see Zend_File_Transfer
         */
        require_once 'Zend/File/Transfer/Adapter/Http.php';

        $config = Zend_Registry::get(Conjoon_Keys::REGISTRY_CONFIG_OBJECT);
        $maxAllowedPacket = $config->database->variables->max_allowed_packet;
        if (!$maxAllowedPacket) {
            /**
             * @see Conjoon_Db_Util
             */
            require_once 'Conjoon/Db/Util.php';

            $maxAllowedPacket = Conjoon_Db_Util::getMaxAllowedPacket(
                Zend_Db_Table::getDefaultAdapter()
            );
        }

        $maxFileSize = min(
            (float)$config->application->files->upload->max_size,
            (float)$maxAllowedPacket
        );

        // allowed filesize is max-filesize - 33-36 % of max filesize,
        // due to base64 encoding which might happen
        $maxFileSize = $maxFileSize - round($maxFileSize/3);

        // build up upload
        $upload = new Zend_File_Transfer_Adapter_Http();

        // assign and check validators
        $upload->addValidator('Count', true, array('min' => 1, 'max' => 1));
        $upload->addValidator('Size', true, $maxFileSize);
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
                implode("\n", $messages),
                Conjoon_Error::LEVEL_WARNING, Conjoon_Error::INPUT,
                null, null, null
            )->getDto();
            $this->view->success = false;
            $this->view->error   = $error;
            return;
        }

        // extract file info
        $fileInfo = array_pop($upload->getFileInfo());
        $name     = $fileInfo['name'];
        $content  = @file_get_contents($fileInfo['tmp_name']);

        if ($content === false) {
            /**
             * @see Conjoon_Error_Factory
             */
            require_once 'Conjoon/Error/Factory.php';

            $error = Conjoon_Error_Factory::createError(
                "Could not get file contents to save \"".$name."\"",
                Conjoon_Error::LEVEL_WARNING, Conjoon_Error::INPUT,
                null, null, null
            )->getDto();
            $this->view->success = false;
            $this->view->error   = $error;
            return;
        }

        $type = $fileInfo['type'];

        /**
         * @see Conjoon_Modules_Groupware_Files_Facade
         */
        require_once 'Conjoon/Modules/Groupware/Files/Facade.php';

        $fileFacade = Conjoon_Modules_Groupware_Files_Facade::getInstance();

        $file = $fileFacade->addFileToTempFolderForUser(
            $name, $content, $type,
            $this->_helper->registryAccess->getUserId()
        );

        if ($file === null) {
            /**
             * @see Conjoon_Error_Factory
             */
            require_once 'Conjoon/Error/Factory.php';

            $error = Conjoon_Error_Factory::createError(
                "Could not upload file \"".$name."\"",
                Conjoon_Error::LEVEL_WARNING, Conjoon_Error::INPUT,
                null, null, null
            )->getDto();
            $this->view->success = false;
            $this->view->error   = $error;
            return;
        }

        // we will silently add the old id to Dto so the client can identify the
        // uploaded record properly
        $file->oldId         = $fileKey;
        $file->folderId      = $file->groupwareFilesFoldersId;
        unset($file->groupwareFilesFoldersId);

        $this->view->success = true;
        $this->view->files   = array($file);
    }


}