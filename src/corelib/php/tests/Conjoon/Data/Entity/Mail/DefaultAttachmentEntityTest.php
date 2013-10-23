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
 * @see Conjoon\Data\Entity\Mail\DefaultAttachmentEntity
 */
require_once 'Conjoon/Data/Entity/Mail/DefaultAttachmentEntity.php';

/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultAttachmentEntityTest extends \PHPUnit_Framework_TestCase {


    public function testOk()
    {
        $entity  = new DefaultAttachmentEntity();
        $content = new DefaultAttachmentContentEntity();

        $this->assertNull($entity->getId());

        $this->assertSame(
            $entity,
            $entity->setAttachmentContent($content)
        );
        $this->assertSame($content, $entity->getAttachmentContent());

        $this->assertSame($entity, $entity->setKey('key'));
        $this->assertSame('key',   $entity->getKey());

        $this->assertSame($entity,    $entity->setFileName('fileName'));
        $this->assertSame('fileName', $entity->getFileName());

        $this->assertSame($entity,    $entity->setMimeType('mimeType'));
        $this->assertSame('mimeType', $entity->getMimeType());

        $this->assertSame($entity,    $entity->setEncoding('encoding'));
        $this->assertSame('encoding', $entity->getEncoding());

        $this->assertSame($entity,     $entity->setContentId('contentId'));
        $this->assertSame('contentId', $entity->getContentId());
    }

}
