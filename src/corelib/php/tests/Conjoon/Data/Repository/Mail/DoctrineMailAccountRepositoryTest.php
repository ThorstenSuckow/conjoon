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

namespace Conjoon\Data\Repository\Mail;

/**
 * @see Conjoon\Data\Repository\Mail\DoctrineMailAccountRepository
 */
require_once 'Conjoon/Data/Repository/Mail/DoctrineMailAccountRepository.php';

/**
 * @see Conjoon\DatabaseTestCaseDefault
 */
require_once 'Conjoon/DatabaseTestCaseDefault.php';

/**
 * @see Conjoon\User\SimpleUser
 */
require_once dirname(__FILE__) . '/../../../User/SimpleUser.php';

/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DoctrineMailAccountRepositoryTest extends \Conjoon\DatabaseTestCaseDefault {

    protected $repository;

    public function getDataSet()
    {
        return $this->createXMLDataSet(
            dirname(__FILE__) . '/fixtures/mysql/mail_account.xml'
        );
    }

    public function setUp()
    {
        parent::setUp();

        $this->repository = $this->_entityManager->getRepository(
            '\Conjoon\Data\Entity\Mail\DefaultMailAccountEntity');

        $this->assertTrue($this->repository
            instanceof \Conjoon\Data\Repository\Mail\DoctrineMailAccountRepository);


        $this->assertEquals(
            3,
            $this->getConnection()->getRowCount('groupware_email_accounts'),
            "Pre-Condition"
        );
    }

    protected function compareDataset($fileName)
    {
        $queryTable = $this->getConnection()->createQueryTable(
            'groupware_email_accounts', 'SELECT * FROM groupware_email_accounts'
        );
        $expectedTable = $this->createXmlDataSet(
            dirname(__FILE__) . '/fixtures/mysql/' . $fileName
        )->getTable("groupware_email_accounts");
        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    /**
     * Ensures everything works as expected.
     *
     * @ticket CN-706
     */
    public function testMailAccountsForUser()
    {
        $tmp  = $this->repository->findById(1);
        $user = $tmp->getUser();

        $entities = $this->repository->getMailAccounts($user);

        $this->assertTrue(is_array($entities));
        $this->assertSame(1, count($entities));

        $this->assertTrue($entities[0] instanceof \Conjoon\Data\Entity\Mail\MailAccountEntity);

        $entities = $this->repository->getMailAccounts(
            new \Conjoon\User\SimpleUser()
        );

        $this->assertTrue(is_array($entities));
        $this->assertSame(0, count($entities));


        $tmp  = $this->repository->findById(3);
        $user = $tmp->getUser();

        $entities = $this->repository->getMailAccounts($user);

        $this->assertTrue(is_array($entities));
        $this->assertSame(0, count($entities));
    }

    /**
     * Ensures everything works as expected.
     */
    public function testGetStandardMailAccount()
    {
        $tmp  = $this->repository->findById(1);
        $user = $tmp->getUser();

        $entity = $this->repository->getStandardMailAccount($user);

        $this->assertSame(1, $entity->getId());

        $tmp  = $this->repository->findById(2);
        $user = $tmp->getUser();

        $entity = $this->repository->getStandardMailAccount($user);

        $this->assertNull($entity);
    }

    /**
     * Ensures everything works as expected.
     *
     * @ticket CN-705
     */
    public function testGetStandardMail_AccountMarkedDeleted()
    {
        $tmp  = $this->repository->findById(3);
        $user = $tmp->getUser();

        $account = $this->repository->findById(3);

        $this->assertSame(3, $account->getUser()->getId());
        $this->assertTrue($account->getIsDeleted());

        $entity = $this->repository->getStandardMailAccount($user);

        $this->assertNull(null, $entity);
    }

    /**
     * Ensure everything works as expected
     */
    public function testFindNone()
    {
        $entity = $this->repository->findById(97809732);

        $this->assertSame(null, $entity);
    }

    /**
     * Ensure everything works as expected
     */
    public function testFindById()
    {
        $entity = $this->repository->findById(1);

        $this->assertSame(1, $entity->getId());
        $this->assertSame('address', $entity->getAddress());
        $this->assertSame(false, $entity->getIsDeleted());
        $this->assertSame('reply_address', $entity->getReplyAddress());

        $this->assertSame(3, count($entity->getFolderMappings()));

        $res = $entity->getFolderMappings();

        $this->assertSame('INBOX', $res[0]->getType());
        $this->assertSame('INBOX', $res[0]->getGlobalName());

        $user = $entity->getUser();

        $this->assertSame(1, $user->getId());
    }

    /**
     * Ensures everything works as expected.
     */
    public function testPersistCreate()
    {
        $entity = new \Conjoon\Data\Entity\Mail\DefaultMailAccountEntity();

        $entity->setName("newname");
        $entity->setAddress("address");
        $entity->setIsStandard(false);
        $entity->setProtocol('POP3');
        $entity->setServerInbox('server_inbox');
        $entity->setServerOutbox('server_outbox');
        $entity->setUsernameInbox('username_inbox');
        $entity->setUsernameOutbox('username_outbox');
        $entity->setUserName('user_name');
        $entity->setIsOutboxAuth(false);
        $entity->setPasswordInbox("password_inbox");
        $entity->setIsSignatureUsed(false);
        $entity->setPortInbox(22);
        $entity->setPortOutbox(33);
        $entity->setIsCopyLeftOnServer(false);
        $entity->setIsDeleted(false);

        $tmp = $this->repository->findById(1);
        $user   = $tmp->getUser();
        $this->assertSame(1, $user->getId());
        $entity->setUser($user);

        // PERSIST
        $this->repository->register($entity);

        $this->assertEquals(3,
            $this->getConnection()->getRowCount('groupware_email_accounts'),
            "Post-Condition"
        );

        $this->repository->flush();

        $this->assertEquals(4,
            $this->getConnection()->getRowCount('groupware_email_accounts'),
            "Post-Condition"
        );

    }

    /**
     * Ensures everything works as expected.
     */
    public function testPersistUpdate()
    {
        $entity = $this->repository->findById(1);

        $entity->setName("testchange");

        $this->repository->register($entity);

        $this->assertEquals(
            3,
            $this->getConnection()->getRowCount('groupware_email_accounts'),
            "Pre-Condition"
        );

        $this->compareDataset('mail_account.xml');

        // FLUSH
        $this->repository->flush();

        $this->compareDataset('mail_account.update.result.xml');
    }

    /**
     * Ensures everything works as expected.
     */
    public function testRemove()
    {
        $entity = $this->repository->findById(1);

        $this->repository->remove($entity);

        $this->assertEquals(
            3,
            $this->getConnection()->getRowCount('groupware_email_folders'),
            "Pre-Condition"
        );

        $this->compareDataset('mail_account.xml');

        // FLUSH
        $this->repository->flush();

        $this->compareDataset('mail_account.remove.result.xml');

        // check groupware email folders accounts
        $queryTable = $this->getConnection()->createQueryTable(
            'groupware_email_folders_accounts', 'SELECT * FROM groupware_email_folders_accounts'
        );
        $expectedTable = $this->createXmlDataSet(
            dirname(__FILE__) . '/fixtures/mysql/mail_account.remove.result.xml'
        )->getTable("groupware_email_folders_accounts");
        $this->assertTablesEqual($expectedTable, $queryTable);

        // check mappings
        $queryTable = $this->getConnection()->createQueryTable(
            'groupware_email_imap_mapping', 'SELECT * FROM groupware_email_imap_mapping'
        );
        $expectedTable = $this->createXmlDataSet(
            dirname(__FILE__) . '/fixtures/mysql/mail_account.remove.result.xml'
        )->getTable("groupware_email_imap_mapping");
        $this->assertTablesEqual($expectedTable, $queryTable);
    }

}
