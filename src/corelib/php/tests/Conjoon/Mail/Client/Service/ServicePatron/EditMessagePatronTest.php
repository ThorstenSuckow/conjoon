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


namespace Conjoon\Mail\Client\Service\ServicePatron;

/**
 * @see  Conjoon\Mail\Client\Service\ServicePatron\EditMessagePatron
 */
require_once 'Conjoon/Mail/Client/Service/ServicePatron/EditMessagePatron.php';


/**
 * @category   Conjoon
 * @package    Conjoon_Mail
 * @subpackage UnitTests
 * @group      Conjoon_Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class EditMessagePatronTest extends \Conjoon\DatabaseTestCaseDefault {

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
        $this->date  = new \DateTime('1970-01-01 00:00:00', new \DateTimeZone('UTC'));
        $this->date->setTimezone(new \DateTimeZone(date_default_timezone_get()));
        $this->compDate = $this->date->format('Y-m-d H:i:s');
        $this->date->setTimezone(new \DateTimeZone('UTC'));

        parent::setUp();

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
            array(
                'input' => array(
                    'message' => array(
                        'contentTextPlain' => '',
                        'contentTextHtml' => '',
                        'date' => $this->date,
                        'to' => '',
                        'cc' => '',
                        'from' => '',
                        'bcc' => '',
                        'replyTo' => '',
                        'subject' => '',
                        'attachments' => array()
                    )
                ),
                'output' => array(
                    'draft' => array(
                        'contentTextPlain' => '',
                        'contentTextHtml' => '',
                        'date' => $this->compDate,
                        'to' => array(),
                        'cc' => array(),
                        'from' => array(),
                        'bcc' => array(),
                        'replyTo' => array(),
                        'subject' => '',
                        'attachments' => array(),
                        'groupwareEmailAccountsId' => 1
                    )
                )
            )
        );

        $this->patron = new EditMessagePatron($this->service);
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
    public function testOk()
    {
        $this->assertEquals(
            $this->input[0]['output'],
            $this->patron->applyForData($this->input[0]['input'])
        );
    }

}
