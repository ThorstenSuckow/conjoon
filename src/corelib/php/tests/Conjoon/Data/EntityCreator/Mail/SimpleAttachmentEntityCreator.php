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

namespace Conjoon\Data\EntityCreator\Mail;

/**
 * @see Conjoon\Data\EntityCreator\Mail\AttachmentEntityCreator
 */
require_once 'Conjoon/Data/EntityCreator/Mail/AttachmentEntityCreator.php';

/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class SimpleAttachmentEntityCreator implements AttachmentEntityCreator {

    public function createListFrom(\Conjoon\Mail\Message\RawMessage $message)
    {
        return array(
            new \Conjoon\Data\Entity\Mail\DefaultAttachmentEntity(),
            new \Conjoon\Data\Entity\Mail\DefaultAttachmentEntity()
        );
    }

    public function createFrom(array $options)
    {
        return new \Conjoon\Data\Entity\Mail\ImapMessageEntity();
    }

}
