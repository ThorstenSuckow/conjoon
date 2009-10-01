<?php
/**
 * conjoon
 * (c) 2002-2009 siteartwork.de/conjoon.org
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
     *
     *
     * @return Array an array with instances of
     * Conjoon_Modules_Groupware_Feeds_Account_Model_Account
     */
    protected function _build(Array $options)
    {
        $userId = $options['userId'];

        /**
         * @see Conjoon_BeanContext_Decorator
         */
        require_once 'Conjoon/BeanContext/Decorator.php';

        $decoratedModel = new Conjoon_BeanContext_Decorator(
            $this->getModel()
        );

        $accounts = $decoratedModel->getAccountsForUserAsDto($userId);

        for ($i = 0, $len = count($accounts); $i < $len; $i++) {
            $dto =& $accounts[$i];
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