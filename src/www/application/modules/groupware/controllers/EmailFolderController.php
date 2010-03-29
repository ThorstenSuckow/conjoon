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
     * Folder names' will be delivered unescaped to the client, so the client has to take care
     * of appropriate html-encoding the given names.
     *
     */
    public function getFolderAction()
    {
        $parentId = trim(strtolower($_POST['node']));

        if ($parentId !== 'root') {
            $parentId = (int)$parentId;
        } else {
            $parentId = 0;
        }

        require_once 'Conjoon/BeanContext/Decorator.php';
        $decoratedFolderModel = new Conjoon_BeanContext_Decorator(
            'Conjoon_Modules_Groupware_Email_Folder_Model_Folder'
        );

        require_once 'Conjoon/Keys.php';
        $user   = Zend_Registry::get(Conjoon_Keys::REGISTRY_AUTH_OBJECT)->getIdentity();
        $userId = $user->getId();

        $rows = $decoratedFolderModel->getFoldersAsDto($parentId, $userId);

        $this->view->success = true;
        $this->view->error   = null;
        $this->view->items   = $rows;
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

        require_once 'Conjoon/Modules/Groupware/Email/Folder/Model/Folder.php';
        $folderModel = new Conjoon_Modules_Groupware_Email_Folder_Model_Folder();

        require_once 'Conjoon/Keys.php';
        $user   = Zend_Registry::get(Conjoon_Keys::REGISTRY_AUTH_OBJECT)->getIdentity();
        $userId = $user->getId();

        $ret = $folderModel->deleteFolder($filteredData['id'], $userId);

        if ($ret === 0) {
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

        $this->view->success = true;
        $this->view->error   = null;
    }

    /**
     * Moves a folder into a new folder.
     * POST:
     *  parentId : the id of the new folder this folder gets moved into
     *  id : the id of this folder thats about being moved
     *
     */
    public function moveFolderAction()
    {
        require_once 'Conjoon/Modules/Groupware/Email/Folder/Filter/Folder.php';
        $filter = new Conjoon_Modules_Groupware_Email_Folder_Filter_Folder(
            $_POST,
            Conjoon_Modules_Groupware_Email_Folder_Filter_Folder::CONTEXT_MOVE
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

        require_once 'Conjoon/Modules/Groupware/Email/Folder/Model/Folder.php';
        $folderModel = new Conjoon_Modules_Groupware_Email_Folder_Model_Folder();

        $ret = $folderModel->moveFolder($filteredData['id'], $filteredData['parentId']);

        if ($ret === 0) {
            require_once 'Conjoon/Error.php';
            $error = new Conjoon_Error();
            $error = $error->getDto();
            $error->title   = 'Error';
            $error->level   = Conjoon_Error::LEVEL_WARNING;
            $error->message = 'Could not move the specified folder into the new folder.';
            $this->view->success = false;
            $this->view->error   = $error;
            return;
        }

        $this->view->success = true;
        $this->view->error   = null;
    }

    /**
     * Adds a new folder to the folder with the specified id.
     * POST:
     * parentId : the id of the parent folder to which the new folder should get
     * appended
     * name : the name of the folder
     *
     * The method will assign a view-id property called "id", which holds the
     * id of the newly added folder.
     */
    public function addFolderAction()
    {
        require_once 'Conjoon/Modules/Groupware/Email/Folder/Filter/Folder.php';
        $filter = new Conjoon_Modules_Groupware_Email_Folder_Filter_Folder(
            $_POST,
            Conjoon_Modules_Groupware_Email_Folder_Filter_Folder::CONTEXT_CREATE
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

        require_once 'Conjoon/Modules/Groupware/Email/Folder/Model/Folder.php';
        $folderModel = new Conjoon_Modules_Groupware_Email_Folder_Model_Folder();

        require_once 'Conjoon/Keys.php';
        $user   = Zend_Registry::get(Conjoon_Keys::REGISTRY_AUTH_OBJECT)->getIdentity();
        $userId = $user->getId();

        $id = $folderModel->addFolder($filteredData['parentId'], $filteredData['name'], $userId);

        if ((int)$id <= 0) {
            $this->view->success = false;
            require_once 'Conjoon/Error.php';
            $error = new Conjoon_Error();
            $error = $error->getDto();
            $error->file  = __FILE__;
            $error->line  = __LINE__;
            $error->type  = Conjoon_Error::UNKNOWN;
            $error->level = Conjoon_Error::LEVEL_WARNING;
            $error->message = "Could not create folder.";
            $this->view->error = $error;
            return;
        }

        $this->view->success = true;
        $this->view->error   = null;
        $this->view->id      = $id;

    }

    /**
     * Renames the folder with the specified it to the specified value.
     * Post vars:
     * id       : the id of the folder that gets renamed
     * name     : the new name of the node
     */
    public function renameFolderAction()
    {
        require_once 'Conjoon/Modules/Groupware/Email/Folder/Filter/Folder.php';
        $filter = new Conjoon_Modules_Groupware_Email_Folder_Filter_Folder(
            $_POST,
            Conjoon_Modules_Groupware_Email_Folder_Filter_Folder::CONTEXT_RENAME
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

        require_once 'Conjoon/Modules/Groupware/Email/Folder/Model/Folder.php';
        $folderModel = new Conjoon_Modules_Groupware_Email_Folder_Model_Folder();

        $ret = $folderModel->renameFolder($filteredData['id'], $filteredData['name']);

        if ($ret === 0) {
            require_once 'Conjoon/Error.php';
            $error = new Conjoon_Error();
            $error = $error->getDto();
            $error->title   = 'Error';
            $error->level   = Conjoon_Error::LEVEL_WARNING;
            $error->message = 'Could not rename the specified folder.';
            $this->view->success = false;
            $this->view->error   = $error;
            return;
        }

        $this->view->success = true;
        $this->view->error   = null;
    }

}