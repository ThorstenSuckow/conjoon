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

namespace Conjoon\Data\Repository\Mail;

/**
 * @see Conjoon\Data\Repository\Mail\ImapMessageRepository
 */
require_once 'Conjoon/Data/Repository/Mail/ImapMessageRepository.php';

/**
 * @see \Conjoon\Data\EntityCreator\Mail\SimpleImapMessageEntityCreator
 */
require_once dirname(__FILE__) . '/../../EntityCreator/Mail/SimpleImapMessageEntityCreator.php';

/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class ImapMessageRepositoryTest extends \PHPUnit_Framework_TestCase {

    protected $repository;

    protected $mailAccount;

    protected $messageLocation;

    protected function setUp()
    {
        parent::setUp();

        $this->messageLocation = new \Conjoon\Mail\Client\Message\DefaultMessageLocation(
            new \Conjoon\Mail\Client\Folder\Folder(
                new \Conjoon\Mail\Client\Folder\DefaultFolderPath(
                    '["root", "79", "INBOXtttt", "rfwe2", "New folder (7)"]'
                )
            ), "1"
        );

        $this->mailAccount = new MailAccountMock(1);

        $this->repository = new ImapMessageRepository(
            $this->mailAccount,
            array(
                'imapConnectionClassName' =>
                    '\Conjoon\Data\Repository\Remote\DefaultImapConnection',
                'imapAdapteeClassName' =>
                    '\Conjoon\Data\Repository\Remote\SimpleImapAdaptee',
                'imapMessageEntityCreatorClassName' => '\Conjoon\Data\EntityCreator\Mail\SimpleImapMessageEntityCreator'
            )
        );
    }

    /**
     * Ensures everything works as expected.
     */
    public function testGetEntityClassName_Ok()
    {
        $this->assertSame(
            '\Conjoon\Data\Entity\Mail\ImapMessageEntity',
            $this->repository->getEntityClassName()
        );
   }

    /**
     * @expectedException \RuntimeException
     */
    public function testPersist_Exception()
    {
        // use mail account as entity, this is okay for now
        $this->repository->persist($this->mailAccount);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testRemove_Exception()
    {
        // use mail account as entity, this is okay for now
        $this->repository->remove($this->mailAccount);
    }

    /**
     * @expectedException \Conjoon\Argument\InvalidArgumentException
     */
    public function testFindById_Exception()
    {
        $this->repository->findById(1);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testFlush_Exception()
    {
        $this->repository->flush();
    }

    /**
     * Ensures everything works as expected.
     */
    public function testFindById()
    {
        $res = $this->repository->findById($this->messageLocation);

        $this->assertTrue(
            $res instanceof \Conjoon\Data\Entity\Mail\ImapMessageEntity
        );

    }

}