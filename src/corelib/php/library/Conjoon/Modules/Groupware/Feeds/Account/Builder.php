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
 * @see Zend_Cache
 */
require_once 'Zend/Cache.php';

/**
 * @see Conjoon_Builder
 */
require_once 'Conjoon/Builder.php';


class Conjoon_Modules_Groupware_Feeds_Account_Builder extends Conjoon_Builder {

    protected $_validGetOptions = array('accountId');

    protected $_buildClass = 'Conjoon_Modules_Groupware_Feeds_Account_Dto';

    protected $_validTagOptions = array('userId');

    /**
     *
     * @param array $options An associative array with the following
     * key value/pairs:
     *   - userId: the id of the user to fetch al twitter accounts for
     */
    protected function _buildId(Array $options)
    {
        return (string)$options['accountId'];
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
     * @return Conjoon_Modules_Groupware_Feeds_Account_Model_Account
     */
    protected function _getModel()
    {
        /**
         * @see Conjoon_Modules_Groupware_Feeds_Account_Model_Account
         */
        require_once 'Conjoon/Modules/Groupware/Feeds/Account/Model/Account.php';

        return new Conjoon_Modules_Groupware_Feeds_Account_Model_Account();
    }

    /**
     * Returns either a cached list of feed accounts for a user, or
     * the accounts out of the database.
     *
     * @param array $options An associative array with the following
     * key value/pairs:
     *   - userId: the id of the user to fetch all feed accounts for
     * @param Conjoon_BeanContext_Decoratable $model
     *
     * @return Array an array with instances of
     * Conjoon_Modules_Groupware_Feeds_Account_Model_Account_Dto
     */
    protected function _build(Array $options, Conjoon_BeanContext_Decoratable $model)
    {
        $accountId = $options['accountId'];

        /**
         * @see Conjoon_BeanContext_Decorator
         */
        require_once 'Conjoon/BeanContext/Decorator.php';

        $decoratedModel = new Conjoon_BeanContext_Decorator($model);

        $accounts = $decoratedModel->getAccountAsDto($accountId);

        return $accounts;
    }

}