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
 * @see  Conjoon\Mail\Client\Service\ServicePatron\ReplyMessagePatron
 */
require_once 'Conjoon/Mail/Client/Service/ServicePatron/ReplyMessagePatron.php';

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
class ReplyMessagePatronTest extends \Conjoon\DatabaseTestCaseDefault {

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
            'reply' => array(

                'input' => array(
                    'message' => array(
                        'contentTextPlain' => 'sfasfajksfajkl',
                        'contentTextHtml' => '',
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
                        'contentTextPlain' => '<blockquote>sfasfajksfajkl</blockquote>',
                        'contentTextHtml' => '',
                        'date' => '1970-01-01 00:00:00',
                        'to' => array(
                            array(
                                'address' => 'secretaddress@peterparker.com',
                                'name'    => ''
                            )
                        ),
                        'cc' => array(),
                        'bcc' => array(),
                        'subject' => 'Re: Subject',
                        'attachments' => array(),
                        'inReplyTo' => '<messageId>',
                        'references' => '<reference1> <reference2> <messageId>',
                        'groupwareEmailAccountsId' => 1
                    )
                )

        ),
            'replyAll' => array(

                    'input' => array(
                        'message' => array(
                            'contentTextPlain' => 'sfasfajksfajkl',
                            'contentTextHtml' => '',
                            'date' => '',
                            'to' => 'Local Local <local@somedomain.com>',
                            'cc' => 'yeahthisaddressshouldprettymuchnotappearinthegeneratedmessage@orly.com, IMIN@inaddress.com, Peter Griffin <peter.griffin@familyguy.com>',
                            'from' => 'Peter Parker <peter.parker@spiderman.com>',
                            'bcc' => '',
                            'replyTo' => '',
                            'subject' => 'Subject',
                            'messageId' => '<messageId>',
                            'references' => '<reference1> <reference2>',
                            'attachments' => array()
                        )
                    ),
                    'output' => array(
                        'draft' => array(
                            'contentTextPlain' => '<blockquote>sfasfajksfajkl</blockquote>',
                            'contentTextHtml' => '',
                            'date' => '1970-01-01 00:00:00',
                            'to' => array(array(
                                'name'    => 'Peter Parker',
                                'address' => 'peter.parker@spiderman.com'
                            )),
                            'cc' => array(
                                array(
                                    'name'    => 'Local Local',
                                    'address' => 'local@somedomain.com'
                                ),
                                array(
                                    'name'    => '',
                                    'address' => 'IMIN@inaddress.com'
                                ),
                                array(
                                    'name'    => 'Peter Griffin',
                                    'address' => 'peter.griffin@familyguy.com'
                                )
                            ),
                            'bcc' => array(),
                            'subject' => 'Re: Subject',
                            'attachments' => array(),
                            'inReplyTo' => '<messageId>',
                            'references' => '<reference1> <reference2> <messageId>',
                            'groupwareEmailAccountsId' => 1
                        )
                    )

            ));

        $this->patron = new ReplyMessagePatron(
            $this->service
        );
    }

    /**
     * @expectedException \Conjoon\Argument\InvalidArgumentException
     */
    public function testConstructor_Exception()
    {
        new ReplyMessagePatron(
            $this->service, array()
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
    public function testReplyOk_NoArgument()
    {
        $patron = new ReplyMessagePatron(
            $this->service
        );

        $this->assertEquals(
            $this->input['reply']['output'],
            $patron->applyForData($this->input['reply']['input'])
        );
    }

    /**
     * Ensures everything works as expected.
     */
    public function testReplyOk_Argument()
    {
        $patron = new ReplyMessagePatron(
            $this->service, false
        );

        $this->assertEquals(
            $this->input['reply']['output'],
            $patron->applyForData($this->input['reply']['input'])
        );
    }

    /**
     * Ensures everything works as expected.
     */
    public function testReplyAllOk()
    {
        $patron = new ReplyMessagePatron(
            $this->service, true
        );

        $this->assertEquals(
            $this->input['replyAll']['output'],
            $patron->applyForData($this->input['replyAll']['input'])
        );
    }

    /**
     * Ensures everything works as expected.
     */
    public function testReplyOk_False()
    {
        $patron = new ReplyMessagePatron(
            $this->service, true
        );

        $this->assertNotEquals(
            $this->input['reply']['output'],
            $patron->applyForData($this->input['reply']['input'])
        );
    }

}
