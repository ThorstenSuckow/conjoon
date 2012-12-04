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

namespace Conjoon\Data\Entity\Mail;

/**
 * @see Conjoon\Data\Entity\Mail\DefaultMailFolderEntity
 */
require_once 'Conjoon/Data/Entity/Mail/DefaultMailFolderEntity.php';

/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultMailFolderEntityTest extends \PHPUnit_Framework_TestCase {

    protected $_input;

    protected $_parent;

    protected function setUp()
    {
        $this->_input = array(
            'id' => "id",
            'name' => "name",
            'isChildAllowed' => true,
            'isLocked' => false,
            'type' => "type",
            'metaInfo' => "metaInfp",
            'isDeleted' => false,
            'parent' => null
        );
    }

    /**
     * Ensures everything works as expected
     */
    public function testOk()
    {
        $folder = new DefaultMailFolderEntity();

        $folder->setId($this->_input['id']);
        $this->assertSame($this->_input['id'], $folder->getId());

        $folder->setName($this->_input['name']);
        $this->assertSame($this->_input['name'], $folder->getName());

        $folder->setIsChildAllowed($this->_input['isChildAllowed']);
        $this->assertSame($this->_input['isChildAllowed'], $folder->getIsChildAllowed());

        $folder->setIsLocked($this->_input['isLocked']);
        $this->assertSame($this->_input['isLocked'], $folder->getIsLocked());

        $folder->setType($this->_input['type']);
        $this->assertSame($this->_input['type'], $folder->getType());

        $folder->setMetaInfo($this->_input['metaInfo']);
        $this->assertSame($this->_input['metaInfo'], $folder->getMetaInfo());

        $folder->setIsDeleted($this->_input['isDeleted']);
        $this->assertSame($this->_input['isDeleted'], $folder->getIsDeleted());

        $this->assertSame(0, count($folder->getMailAccounts()));

        $folder->setParent(null);
        $this->assertSame(null, $folder->getParent());
        $fl2 = new DefaultMailFolderEntity();
        $folder->setParent($fl2);
        $this->assertSame($fl2, $folder->getParent());
    }

}