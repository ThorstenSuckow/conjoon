<?php
/**
 * conjoon
 * (c) 2007-2015 conjoon.org
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

namespace Conjoon\Vendor\Zend\Controller\Action\MailModule;

/**
 * @see \Conjoon\Vendor\Zend\Controller\Action\BaseController
 */
require_once 'Conjoon/Vendor/Zend/Controller/Action/BaseController.php';


/**
 * Abstract base class for controllers based on Zend Framework 1 for the
 * MailModule
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
abstract class BaseController extends \Conjoon\Vendor\Zend\Controller\Action\BaseController {

    /**
     * Returns the transport with the server/auth information from the specified
     * account, along with the configuration's smtp client name for using when
     * helo'ing the mail server.
     *
     * @param Conjoon_Modules_Groupware_Email_Account $account The account for
     *        which the transport should be returned
     *
     * @return Conjoon\Mail\Transport\Smtp
     */
    protected function getTransportForAccount(
        \Conjoon_Modules_Groupware_Email_Account $account) {

        $regCon = $this->getApplicationConfiguration();

        $clientName = $regCon->application->mail->smtp->client_name;

        $config = array();
        if ($account->isOutboxAuth()) {
            $config = array(
                'name' => $clientName,
                /**
                 * @todo allow for other auth methods as provided by ZF
                 */
                'auth'     => 'login',
                'username' => $account->getUsernameOutbox(),
                'password' => $account->getPasswordOutbox(),
                'port'     => $account->getPortOutbox()
            );

            $ssl = $account->getOutboxConnectionType();

            if ($ssl == 'SSL' || $ssl == 'TLS') {
                $config['ssl'] = $ssl;
            }
        }

        /**
         * @see \Conjoon\Mail\Transport\Smtp
         */
        require_once 'Conjoon/Mail/Transport/Smtp.php';

        return new \Conjoon\Mail\Transport\Smtp(
            $account->getServerOutbox(), $config
        );
    }

}
