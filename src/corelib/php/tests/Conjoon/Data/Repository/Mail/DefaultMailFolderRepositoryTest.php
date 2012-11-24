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
 * @see Conjoon\Data\Repository\Mail\DefaultMailFolderRepository
 */
require_once 'Conjoon/Data/Repository/Mail/DefaultMailFolderRepository.php';

/**
 * @see Conjoon\DatabaseTestCaseDefault
 */
require_once 'Conjoon/DatabaseTestCaseDefault.php';

/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultMailFolderRepositoryTest extends \Conjoon\DatabaseTestCaseDefault {

    public function getDataSet()
    {
        return $this->createXMLDataSet(
            dirname(__FILE__) . '/fixtures/mysql/mail_folder.xml'
        );
    }

    /**
     * Ensure everything works as expected
     */
    public function testFindById()
    {
        $this->assertEquals(
            3,
            $this->getConnection()->getRowCount('groupware_email_folders'),
            "Pre-Condition"
        );

        $repository = new DefaultMailFolderRepository;

        $entity = $repository->findById(3);

        $this->assertSame(3, $entity->getId());
        $this->assertSame('folder 3', $entity->getName());
        $this->assertSame(true, $entity->getIsChildAllowed());
        $this->assertSame(false, $entity->getIsLocked());
        $this->assertSame('root', $entity->getType());
        $this->assertSame('inbox', $entity->getMetaInfo());
        $this->assertSame(false, $entity->getIsDeleted());

        $entity = $entity->getParent();

        $this->assertTrue(
            $entity instanceof \Conjoon\Data\Entity\EntityProxy
        );

        $this->assertSame(2, $entity->getId());
        $this->assertSame('folder 2', $entity->getName());
        $this->assertSame(true, $entity->getIsChildAllowed());
        $this->assertSame(false, $entity->getIsLocked());
        $this->assertSame('root', $entity->getType());
        $this->assertSame('inbox', $entity->getMetaInfo());
        $this->assertSame(false, $entity->getIsDeleted());

        $entity = $entity->getParent();

        $this->assertTrue(
            $entity instanceof \Conjoon\Data\Entity\EntityProxy
        );

        $this->assertSame(1, $entity->getId());
        $this->assertSame('folder 1', $entity->getName());
        $this->assertSame(true, $entity->getIsChildAllowed());
        $this->assertSame(false, $entity->getIsLocked());
        $this->assertSame('root', $entity->getType());
        $this->assertSame('inbox', $entity->getMetaInfo());
        $this->assertSame(false, $entity->getIsDeleted());
        $this->assertSame(null, $entity->getParent());
    }

}