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


namespace Conjoon\Mail\Server\Protocol;

/**
 * @see DefaultProtocol
 */
require_once 'Conjoon/Mail/Server/Protocol/DefaultProtocol.php';

require_once dirname(__FILE__) . '/ProtocolTestCase.php';


/**
 * @category   Conjoon
 * @package    Conjoon_Mail
 * @subpackage UnitTests
 * @group      Conjoon_Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultProtocolTest extends ProtocolTestCase {

    protected $protocol;

    protected $failProtocol;

    protected function setUp()
    {
        parent::setUp();

        $this->protocol = new DefaultProtocol($this->protocolAdaptee);

        $this->failProtocol = new DefaultProtocol($this->failProtocolAdaptee);
    }


    /**
     * Ensures everything works as expected
     */
    public function testOk()
    {
        $this->assertTrue(
            $this->protocol->setFlags(array(
                'user'       => $this->user,
                'parameters' => array(
                    'folderFlagCollection' => $this->folderFlagCollection,
                )
            ))
                instanceof
                \Conjoon\Mail\Server\Protocol\DefaultResult\SetFlagsResult
        );

        $this->assertTrue(
            $this->protocol->setFlags(array(
                'user'       => $this->user,
                'parameters' => array(
                    'folderFlagCollection' => array(),
                )
            ))
                instanceof
                \Conjoon\Mail\Server\Protocol\DefaultResult\ErrorResult
        );

        $this->assertTrue(
            $this->protocol->getMessage(array(
                'user'       => $this->user,
                'parameters' => array(
                    'messageLocation' => new \Conjoon\Mail\Client\Message\DefaultMessageLocation(
                        $this->folderFlagCollection->getFolder(), 1
                    ),
                )
            ))
                instanceof
                \Conjoon\Mail\Server\Protocol\DefaultResult\GetMessageResult
        );


        $this->assertTrue(
            $this->protocol->getAttachment(array(
                'user'       => $this->user,
                'parameters' => array(
                    'attachmentLocation' =>
                    new \Conjoon\Mail\Client\Message\DefaultAttachmentLocation(
                        new \Conjoon\Mail\Client\Message\DefaultMessageLocation(
                            $this->folderFlagCollection->getFolder(), 1
                        ), "1"
                    ),
                )
            ))
                instanceof
                \Conjoon\Mail\Server\Protocol\DefaultResult\GetAttachmentResult
        );

        $this->assertTrue(
            $this->failProtocol->setFlags(array(
                'user'       => $this->user,
                'parameters' => array(
                    'folderFlagCollection' => $this->folderFlagCollection,
                )
            ))
                instanceof
                \Conjoon\Mail\Server\Protocol\DefaultResult\ErrorResult
        );

    }

}
