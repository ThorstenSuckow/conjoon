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
 * This facade eases the access for operations with IMAP folder/mailbox actions.
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
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
     * @var Conjoon_Modules_Groupware_Email_Folder_Model_Folder
     */
    private $_folderModel = null;

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

        $imapMappings = $this->_getFolderMappingDecorator()
                        ->getImapMappingsForUserAsDto($userId);

        $localMappings = $this->_getFolderModel()->getLocalMappingsForUser(
            $userId
        );

        $localMappings = $this->_convertLocalMappingsToFolderMappingDtos(
            $localMappings
        );

        $mappings = array_merge($imapMappings, $localMappings);

        return $mappings;
    }

// -------- api

    /**
     * Converts local mappings as retrieved from
     * Conjoon_Modules_Groupware_Email_Folder_Model_Folder::getLocalMappingsForUser()
     * to an array with Conjoon_Modules_Groupware_Email_FolderMapping_Dto.
     *
     * @param array $localmappings
     *
     * @return array|Conjoon_Modules_Groupware_Email_FolderMapping_Dto
     */
    private function _convertLocalMappingsToFolderMappingDtos(Array $localMappings)
    {
        /**
         * @see Conjoon_Modules_Groupware_Email_FolderMapping_Dto
         */
        require_once 'Conjoon/Modules/Groupware/Email/FolderMapping/Dto.php';

        $dtos = array();

        for ($i = 0, $len = count($localMappings); $i < $len; $i++) {
            $lm =& $localMappings[$i];
            $dto = new Conjoon_Modules_Groupware_Email_FolderMapping_Dto();
            $dto->id                       = time()+$i;
            $dto->rootFolderId             = $lm['parent_id'];
            $dto->type                     = strtoupper($lm['type']);
            $dto->globalName               = $lm['id'];
            $dto->groupwareEmailAccountsId = $lm['groupware_email_accounts_id'];

            $dtos[] = $dto;
        }

        return $dtos;
    }

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

    /**
     *
     * @return Conjoon_Modules_Groupware_Email_Folder_Model_Folder
     */
    private function _getFolderModel()
    {
        if (!$this->_folderModel) {
             /**
             * @see Conjoon_Modules_Groupware_Email_Folder_Model_Folder
             */
            require_once 'Conjoon/Modules/Groupware/Email/Folder/Model/Folder.php';

            $this->_folderModel = new Conjoon_Modules_Groupware_Email_Folder_Model_Folder();
        }

        return $this->_folderModel;
    }

}