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
 * @see Conjoon_Log
 */
require_once 'Conjoon/Log.php';

/**
 *
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Conjoon_Modules_Groupware_Email_Message_Facade {

    /**
     * @var Conjoon_Modules_Groupware_Email_Message_Facade
     */
    private static $_instance = null;

    /**
     * @var Conjoon_Modules_Groupware_Email_Message_Builder
     */
    private $_builder = null;

    /**
     * @var Conjoon_Modules_Groupware_Email_Message_Model_Message
     */
    private $_messageModel = null;

    /**
     * @var Conjoon_BeanContext_Decorator $_messageDecorator
     */
    private $_messageDecorator = null;

    private function __construct()
    {

    }

    private function __clone()
    {

    }

    public static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }


// -------- public api

    /**
     * Returns the email message for the specified id and refreshes the cache
     * if $refreshCache was set to true.
     *
     * @param integer $groupwareEmailItemsId
     * @param integer $userId
     * @param boolean $refreshCache Whether to clean the cache before the message
     * gets built using the builder.
     *
     * @return Conjoon_Modules_Groupware_Email_Message_Dto
     */
    public function getMessage($groupwareEmailItemsId, $userId, $refreshCache = false)
    {
        $builder = $this->_getBuilder();

        if ($refreshCache === true) {
            $this->removeMessageFromCache($groupwareEmailItemsId, $userId);
        }

        return $builder->get(array(
            'groupwareEmailItemsId' => $groupwareEmailItemsId,
            'userId'                => $userId
        ));

    }

    /**
     * Removes the message from the cache.
     *
     * @param integer $groupwareEmailItemsId
     * @param integer $userId
     *
     */
    public function removeMessageFromCache($groupwareEmailItemsId, $userId)
    {
        $builder = $this->_getBuilder();

        $builder->remove(array(
            'groupwareEmailItemsId' => $groupwareEmailItemsId,
            'userId'                => $userId
        ));
    }

// -------- api

    /**
     *
     * @return Conjoon_BeanContext_Decorator
     */
    private function _getMessageDecorator()
    {
        if (!$this->_messageDecorator) {

            /**
             * @see Conjoon_BeanContext_Decorator
             */
            require_once 'Conjoon/BeanContext/Decorator.php';

            $this->_messageDecorator = new Conjoon_BeanContext_Decorator(
                $this->_getMessageModel()
            );

        }

        return $this->_messageDecorator;

    }

    /**
     * @return Conjoon_Modules_Groupware_Email_Message_Builder
     */
    private function _getBuilder()
    {
        if (!$this->_builder) {
            /**
             * @see Conjoon_Builder_Factory
             */
            require_once 'Conjoon/Builder/Factory.php';

            /**
             * @see Conjoon_Keys
             */
            require_once 'Conjoon/Keys.php';

            $this->_builder = Conjoon_Builder_Factory::getBuilder(
                Conjoon_Keys::CACHE_EMAIL_MESSAGE,
                Zend_Registry::get(Conjoon_Keys::REGISTRY_CONFIG_OBJECT)->toArray(),
                $this->_getMessageModel()
            );
        }

        return $this->_builder;
    }

    /**
     *
     * @return Conjoon_Modules_Groupware_Email_Message_Model_Message
     */
    private function _getMessageModel()
    {
        if (!$this->_messageModel) {
             /**
             * @see Conjoon_Modules_Groupware_Email_Message_Model_Message
             */
            require_once 'Conjoon/Modules/Groupware/Email/Message/Model/Message.php';

            $this->_messageModel = new Conjoon_Modules_Groupware_Email_Message_Model_Message();
        }

        return $this->_messageModel;
    }

}