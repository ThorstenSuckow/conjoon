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
 * This facade eases the access for operations with IMAP folder/mailbox actions.
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Conjoon_Modules_Groupware_Email_FolderMapping_Facade {

    /**
     * @var Conjoon_Modules_Groupware_Email_FolderMapping_Facade
     */
    private static $_instance = null;

    /**
     * @var Conjoon_Modules_Groupware_Email_FolderMapping_Model_ImapMapping
     */
    private $_imapMappingModel = null;

    /**
     * @var Conjoon_BeanContext_Decorator
     */
    private $_folderMappingDecorator = null;

    /**
     * Enforce singleton.
     *
     */
    private function __construct()
    {
    }

    /**
     * Enforce singleton.
     *
     */
    private function __clone()
    {
    }

    /**
     *
     * @return Conjoon_Modules_Groupware_Email_Folder_Facade
     */
    public static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }


// -------- public api

    /**
     * Returns a list of Conjoon_Groupware_Email_FolderMapping_Dto's representing
     * all available folder mappings.
     *
     * @return array|Conjoon_Groupware_Email_FolderMapping_Dto
     *
     * @throws InvalidArgumentException
     */
    public function getFolderMappingsForUserId($userId)
    {
        $userId = (int)$userId;

        if ($userId <= 0) {
            throw new InvalidArgumentException(
                "Invalid argument - userId, was \"$userId\""
            );
        }

        $mappings = $this->_getFolderMappingDecorator()->getImapMappingsForUserAsDto($userId);

        return $mappings;
    }

// -------- api

    /**
     *
     * @return Conjoon_BeanContext_Decorator
     */
    private function _getFolderMappingDecorator()
    {
        if (!$this->_folderMappingDecorator) {

            /**
             * @see Conjoon_BeanContext_Decorator
             */
            require_once 'Conjoon/BeanContext/Decorator.php';

            $this->_folderMappingDecorator = new Conjoon_BeanContext_Decorator(
                $this->_getImapMappingModel()
            );
        }

        return $this->_folderMappingDecorator;
    }

    /**
     *
     * @return Conjoon_Modules_Groupware_Email_ImapMapping_Model_ImapMapping
     */
    private function _getImapMappingModel()
    {
        if (!$this->_imapMappingModel) {
             /**
             * @see Conjoon_Modules_Groupware_Email_FolderMapping_Model_ImapMapping
             */
            require_once 'Conjoon/Modules/Groupware/Email/FolderMapping/Model/ImapMapping.php';

            $this->_imapMappingModel = new Conjoon_Modules_Groupware_Email_FolderMapping_Model_ImapMapping();
        }

        return $this->_imapMappingModel;
    }

}