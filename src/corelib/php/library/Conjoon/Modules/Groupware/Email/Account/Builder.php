<?php
/**
 * conjoon
 * (c) 2007-2014 conjoon.org
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
 * @see Zend_Cache
 */
require_once 'Zend/Cache.php';

/**
 * @see Conjoon_Builder
 */
require_once 'Conjoon/Builder.php';


class Conjoon_Modules_Groupware_Email_Account_Builder extends Conjoon_Builder {

    protected $_validGetOptions = array('userId');

    protected $_buildClass = 'Conjoon_Modules_Groupware_Email_Account_Dto';

    /**
     *
     * @param array $options An associative array with the following
     * key value/pairs:
     *   - userId: the id of the user to fetch al twitter accounts for
     */
    protected function _buildId(Array $options)
    {
        return (string)$options['userId'];
    }

    /**
     *
     * @param array $options An associative array with the following
     * key value/pairs:
     *   - accountId: The id of the related account this feed was retrieved
     * for
     */
    protected function _getTagList(Array $options)
    {
        return array((string)$options['userId']);
    }

    /**
     * @return Conjoon_Modules_Groupware_Email_Account_Model_Account
     */
    protected function _getModel()
    {
        /**
         * @see Conjoon_Modules_Groupware_Email_Account_Model_Account
         */
        require_once 'Conjoon/Modules/Groupware/Email/Account/Model/Account.php';

        return new Conjoon_Modules_Groupware_Email_Account_Model_Account();
    }


    /**
     * Returns either a cached list of email accounts for a user, or
     * the accounts out of the database.
     *
     * @param array $options An associative array with the following
     * key value/pairs:
     *   - userId: the id of the user to fetch all email accounts for
     * @param Conjoon_BeanContext_Decoratable $model
     *
     * @return Array an array with instances of
     * Conjoon_Modules_Groupware_Feeds_Account_Model_Account
     */
    protected function _build(Array $options, Conjoon_BeanContext_Decoratable $model)
    {
        $userId = $options['userId'];

        /**
         * @see Conjoon_BeanContext_Decorator
         */
        require_once 'Conjoon/BeanContext/Decorator.php';

        $decoratedModel = new Conjoon_BeanContext_Decorator($model);

        $accounts = $decoratedModel->getAccountsForUserAsDto($userId);

        /**
         * @see Conjoon_Modules_Groupware_Email_ImapHelper
         */
        require_once 'Conjoon/Modules/Groupware/Email/ImapHelper.php';

        /**
         * @see Conjoon_Modules_Groupware_Email_Folder_Facade
         */
        require_once 'Conjoon/Modules/Groupware/Email/Folder/Facade.php';


        $facade = Conjoon_Modules_Groupware_Email_Folder_Facade::getInstance();

        for ($i = 0, $len = count($accounts); $i < $len; $i++) {
            $dto =& $accounts[$i];

            if ($dto->protocol == 'IMAP') {

                $folderMappings =& $dto->folderMappings;

                $folder = null;

                try {
                    $folder = $facade->getRootFolderForAccountId(
                        $dto, $userId
                    );
                    $folder = $folder[0];

                } catch (Exception $e) {
                    // connection exception ignore
                }

                for ($a = 0, $lena = count($folderMappings); $a < $lena; $a++) {

                    if (!$folder) {
                        // connection exception, ignore
                        $folderMappings[$a]['globalName'] = "";
                        $folderMappings[$a]['delimiter']  = "";
                        $folderMappings[$a]['path']       = array();
                        continue;
                    }

                    try {
                        $folderMappings[$a]['delimiter'] =
                            Conjoon_Modules_Groupware_Email_ImapHelper
                            ::getFolderDelimiterForImapAccount($dto);

                        $folderMappings[$a]['path'] = array_merge(
                            array('root', $folder['id']),
                            Conjoon_Modules_Groupware_Email_ImapHelper
                            ::splitFolderForImapAccount(
                                $folderMappings[$a]['globalName'], $dto
                            )
                        );

                    } catch (Exception $e) {
                        // connection exception, ignore
                        $folderMappings[$a]['globalName'] = "";
                        $folderMappings[$a]['delimiter']  = "";
                        $folderMappings[$a]['path']       = array();
                    }


                }
            }

            if (!$dto->isOutboxAuth) {
                $dto->usernameOutbox = "";
                $dto->passwordOutbox = "";
            }
            $dto->passwordOutbox = str_pad("", strlen($dto->passwordOutbox), '*');
            $dto->passwordInbox  = str_pad("", strlen($dto->passwordInbox), '*');
        }

        return $accounts;
    }

}