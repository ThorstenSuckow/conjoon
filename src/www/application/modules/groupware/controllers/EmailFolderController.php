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
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Groupware_EmailFolderController extends Zend_Controller_Action {

    const CONTEXT_JSON = 'json';

    /**
     * Inits this controller and sets the context-switch-directives
     * on the various actions.
     *
     */
    public function init()
    {
        $conjoonContext = $this->_helper->conjoonContext();

        $conjoonContext->addActionContext('get.folder',    self::CONTEXT_JSON)
                       ->addActionContext('rename.folder', self::CONTEXT_JSON)
                       ->addActionContext('add.folder',    self::CONTEXT_JSON)
                       ->addActionContext('move.folder',   self::CONTEXT_JSON)
                       ->addActionContext('delete.folder', self::CONTEXT_JSON)
                       ->initContext();

        $this->_helper->filterRequestData()
                      ->registerFilter('Groupware_EmailFolderController::rename.folder')
                      ->registerFilter('Groupware_EmailFolderController::move.folder')
                      ->registerFilter('Groupware_EmailFolderController::get.folder')
                      ->registerFilter('Groupware_EmailFolderController::add.folder');
    }

    /**
     * Returns the email folder for a user. A user has always at least the
     * following nodes available in his mail application for storing emails:
     *
     * rootNode    => id  : root   | 0 custom children
     *     Inbox   => id  : inbox  | 0..n custom children
     *     Spam    => id  : spam   | 0..n custom children
     *     Outbox  => id  : outbox | 0 custom children
     *     Sent    => id  : sent   | 0..n custom children
     *     Trash   => id  : trash  | 0..n custom children
     *
     *
     * Each folder except the rootNode and the Outbox can have any amount of
     * children appended, which itself may be nested to any level.
     *
     * The method returns the folders which are direct childnodes, along with
     * the number of emails which remain unread in this folder yet. This attribute
     * is called "pending" since it also denotes remaining emails in the outbox
     * folder which have not been send yet.
     *
     * The param of the id of the node, which children to return, is stored in
     * the supplied Post parameter. The initial request will have the id 'root'
     * or "0".
     * It is up to the application to read out the root's property id mapped to
     * the logged in user out of the database and return the associated childs
     * accordingly.
     *
     * NOTE:
     * Folder names' will be delivered unescaped to the client, so the client has to
     * take care of appropriate html-encoding the given names.
     *
     */
    public function getFolderAction()
    {
        /**
         * @see Conjoon_Text_Parser_Mail_MailboxFolderPathJsonParser
         */
        require_once 'Conjoon/Text/Parser/Mail/MailboxFolderPathJsonParser.php';

        $parser = new Conjoon_Text_Parser_Mail_MailboxFolderPathJsonParser();

        try {
            $pathParts = $parser->parse($this->_request->getParam('path'));
        } catch (Conjoon_Text_Parser_Exception $e) {
            /**
             * @see Conjoon_Error
             */
            require_once 'Conjoon/Error.php';

            $error = Conjoon_Error::fromException($e);
            $this->view->success = false;
            $this->view->error   = $error->getDto();
            return;
        }

        /**
         * @see Conjoon_Modules_Groupware_Email_Folder_Facade
         */
        require_once 'Conjoon/Modules/Groupware/Email/Folder/Facade.php';

        $facade = Conjoon_Modules_Groupware_Email_Folder_Facade::getInstance();

        $userId = $this->_helper->registryAccess->getUserId();

        $groupwareEmailAccountsId
            = (int) $this->_request->getParam('groupwareEmailAccountsId');

        $groupwareEmailAccountsId = $groupwareEmailAccountsId
                                    ? $groupwareEmailAccountsId
                                    : null;

        $folders = $facade->getFoldersForPathAndUserId(
            $pathParts, $userId, $groupwareEmailAccountsId
        );

        $this->view->success = true;
        $this->view->error   = null;
        $this->view->items   = $folders;
    }

    /**
     * Deletes a folder permanently
     *
     * POST:
     * - integer id : the id of the folder to delete
     */
    public function deleteFolderAction()
    {
        require_once 'Conjoon/Modules/Groupware/Email/Folder/Filter/Folder.php';
        $filter = new Conjoon_Modules_Groupware_Email_Folder_Filter_Folder(
            $_POST,
            Conjoon_Modules_Groupware_Email_Folder_Filter_Folder::CONTEXT_DELETE
        );

        $filteredData = array();
        try {
            $filteredData = $filter->getProcessedData();
        } catch (Zend_Filter_Exception $e) {
            require_once 'Conjoon/Error.php';
            $error = Conjoon_Error::fromFilter($filter, $e);
            $this->view->success = false;
            $this->view->error   = $error->getDto();
            return;
        }

        require_once 'Conjoon/Keys.php';
        $user   = Zend_Registry::get(Conjoon_Keys::REGISTRY_AUTH_OBJECT)->getIdentity();
        $userId = $user->getId();

        /**
         * @see Conjoon_Modules_Groupware_Email_Folder_Facade
         */
        require_once 'Conjoon/Modules/Groupware/Email/Folder/Facade.php';

        $facade = Conjoon_Modules_Groupware_Email_Folder_Facade::getInstance();

        $ret = $facade->deleteLocalFolderForUser($filteredData['id'], $userId);

        if (!$ret) {
            require_once 'Conjoon/Error.php';
            $error = new Conjoon_Error();
            $error = $error->getDto();
            $error->title   = 'Error';
            $error->level   = Conjoon_Error::LEVEL_WARNING;
            $error->message = 'Could not delete the specified folder.';
            $this->view->success = false;
            $this->view->error   = $error;
            return;
        }

        $this->view->ret     = $ret;
        $this->view->success = true;
        $this->view->error   = null;
    }

    /**
     * Moves a folder into a new folder.
     * POST:
     *  parentId   : the id of the new folder this folder gets moved into
     *  id         : the id of this folder thats about being moved
     *  path       : the complete path of the folder to move
     *  parentPath : the complete path of the new parent node
     *
     */
    public function moveFolderAction()
    {
        /**
         * @see Conjoon_Modules_Groupware_Email_Folder_Facade
         */
        require_once 'Conjoon/Modules/Groupware/Email/Folder/Facade.php';

        $facade = Conjoon_Modules_Groupware_Email_Folder_Facade::getInstance();

        /**
         * @see Conjoon_Text_Parser_Mail_MailboxFolderPathJsonParser
         */
        require_once 'Conjoon/Text/Parser/Mail/MailboxFolderPathJsonParser.php';

        $parser = new Conjoon_Text_Parser_Mail_MailboxFolderPathJsonParser();

        try {
            $pathParts = $parser->parse(
                $this->_request->getParam('path')
            );
            $parentPathParts = $parser->parse(
                $this->_request->getParam('parentPath')
            );
        } catch (Conjoon_Text_Parser_Exception $e) {
            /**
             * @see Conjoon_Error
             */
            require_once 'Conjoon/Error.php';

            $error = Conjoon_Error::fromException($e);
            $this->view->success = false;
            $this->view->error   = $error->getDto();
            return;
        }


        $userId = $this->_helper->registryAccess->getUserId();

        try {
            $folder = $facade->moveFolderFromPathToPathForUserId(
                $pathParts, $parentPathParts, $userId);

            if ($folder === false) {
                /**
                 * @see Conjoon_Error_Factory
                 */
                require_once 'Conjoon/Error/Factory.php';

                $error = Conjoon_Error_Factory::createError(
                    "Could not move the specified folder.",
                    Conjoon_Error::LEVEL_WARNING
                )->getDto();

                $this->view->success = false;
                $this->view->error   = $error;
                return;
            }

        } catch (Exception $e) {
            /**
             * @see Conjoon_Error
             */
            require_once 'Conjoon/Error.php';

            $this->view->success = true;
            $this->view->error   = Conjoon_Error::fromException($e)->getDto();

            return;
        }

        $this->view->success = true;
        $this->view->error   = null;
        $this->view->folder  = $folder;
    }

    /**
     * Adds a new folder to the folder with the specified id.
     * POST:
     * parentId : the id of the parent folder to which the new folder should get
     * appended
     * name : the name of the folder
     * path : The path to the parent node of the node that should get created
     *
     * The method will assign a view-id property called "id", which holds the
     * id of the newly added folder.
     */
    public function addFolderAction()
    {
        /**
         * @see Conjoon_Modules_Groupware_Email_Folder_Facade
         */
        require_once 'Conjoon/Modules/Groupware/Email/Folder/Facade.php';

        $facade = Conjoon_Modules_Groupware_Email_Folder_Facade::getInstance();

        /**
         * @see Conjoon_Text_Parser_Mail_MailboxFolderPathJsonParser
         */
        require_once 'Conjoon/Text/Parser/Mail/MailboxFolderPathJsonParser.php';

        $parser = new Conjoon_Text_Parser_Mail_MailboxFolderPathJsonParser();

        try {
            $pathParts = $parser->parse($this->_request->getParam('path'));
        } catch (Conjoon_Text_Parser_Exception $e) {
            /**
             * @see Conjoon_Error
             */
            require_once 'Conjoon/Error.php';

            $error = Conjoon_Error::fromException($e);
            $this->view->success = false;
            $this->view->error   = $error->getDto();
            return;
        }

        $name   = $this->_request->getParam('name');
        $userId = $this->_helper->registryAccess->getUserId();

        try {
            $folder = $facade->addFolderToPathForUserId(
                $name, $pathParts, $userId
            );

            if ($folder === false) {
                /**
                 * @see Conjoon_Error_Factory
                 */
                require_once 'Conjoon/Error/Factory.php';

                $error = Conjoon_Error_Factory::createError(
                    "Could not add the folder.",
                    Conjoon_Error::LEVEL_WARNING
                )->getDto();

                $this->view->success = false;
                $this->view->error   = $error;
                return;
            }

        } catch (Exception $e) {
            /**
             * @see Conjoon_Error
             */
            require_once 'Conjoon/Error.php';

            $this->view->success = true;
            $this->view->error   = Conjoon_Error::fromException($e)->getDto();

            return;
        }

        $this->view->success = true;
        $this->view->error   = null;
        $this->view->folder  = $folder;
    }

    /**
     * Renames the folder with the specified it to the specified value.
     * Post vars:
     * id       : the id of the folder that gets renamed
     * name     : the new name of the node
     * parentId : the id of the current parent folder
     * path     : the path of this node in the tree. This is relevant for
     * IMAP mailboxes which get renamed
     */
    public function renameFolderAction()
    {
        /**
         * @see Conjoon_Modules_Groupware_Email_Folder_Facade
         */
        require_once 'Conjoon/Modules/Groupware/Email/Folder/Facade.php';

        $facade = Conjoon_Modules_Groupware_Email_Folder_Facade::getInstance();

        /**
         * @see Conjoon_Text_Parser_Mail_MailboxFolderPathJsonParser
         */
        require_once 'Conjoon/Text/Parser/Mail/MailboxFolderPathJsonParser.php';

        $parser = new Conjoon_Text_Parser_Mail_MailboxFolderPathJsonParser();

        try {
            $pathParts = $parser->parse($this->_request->getParam('path'));
        } catch (Conjoon_Text_Parser_Exception $e) {
            /**
             * @see Conjoon_Error
             */
            require_once 'Conjoon/Error.php';

            $error = Conjoon_Error::fromException($e);
            $this->view->success = false;
            $this->view->error   = $error->getDto();
            return;
        }

        $name   = $this->_request->getParam('name');
        $userId = $this->_helper->registryAccess->getUserId();

        try {
            $folder = $facade->renameFolderForPathAndUserId(
                $name, $pathParts, $userId
            );

            if ($folder === false) {
                /**
                 * @see Conjoon_Error_Factory
                 */
                require_once 'Conjoon/Error/Factory.php';

                $error = Conjoon_Error_Factory::createError(
                    "Could not rename the specified folder.",
                    Conjoon_Error::LEVEL_WARNING
                )->getDto();

                $this->view->success = false;
                $this->view->error   = $error;
                return;
            }

        } catch (Exception $e) {
            /**
             * @see Conjoon_Error
             */
            require_once 'Conjoon/Error.php';

            $this->view->success = true;
            $this->view->error   = Conjoon_Error::fromException($e)->getDto();

            return;
        }


        $this->view->success = true;
        $this->view->error   = null;
        $this->view->folder  = $folder;
    }

}