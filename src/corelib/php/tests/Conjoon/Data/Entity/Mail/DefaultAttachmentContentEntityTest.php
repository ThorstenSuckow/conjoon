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
 * @see Conjoon\Data\Entity\Mail\DefaultAttachmentContentEntity
 */
require_once 'Conjoon/Data/Entity/Mail/DefaultAttachmentContentEntity.php';

/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultAttachmentContentEntityTest extends \PHPUnit_Framework_TestCase {


    public function testOk()
    {
        $entity = new DefaultAttachmentContentEntity();

        $this->assertNull($entity->getId());

        $this->assertSame($entity,   $entity->setContent('content'));
        $this->assertSame('content', $entity->getContent());
    }

}