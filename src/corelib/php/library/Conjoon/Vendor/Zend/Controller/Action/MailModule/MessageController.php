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

namespace Conjoon\Vendor\Zend\Controller\Action\MailModule;

/**
 * @see \Conjoon\Vendor\Zend\Controller\Action\BaseController
 */
require_once 'Conjoon/Vendor/Zend/Controller/Action/BaseController.php';

/**
 * @see \Conjoon\Argument\ArgumentCheck
 */
require_once 'Conjoon/Argument/ArgumentCheck.php';

/**
 * @see Zend_Registry
 */
require_once 'Zend/Registry.php';

/**
 * @see Conjoon_Keys
 */
require_once 'Conjoon/Keys.php';

use \Conjoon\Vendor\Zend\Controller\Action\BaseController,
    \Conjoon\Argument\ArgumentCheck;

/**
 * Abstract base class for controllers dealing with retrieving mail messages
 * from the conjoon backend.
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
abstract class MessageController extends BaseController {

    /**
     * Helper function for fetching a single email message from a remote
     * server.
     *
     * @param string $id The id of the message
     * @param string $path The json encoded path where the message can be found
     * @param mixed $allowExternals whether external resources are allowed. If this is
     * set to NULL, the registry settings of the current user will be given precedence
     *
     * @throws \Conjoon\Argument\InvalidArgumentException
     */
    protected function getMessageHelper($id, $path, $allowExternals = null)
    {
        $data = array('path' => $path);

        ArgumentCheck::check(array(
            'path' => array(
                'type'       => 'string',
                'allowEmpty' => false
            )
        ), $data);

        $path = $data['path'];

        /**
         * @see Conjoon_Modules_Default_Registry_Facade
         */
        require_once 'Conjoon/Modules/Default/Registry/Facade.php';

        $registry = \Conjoon_Modules_Default_Registry_Facade::getInstance();

        $readingKey = '/client/conjoon/modules/mail/options/reading/';

        $preferredFormat = $registry->getValueForKeyAndUserId(
            $readingKey . 'preferred_format',
            $this->getCurrentAppUser()->getId()
        );
        $allowExternals = $allowExternals === null
            ? $registry->getValueForKeyAndUserId(
                  $readingKey . 'allow_externals',
                  $this->getCurrentAppUser()->getId()
              )
            : $allowExternals;

        /**
         * @see \Conjoon\Mail\Client\Message\Strategy\DefaultPlainReadableStrategy
         */
        require_once 'Conjoon/Mail/Client/Message/Strategy/DefaultPlainReadableStrategy.php';

        $plainReadableStrategy = new \Conjoon\Mail\Client\Message\Strategy\DefaultPlainReadableStrategy();

        $readableStrategy = null;

        $htmlPurifier = $this->getHtmlPurifierHelper($allowExternals);

        if ($preferredFormat == 'html') {
            /**
             * @see \Conjoon\Mail\Client\Message\Strategy\DefaultHtmlReadableStrategy
             */
            require_once 'Conjoon/Mail/Client/Message/Strategy/DefaultHtmlReadableStrategy.php';

            /**
             * @see \Conjoon\Text\Parser\Html\ExternalResourcesParser
             */
            require_once 'Conjoon/Text/Parser/Html/ExternalResourcesParser.php';

            $readableStrategy = new \Conjoon\Mail\Client\Message\Strategy\DefaultHtmlReadableStrategy(
                $htmlPurifier,
                $plainReadableStrategy,
                new \Conjoon\Text\Parser\Html\ExternalResourcesParser()
            );

        } else {
            $readableStrategy = $plainReadableStrategy;
        }

        $serviceFacade = $this->getMessageServiceFacadeHelper();

        return $serviceFacade->getMessage(
            $id,
            $path,
            $this->getCurrentAppUser(),
            $readableStrategy
        );

    }


    /**
     * Returns the MessageServiceFacade used with this controller.
     *
     *
     * @return \Conjoon\Mail\Client\Service\MessageServiceFacade
     */
    protected function getMessageServiceFacadeHelper() {

        $entityManager = \Zend_Registry::get(\Conjoon_Keys::DOCTRINE_ENTITY_MANAGER);

        $mailAccountRepository =
            $entityManager->getRepository('\Conjoon\Data\Entity\Mail\DefaultMailAccountEntity');
        $mailFolderRepository =
            $entityManager->getRepository('\Conjoon\Data\Entity\Mail\DefaultMailFolderEntity');
        $messageFlagRepository =
            $entityManager->getRepository('\Conjoon\Data\Entity\Mail\DefaultMessageFlagEntity');
        $localMessageRepository =
            $entityManager->getRepository('\Conjoon\Data\Entity\Mail\DefaultMessageEntity');
        $attachmentRepository =
            $entityManager->getRepository('\Conjoon\Data\Entity\Mail\DefaultAttachmentEntity');

        $protocolAdaptee = new \Conjoon\Mail\Server\Protocol\DefaultProtocolAdaptee(
            $mailFolderRepository, $messageFlagRepository, $mailAccountRepository,
            $localMessageRepository, $attachmentRepository
        );

        /**
         * @see \Conjoon\Mail\Server\Protocol\DefaultProtocol
         */
        $protocol = new \Conjoon\Mail\Server\Protocol\DefaultProtocol($protocolAdaptee);

        /**
         * @see \Conjoon\Mail\Server\DefaultServer
         */
        require_once 'Conjoon/Mail/Server/DefaultServer.php';

        $server = new \Conjoon\Mail\Server\DefaultServer($protocol);

        /**
         * @see \Conjoon\Mail\Client\Service\DefaultMessageServiceFacade
         */
        require_once 'Conjoon/Mail/Client/Service/DefaultMessageServiceFacade.php';

        return new \Conjoon\Mail\Client\Service\DefaultMessageServiceFacade(
            $server, $mailAccountRepository, $mailFolderRepository
        );
    }


    /**
     * Helper function for setting up and returning a HtmlPurifier instance
     * for sanitizing html mail bodies.
     *
     * @param boolean $allowExternals whether external resources should be
     * allowed or not
     *
     * @return \HtmlPurifier
     */
    public function getHtmlPurifierHelper($allowExternals) {

        /**
         * @see \Conjoon\Util\Environment
         */
        require_once 'Conjoon/Net/Environment.php';

        $cnEnvironment = new \Conjoon\Net\Environment();

        $htmlPurifierConfig = $this->getBaseHtmlPurifierConfig();

        $config = $this->getApplicationConfiguration();

        /**
         * @see \Conjoon\Vendor\HtmlPurifier\UriFilter\ResourceNotAvailableUriFilter
         */
        require_once 'Conjoon/Vendor/HtmlPurifier/UriFilter/ResourceNotAvailableUriFilter.php';

        $htmlPurifierConfig->set('HTML.Trusted', false);
        $htmlPurifierConfig->set('CSS.AllowTricky', false);
        $htmlPurifierConfig->set('CSS.AllowImportant', false);
        $htmlPurifierConfig->set('CSS.Trusted', false);
        $htmlPurifierConfig->set('URI.DisableExternalResources', !$allowExternals);

        $uri = $htmlPurifierConfig->getDefinition('URI');

        $cnUri = $cnEnvironment->getCurrentUriBase();
        $cnUri = $cnUri->setPath(
            rtrim($config->environment->base_url, '/') .
                '/' .
                '/default/index/resource.not.available'
        );

        $uri->addFilter(
            new \Conjoon\Vendor\HtmlPurifier\UriFilter\ResourceNotAvailableUriFilter($cnUri),
            $htmlPurifierConfig
        );

        return new \HTMLPurifier($htmlPurifierConfig);

    }

    /**
     * Returns basic htmlpurifier configuration.
     *
     * @return \HTMLPurifier_Config
     */
    protected function getBaseHtmlPurifierConfig() {

        $config = $this->getApplicationConfiguration();

        $htmlPurifierConfig = \HTMLPurifier_Config::createDefault();
        if (!$config->application->htmlpurifier->use_cache ||
            !$config->application->htmlpurifier->cache_dir) {
            $htmlPurifierConfig->set('Cache.DefinitionImpl', null);
        } else {
            $htmlPurifierConfig->set(
                'Cache.SerializerPath',
                $config->application->htmlpurifier->cache_dir
            );
        }

        return $htmlPurifierConfig;
    }

}