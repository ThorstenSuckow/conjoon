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


namespace Conjoon\Mail\Client\Service\ServicePatron;

/**
 * @see  Conjoon\Mail\Client\Service\ServicePatron\ForwardMessagePatron
 */
require_once 'Conjoon/Mail/Client/Service/ServicePatron/ForwardMessagePatron.php';

/**
 * @see  \Conjoon\User\SimpleUser
 */
require_once dirname(__FILE__) . '/../../../../User/SimpleUser.php';


/**
 * @category   Conjoon
 * @package    Conjoon_Mail
 * @subpackage UnitTests
 * @group      Conjoon_Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class ForwardMessagePatronTest extends \Conjoon\DatabaseTestCaseDefault {

    protected $input;

    protected $patron;

    protected $service;

    protected $date;

    protected $compDate;

    public function getDataSet()
    {
        return $this->createXMLDataSet(
            dirname(__FILE__) . '/fixtures/mysql/account.xml'
        );
    }


    protected function setUp()
    {
        parent::setUp();

        $this->date  = new \DateTime('1970-01-01 00:00:00', new \DateTimeZone('UTC'));
        $this->date->setTimezone(new \DateTimeZone(date_default_timezone_get()));
        $this->compDate = $this->date->format('Y-m-d H:i:s');
        $this->date->setTimezone(new \DateTimeZone('UTC'));

        $this->service =  new \Conjoon\Mail\Client\Account\DefaultAccountService(
            array(
                'user'          => new \Conjoon\User\SimpleUser(1),
                'mailAccountRepository' => $this->_entityManager->getRepository(
                    '\Conjoon\Data\Entity\Mail\DefaultMailAccountEntity'),
                'folderService' => new \Conjoon\Mail\Client\Folder\DefaultFolderService(array(
                    'user'                 => new \Conjoon\User\SimpleUser(1),
                    'mailFolderCommons'    => new \Conjoon\Mail\Client\Folder\DefaultFolderCommons(
                        array(
                            'user' => new \Conjoon\User\SimpleUser(1),
                            'mailFolderRepository' => $this->_entityManager->getRepository(
                                '\Conjoon\Data\Entity\Mail\DefaultMailFolderEntity'
                            ))
                    ),
                    'mailFolderRepository' => $this->_entityManager->getRepository(
                        '\Conjoon\Data\Entity\Mail\DefaultMailFolderEntity'
                    )))));

        $this->input = array(
            'forward' => array(

                'input' => array(
                    'message' => array(
                        'contentTextHtml' => '',
                        'contentTextPlain' => '',
                        'date' =>  $this->date,
                        'to' => 'gocheckitoutyo@receivingreceiver.com',
                        'cc' => '',
                        'from' => 'Peter Parker <peter.parker@spiderman.com>',
                        'bcc' => '',
                        'replyTo' => 'secretaddress@peterparker.com',
                        'subject' => 'Subject',
                        'messageId' => '<messageId>',
                        'references' => '<reference1> <reference2>',
                        'attachments' => array()
                    )
                ),
                'output' => array(
                    'draft' => array(
                        'contentTextHtml' => '',
                        'date' => $this->compDate,
                        'to' => array(),
                        'cc' => array(),
                        'bcc' => array(),
                        'subject' => 'Fwd: Subject',
                        'attachments' => array(),
                        'inReplyTo' => '',
                        'references' => '',
                        'groupwareEmailAccountsId' => 1
                    )
                )

        ));

        $this->patron = new ForwardMessagePatron(
            $this->service
        );
    }

    /**
     * @expectedException \Conjoon\Mail\Client\Service\ServicePatron\ServicePatronException
     */
    public function testApplyForData_Exception()
    {
        $this->patron->applyForData(array(
           'test' => array()
        ));
    }

    /**
     * Ensures everything works as expected.
     */
    public function testForwardOk()
    {
        $d = $this->patron->applyForData($this->input['forward']['input']);

        unset($d['draft']['contentTextPlain']);
        unset($this->input['forward']['output']['contentTextPlain']);

        $this->assertEquals(
            $this->input['forward']['output'], $d
        );
    }


}
