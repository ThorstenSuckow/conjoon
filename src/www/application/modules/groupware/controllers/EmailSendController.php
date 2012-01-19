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
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Groupware_EmailSendController extends Zend_Controller_Action {

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
     *
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


        require_once 'Conjoon/Modules/Groupware/Email/Address.php';
        require_once 'Conjoon/Modules/Groupware/Email/Draft.php';
        require_once 'Conjoon/BeanContext/Inspector.php';

        // create the message object here
        $to  = array();
        $cc  = array();
        $bcc = array();

        $toString  = array();
        $ccString  = array();

        foreach ($data['cc'] as $dcc) {
            $add        = new Conjoon_Modules_Groupware_Email_Address($dcc);
            $cc[]       = $add;
            $toString[] = $add->__toString();
        }
        foreach ($data['bcc'] as $dbcc) {
            $add         = new Conjoon_Modules_Groupware_Email_Address($dbcc);
            $bcc[]       = $add;
        }
        foreach ($data['to'] as $dto) {
            $add        = new Conjoon_Modules_Groupware_Email_Address($dto);
            $to[]       = $add;
            $toString[] = $add->__toString();
        }

        $toString  = implode(', ', $toString);
        $ccString  = implode(', ', $ccString);

        $data['cc']  = $cc;
        $data['to']  = $to;
        $data['bcc'] = $bcc;

        // get the specified account for the user
        require_once 'Conjoon/BeanContext/Decorator.php';
        require_once 'Conjoon/Keys.php';

        $accountDecorator = new Conjoon_BeanContext_Decorator(
            'Conjoon_Modules_Groupware_Email_Account_Model_Account'
        );

        $auth   = Zend_Registry::get(Conjoon_Keys::REGISTRY_AUTH_OBJECT);
        $userId = $auth->getIdentity()->getId();

        $account = $accountDecorator->getAccountAsEntity($data['groupwareEmailAccountsId'], $userId);

        // no account found?
        if (!$account) {
            require_once 'Conjoon/Error.php';
            $error = new Conjoon_Error();
            $error = $error->getDto();;
            $error->title = 'Error while sending email';
            $error->message = 'Could not find specified account.';
            $error->level = Conjoon_Error::LEVEL_ERROR;
            $this->view->error   = $error;
            $this->view->success = false;
            $this->view->item    = null;
            return;
        }

        $message = Conjoon_BeanContext_Inspector::create(
                'Conjoon_Modules_Groupware_Email_Draft',
                $data,
                true
        );

        require_once 'Conjoon/Modules/Groupware/Email/Sender.php';

        try {
            $mail = Conjoon_Modules_Groupware_Email_Sender::send($message, $account);
        } catch (Exception $e) {
            require_once 'Conjoon/Error.php';
            $error = new Conjoon_Error();
            $error = $error->getDto();;
            $error->title = 'Error while sending email';
            $error->message = $e->getMessage();
            // check here if a message is set. We rely heavily on stream_socket_client
            // in Zend_Mail_Protocol_Abstract which may not set the error message all
            // the time. If no internet conn is available, the message will be missing
            // on windows systems, for example
            if ($error->message == "") {
                $error->message = "The message with the subject \""
                                  . $message->getSubject()."\" could not be sent. "
                                  . "Please check the internet connection of "
                                  . "the server this software runs on.";
            }
            $error->level = Conjoon_Error::LEVEL_ERROR;
            $this->view->error   = $error;
            $this->view->success = false;
            $this->view->item    = null;
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
            $message, $account, $userId, $mail, $data['type'], $data['referencesId']
        );

        if (!$item) {
            require_once 'Conjoon/Error.php';
            $error = new Conjoon_Error();
            $error = $error->getDto();;
            $error->title = 'Error while saving email';
            $error->message = 'The email was sent, but it could not be stored into the database.';
            $error->level = Conjoon_Error::LEVEL_ERROR;
            $this->view->error   = $error;
            $this->view->success = false;
            $this->view->item    = null;
            return;
        }

        // if the sent email referenced an existing message, tr to fetch this message
        // and send it along as context-referenced item

        $contextReferencedItem = $itemDecorator->getReferencedItemAsDto(
            $item->id,
            $userId
        );


        $this->view->error   = null;
        $this->view->success = true;
        $this->view->item    = $item;
        $this->view->contextReferencedItem  = empty($contextReferencedItem)
                                            ? null
                                            : $contextReferencedItem;
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

        $sendItems              = array();
        $contextReferencedItems = array();

        foreach ($toSend as $id) {

            $id = (int)$id;

            if ($id <= 0) {
                continue;
            }

            $rawDraft = $draftModel->getDraft($id, $userId);

            if (empty($rawDraft)) {
                continue;
            }

            Conjoon_Util_Array::camelizeKeys($rawDraft);


            $account = $accountDecorator->getAccountAsEntity(
                $rawDraft['groupwareEmailAccountsId'],
                $userId
            );

            // no account found?
            if (!$account) {
                /**
                 * @todo think about using the standard account as a fallback or use at last
                 * an error message to inform the user that the account used to write this email
                 * is not available anymore
                 */
                continue;
            }

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
                $mail = Conjoon_Modules_Groupware_Email_Sender::send($message, $account);
            } catch (Exception $e) {
                continue;
            }

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

        $this->view->success   = true;
        $this->view->sentItems = $sendItems;
        $this->view->error     = null;
        $this->view->contextReferencedItems = $contextReferencedItems;

    }


}