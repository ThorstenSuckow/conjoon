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

    public function getDataSet()
    {
        return $this->createXMLDataSet(
            dirname(__FILE__) . '/fixtures/mysql/account.xml'
        );
    }


    protected function setUp()
    {
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
            'forward' => array(

                'input' => array(
                    'message' => array(
                        'contentTextHtml' => '',
                        'contentTextPlain' => '',
                        'date' => '',
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
                        'date' => '1970-01-01 00:00:00',
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