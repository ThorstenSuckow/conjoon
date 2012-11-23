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

namespace Conjoon\Data\EntityManager\Mail;

use Conjoon\Data\Entity\SimpleDataEntity;

use Conjoon\Data\Entity\Mail\DefaultMailFolderEntity;

/**
 * @see Conjoon\Data\Entity\SimpleDataEntity
 */
require_once 'Conjoon/Data/Entity/SimpleDataEntity.php';


/**
 * @see Conjoon\Data\Entity\Mail\SimpleMailFolderEntityManager
 */
require_once 'Conjoon/Data/EntityManager/Mail/SimpleMailFolderEntityManager.php';

/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class SimpleMailFolderEntityManagerTest extends \PHPUnit_Framework_TestCase {

    protected $_folderEntityManager;

    /**
     *
     */
    protected function setUp()
    {
        $this->_folderEntityManager = new SimpleMailFolderEntityManager();
    }

    /**
     * @expectedException \Conjoon\Argument\InvalidArgumentException
     */
    public function testExceptionFind()
    {
        $this->_folderEntityManager->find("");
    }

    /**
     * @expectedException \Conjoon\Argument\InvalidArgumentException
     */
    public function testExceptionPersist()
    {
        $this->_folderEntityManager->persist(new SimpleDataEntity());
    }

    /**
     * @expectedException \Conjoon\Argument\InvalidArgumentException
     */
    public function testExceptionRemove()
    {
        $this->_folderEntityManager->remove(new SimpleDataEntity());
    }

    /**
     * Ensures everythingworks as expected.
     */
    public function testOk()
    {
        $entity = new DefaultMailFolderEntity();

        $this->assertSame(null, $this->_folderEntityManager->find(1));
        $this->assertSame(null, $this->_folderEntityManager->persist($entity));
        $this->assertSame(false, $this->_folderEntityManager->remove($entity));
    }
}