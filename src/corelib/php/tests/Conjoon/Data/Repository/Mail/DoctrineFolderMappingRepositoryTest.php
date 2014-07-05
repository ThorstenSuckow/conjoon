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
 * @see Conjoon\Data\Repository\Mail\DoctrineFolderMappingRepository
 */
require_once 'Conjoon/Data/Repository/Mail/DoctrineFolderMappingRepository.php';

/**
 * @see Conjoon\DatabaseTestCaseDefault
 */
require_once 'Conjoon/DatabaseTestCaseDefault.php';

/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DoctrineFolderMappingRepositoryTest extends \Conjoon\DatabaseTestCaseDefault {

    protected $repository;

    protected $accountRepository;

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
            '\Conjoon\Data\Entity\Mail\DefaultFolderMappingEntity');

        $this->accountRepository = $this->_entityManager->getRepository(
            '\Conjoon\Data\Entity\Mail\DefaultMailAccountEntity');

        $this->assertTrue($this->repository
            instanceof \Conjoon\Data\Repository\Mail\DoctrineFolderMappingRepository);


        $this->assertEquals(
            3,
            $this->getConnection()->getRowCount('groupware_email_imap_mapping'),
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
        $this->assertSame('INBOX', $entity->getType());
        $this->assertSame('INBOX', $entity->getGlobalName());

        $this->assertNotNull($entity->getMailAccount());

        $account = $entity->getMailAccount();

        $this->assertSame(
            $account,
            $this->repository->findById(2)->getMailAccount()
        );
    }

    /**
     * Ensure everything works as expected
     */
    public function testCreateUsingAccount()
    {
        $account = $this->accountRepository->findById(1);

        $mapping = new \Conjoon\Data\Entity\Mail\DefaultFolderMappingEntity();
        $mapping->setType('SENT');
        $mapping->setGlobalName('INBOX/Sent');

        $account->addFolderMapping($mapping);

        $mapping->setMailAccount($account);
        $this->repository->register($mapping);
        $this->accountRepository->register($account);

        $this->assertEquals(3,
            $this->getConnection()->getRowCount('groupware_email_imap_mapping'),
            "Post-Condition"
        );

        $this->accountRepository->flush($account);

        $this->assertEquals(4,
            $this->getConnection()->getRowCount('groupware_email_imap_mapping'),
            "Post-Condition"
        );
    }

    /**
     * Ensure everything works as expected
     */
    public function testCreateUsingMappingItself()
    {
        $account = $this->accountRepository->findById(1);

        $mapping = new \Conjoon\Data\Entity\Mail\DefaultFolderMappingEntity();
        $mapping->setType('SENT');
        $mapping->setGlobalName('INBOX/Sent');

        $mapping->setMailAccount($account);
        $this->repository->register($mapping);

        $this->assertEquals(3,
            $this->getConnection()->getRowCount('groupware_email_imap_mapping'),
            "Post-Condition"
        );

        $this->repository->flush($mapping);

        $this->assertEquals(4,
            $this->getConnection()->getRowCount('groupware_email_imap_mapping'),
            "Post-Condition"
        );
    }

}
