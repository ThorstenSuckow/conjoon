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
class Service_TwitterAccountController extends Zend_Controller_Action {

    const CONTEXT_JSON = 'json';

    /**
     * Inits this controller and sets the context-switch-directives
     * on the various actions.
     *
     */
    public function init()
    {
        $this->_helper->filterRequestData()
                      ->registerFilter('Service_TwitterAccountController::add.account', true)
                      ->registerFilter('Service_TwitterAccountController::remove.account')
                      ->registerFilter('Service_TwitterAccountController::update.account');

        $conjoonContext = $this->_helper->conjoonContext();

        $conjoonContext->addActionContext('get.accounts',   self::CONTEXT_JSON)
                       ->addActionContext('remove.account', self::CONTEXT_JSON)
                       ->addActionContext('add.account',    self::CONTEXT_JSON)
                       ->addActionContext('update.account', self::CONTEXT_JSON)
                       ->initContext();
    }

    /**
     * Sends account informations to the client.
     * Passwords will be masked. This action will also try to load
     * the user information for each account from the Twitter Service.
     * If this fails, the "twitter*" properties of the data which is to be
     * send to the client will be empty.
     *
     */
    public function getAccountsAction()
    {
        /**
         * @todo refactor when facade gets created, return list from server
         */

        /**
         * @see Conjoon_Keys
         */
        require_once 'Conjoon/Keys.php';

        $user   = Zend_Registry::get(
            Conjoon_Keys::REGISTRY_AUTH_OBJECT
        )->getIdentity();

        $userId = $user->getId();

        /**
         * @see Conjoon_Builder_Factory
         */
        require_once 'Conjoon/Builder/Factory.php';

        $data = Conjoon_Builder_Factory::getBuilder(
            Conjoon_Keys::CACHE_TWITTER_ACCOUNTS,
            Zend_Registry::get(Conjoon_Keys::REGISTRY_CONFIG_OBJECT)->toArray()
        )->get(array('userId' => $this->_helper->registryAccess()->getUserId()));

        $this->view->success  = true;
        $this->view->accounts = $data;
        $this->view->error    = null;
    }

    /**
     * Imports a new Twitter account into conjoon.
     * Needed post parameters are "name", "twitterId", "oauthToken" and
     * "oauthTokenSecret".
     * The method will try to connect to the Twitter service to verify
     * the account credentials.
     * If this fails, the following information will be send to the client:
     *  - connectionFailure = true
     *  - success = false
     * Any other error will be communicated to the client. A successfull
     * stored account will be saved in "account" and returned to the client.
     *
     *
     */
    public function addAccountAction()
    {
        $name             = $this->_request->getParam('name');
        $oauthToken       = $this->_request->getParam('oauthToken');
        $oauthTokenSecret = $this->_request->getParam('oauthTokenSecret');
        $twitterId        = $this->_request->getParam('twitterId');
        $updateInterval   = $this->_request->getParam('updateInterval');

        /**
         * @see Conjoon_Service_Twitter_Proxy
         */
        require_once 'Conjoon/Service/Twitter/Proxy.php';

        $proxy = new Conjoon_Service_Twitter_Proxy(array(
            'oauth_token'        => $oauthToken,
            'oauth_token_secret' => $oauthTokenSecret,
            'user_id'            => $twitterId,
            'screen_name'        => $name
        ));

        $dto = $proxy->accountVerifyCredentials();

        /**
         * @see Conjoon_Error
         */
        require_once 'Conjoon/Error.php';

        if ($dto instanceof Conjoon_Error) {
            $this->view->success           = false;
            $this->view->error             = $dto->getDto();
            $this->view->connectionFailure = true;
            return;
        }

        /**
         * @see Conjoon_Modules_Service_Twitter_Account_Model_Account
         */
        require_once 'Conjoon/Modules/Service/Twitter/Account/Model/Account.php';

        $model = new Conjoon_Modules_Service_Twitter_Account_Model_Account();

        $id = $model->addAccountForUserId(array(
            'name'               => $name,
            'update_interval'    => $updateInterval,
            'oauth_token'        => $oauthToken,
            'oauth_token_secret' => $oauthTokenSecret,
            'twitter_id'         => $twitterId
        ), $this->_helper->registryAccess()->getUserId());

        if ($id == 0) {
            /**
             * @see Conjoon_Error_Factory
             */
            require_once 'Conjoon/Error/Factory.php';

            $this->view->error = Conjoon_Error_Factory::createError(
                "Could not write the data for the Twitter account into the datastorage.",
                Conjoon_Error::LEVEL_ERROR
            );

            $this->view->success = false;
            return;
        }

        /**
         * @see Conjoon_Builder_Factory
         */
        require_once 'Conjoon/Builder/Factory.php';

        Conjoon_Builder_Factory::getBuilder(
            Conjoon_Keys::CACHE_TWITTER_ACCOUNTS,
            Zend_Registry::get(Conjoon_Keys::REGISTRY_CONFIG_OBJECT)->toArray()
        )->remove(array('userId' => $this->_helper->registryAccess()->getUserId()));

        $dto->updateInterval = $updateInterval;
        $dto->password       = "******";
        $dto->name           = $name;
        $dto->id             = $id;

        $this->view->success = true;
        $this->view->account = $dto;
    }

    /**
     * Removes accounts based on the ids passed to this method.
     * The ids will be available in the POST-Param "data", whereas each index
     * holds the id of the account to remove.
     *
     * Removed accounts will be send back as their ids in the property "removed"
     * (numeric array), accounts, which could not be removed, will be stored in
     * the numeric array "failed". As soon, as there is one entry in this array,
     * the response property "success" has to be set to "false".
     *
     *
     */
    public function removeAccountAction()
    {
        $data = $this->_request->getParam('data');

        /**
         * @see Conjoon_Modules_Service_Twitter_Account_Model_Account
         */
        require_once 'Conjoon/Modules/Service/Twitter/Account/Model/Account.php';

        $model = new Conjoon_Modules_Service_Twitter_Account_Model_Account();

        $removed = array();
        $failed  = array();

        for ($i = 0, $len = count($data); $i < $len; $i++) {
            $rem = $model->deleteAccountForId($data[$i]);
            if ($rem) {
                $removed[] = $data[$i];
            } else {
                $failed[] = $data[$i];
            }
        }

        if (count($removed)) {
            /**
             * @see Conjoon_Builder_Factory
             */
            require_once 'Conjoon/Builder/Factory.php';

            Conjoon_Builder_Factory::getBuilder(
                Conjoon_Keys::CACHE_TWITTER_ACCOUNTS,
                Zend_Registry::get(Conjoon_Keys::REGISTRY_CONFIG_OBJECT)->toArray()
            )->remove(array('userId' => $this->_helper->registryAccess()->getUserId()));
        }

        $this->view->success = (count($failed) == 0);
        $this->view->removed = $removed;
        $this->view->failed  = $failed;
    }

    /**
     * Updates an account based on the given data.
     * Data to be updated will be available in a numeric indexed array "data",
     * whereas each value is an assoc array with the following fields:
     *  - id
     *  - name
     *  - password
     *  - updateInterval
     *
     * The field "id" is mandatory. Successfully updated indexes of accounts must
     * be send back in a property "updated", whereas data which cannot be updated must
     * be send back in a property called "failed" (each value in this array is the id
     * of the account which could not be updated).
     * The "success" property has to be set to "false" as soon as one account could
     * not be updated.
     *
     */
    public function updateAccountAction()
    {
        $data = $this->_request->getParam('data');

        /**
         * @see Conjoon_Modules_Service_Twitter_Account_Filter_Account
         */
        require_once 'Conjoon/Modules/Service/Twitter/Account/Filter/Account.php';

        /**
         * @see Conjoon_Modules_Service_Twitter_Account_Model_Account
         */
        require_once 'Conjoon/Modules/Service/Twitter/Account/Model/Account.php';

        $model = new Conjoon_Modules_Service_Twitter_Account_Model_Account();

        $filter = new Conjoon_Modules_Service_Twitter_Account_Filter_Account(
            array(),
            Conjoon_Modules_Service_Twitter_Account_Filter_Account::CONTEXT_UPDATE
        );

        $updated = array();
        $failed  = array();

        for ($i = 0, $len = count($data); $i < $len; $i++) {
            $filter->setData($data[$i]);
            $upData = $filter->getProcessedData();

            $id = $upData['id'];
            unset($upData['id']);

            // no data left? skip, mark account as succesfully updated
            if (empty($upData)) {
                $updated[] = $id;
                continue;
            }

            $res = $model->updateAccountForId($upData, $id);

            if (!$res) {
                $failed[] = $id;
            } else {
                $updated[] = $id;
            }
        }

        if (count($updated)) {
            /**
             * @see Conjoon_Builder_Factory
             */
            require_once 'Conjoon/Builder/Factory.php';

            Conjoon_Builder_Factory::getBuilder(
                Conjoon_Keys::CACHE_TWITTER_ACCOUNTS,
                Zend_Registry::get(Conjoon_Keys::REGISTRY_CONFIG_OBJECT)->toArray()
            )->remove(array('userId' => $this->_helper->registryAccess()->getUserId()));
        }

        $this->view->success = (count($failed) == 0);
        $this->view->failed  = $failed;
        $this->view->updated = $updated;
    }

    /**
     * Action redirects to Twitter for letting a user decide whether
     * he wants conjoon give access to his Twitter Account via oauth.
     * Note: When this action is called, conjoon is unaware of the fact
     * whether the account the user is currently logged in with at Twitter
     * is already registered at the datastore.
     * This action will redirect to the oauth site url of Twitter and
     * stop all script execution after this.
     */
    public function authorizeAccountAction()
    {
        /**
         * @see Conjoon_Modules_Default_Registry_Facade
         */
        require_once 'Conjoon/Modules/Default/Registry/Facade.php';

        $registry = Conjoon_Modules_Default_Registry_Facade::getInstance();

        $userId = $this->_helper->registryAccess()->getUserId();

        $port     = $registry->getValueForKeyAndUserId('/server/environment/port', $userId);
        $protocol = $registry->getValueForKeyAndUserId('/server/environment/protocol', $userId);
        $host     = $registry->getValueForKeyAndUserId('/server/environment/host', $userId);

        /**
         * @see Zend_Session_Namespace
         */
        require_once 'Zend/Session/Namespace.php';

        /**
         * @see Conjoon_Keys
         */
        require_once 'Conjoon/Keys.php';

        $sessionOauth = new Zend_Session_Namespace(
            Conjoon_Keys::SESSION_SERVICE_TWITTER_OAUTH
        );

        /**
         * @see Zend_Registry
         */
        require_once 'Zend/Registry.php';

        $config = Zend_Registry::get(Conjoon_Keys::REGISTRY_CONFIG_OBJECT);

        $callbackUrl = $protocol . '://' . $host . ':' . $port . '/'
                       . $config->environment->base_url . '/'
                       . $config->application->twitter->oauth->callbackUrl;

        $siteUrl        = $config->application->twitter->oauth->siteUrl;
        $consumerKey    = $config->application->twitter->oauth->consumerKey;
        $consumerSecret = $config->application->twitter->oauth->consumerSecret;

        $options = array(
            'callbackUrl'    => $callbackUrl,
            'siteUrl'        => $siteUrl,
            'consumerKey'    => $consumerKey,
            'consumerSecret' => $consumerSecret
        );

        /**
         * @see Zend_Oauth_Consumer
         */
        require_once 'Zend/Oauth/Consumer.php';
        $consumer = new Zend_Oauth_Consumer($options);

        $token = $consumer->getRequestToken();

        $sessionOauth->oauthToken       = $token->getParam('oauth_token');
        $sessionOauth->oauthTokenSecret = $token->getParam('oauth_token_secret');
        $consumer->redirect();
        die();
    }

    /**
     * This is the action to which Twitter redirects once the user has authorized
     * conjoon to use a specific Twitter account.
     * Necessary configuration will be stored in the session. The Session parameters
     * oauthToken and oauthTokenSecret must be available.
     */
    public function authorizeOkayAction()
    {
        $this->view->title = "conjoon - Twitter Account Authorization";

        /**
         * @see Zend_Session_Namespace
         */
        require_once 'Zend/Session/Namespace.php';

        /**
         * @see Conjoon_Keys
         */
        require_once 'Conjoon/Keys.php';

        $sessionOauth = new Zend_Session_Namespace(
            Conjoon_Keys::SESSION_SERVICE_TWITTER_OAUTH
        );

        if (!isset($sessionOauth->oauthToken) || !isset($sessionOauth->oauthTokenSecret)) {
            die("invalid data.");
        }

        /**
         * @see Zend_Oauth_Consumer
         */
        require_once 'Zend/Oauth/Consumer.php';

        $config = Zend_Registry::get(Conjoon_Keys::REGISTRY_CONFIG_OBJECT);

        /**
         * @see Conjoon_Modules_Default_Registry_Facade
         */
        require_once 'Conjoon/Modules/Default/Registry/Facade.php';

        $registry = Conjoon_Modules_Default_Registry_Facade::getInstance();

        $userId = $this->_helper->registryAccess()->getUserId();

        $port     = $registry->getValueForKeyAndUserId('/server/environment/port', $userId);
        $protocol = $registry->getValueForKeyAndUserId('/server/environment/protocol', $userId);
        $host     = $registry->getValueForKeyAndUserId('/server/environment/host', $userId);

        $callbackUrl = $protocol . '://' . $host . ':' . $port . '/'
                       . $config->environment->base_url . '/'
                       . $config->application->twitter->oauth->callbackUrl;

        $siteUrl        = $config->application->twitter->oauth->siteUrl;
        $consumerKey    = $config->application->twitter->oauth->consumerKey;
        $consumerSecret = $config->application->twitter->oauth->consumerSecret;


        $options = array(
            'callbackUrl'    => $callbackUrl,
            'siteUrl'        => $siteUrl,
            'consumerKey'    => $consumerKey,
            'consumerSecret' => $consumerSecret
        );

        $consumer = new Zend_Oauth_Consumer($options);

        require_once 'Zend/Oauth/Token/Request.php';

        $requestToken = new Zend_Oauth_Token_Request();

        $requestToken->setParams(array(
            'oauth_token'        => $sessionOauth->oauthToken,
            'oauth_token_secret' => $sessionOauth->oauthTokenSecret
        ));


        $accessToken = $consumer->getAccessToken(
            $_GET, $requestToken
        );

        require_once 'Zend/Oauth/Token/Access.php';

        $screenName = $accessToken->getParam('screen_name');
        $userId     = $accessToken->getParam('user_id');

        $oauthToken       = $accessToken->getParam('oauth_token');
        $oauthTokenSecret = $accessToken->getParam('oauth_token_secret');

        require_once 'Conjoon/Service/Twitter/Proxy.php';


        $twitter = new Conjoon_Service_Twitter_Proxy(array(
            'screen_name'        => $screenName,
            'user_id'            => $userId,
            'oauth_token'        => $oauthToken,
            'oauth_token_secret' => $oauthTokenSecret
        ));
        $dto = $twitter->accountVerifyCredentials();

        if ($dto instanceof Conjoon_Error) {
            $this->view->success           = false;
            $this->view->error             = $dto->getDto();
            $this->view->connectionFailure = true;
            return;
        }

        unset($sessionOauth->oauthToken);
        unset($sessionOauth->oauthTokenSecret);

        $dto->oauthToken       = $oauthToken;
        $dto->oauthTokenSecret = $oauthTokenSecret;

        $this->view->success = true;
        $this->view->account = $dto;
    }
}