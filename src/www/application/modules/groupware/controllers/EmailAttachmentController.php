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
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Groupware_EmailAttachmentController extends Zend_Controller_Action {

    const CONTEXT_JSON = 'json';

    /**
     * Inits this controller and sets the context-switch-directives
     * on the various actions.
     *
     */
    public function init()
    {
        $this->_helper->filterRequestData()
                      ->registerFilter('Groupware_EmailAttachmentController::download.attachment');

        /*$conjoonContext = $this->_helper->conjoonContext();

        $conjoonContext->addActionContext('add.email.account',     self::CONTEXT_JSON)
                       ->addActionContext('get.email.accounts',    self::CONTEXT_JSON)
                       ->addActionContext('update.email.accounts', self::CONTEXT_JSON)
                       ->initContext();*/
    }

    public function uploadAttachmentAction()
    {
        sleep(30);
    }

    /**
     * Sets header to the mime type of the attachment as queried from the
     * database and tries to send the file contents to the client.
     * To identify the attachment, the action needs the parameters "key"
     * and "id" as found in the data model of the attachments.
     *
     */
    public function downloadAttachmentAction()
    {
        $attachmentId  = $this->_request->getParam('id');
        $attachmentKey = $this->_request->getParam('key');
        $userId        = $this->_helper->registryAccess->getUserId();

        $downloadCookieName = $this->_request->getParam('downloadCookieName');

        /**
         * @see Conjoon_Modules_Groupware_Email_Attachment_Facade
         */
        require_once 'Conjoon/Modules/Groupware/Email/Attachment/Facade.php';

        $facade = Conjoon_Modules_Groupware_Email_Attachment_Facade::getInstance();

        $data = $facade->getAttachmentDownloadDataForUserId(
            $attachmentKey, $attachmentId, $userId
        );

        if (!$data) {
            /**
             * @see Conjoon_Exception
             */
            require_once 'Conjoon/Exception.php';

            // we'll throw an exception, that's okay for now
            throw new Conjoon_Exception("Sorry, but the requested attachment is not available.");

            return;
        }

        $this->_helper->viewRenderer->setNoRender();


        $response = $this->getResponse();
        $response->clearAllHeaders();

        setcookie($downloadCookieName, 'downloading', 0,  '/');

        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
                 ->setHeader('Expires', 'Mon, 26 Jul 1997 05:00:00 GMT', true)
                 ->setHeader('Pragma', 'no-cache', true)
                 ->setHeader('Content-Description', $data['fileName'], true)
                 ->setHeader('Content-Type', $data['mimeType'], true)
                 ->setHeader('Content-Transfer-Encoding', 'binary', true)
                 ->setHeader(
                    'Content-Disposition',
                    'attachment; filename="'.addslashes($data['fileName']).'"',
                    true
                 );

        $response->sendHeaders();
        $response->setBody($data['content']);
    }

}