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
