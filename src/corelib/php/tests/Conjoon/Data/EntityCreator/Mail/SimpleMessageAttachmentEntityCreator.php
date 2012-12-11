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
 * @see Conjoon\Data\EntityCreator\Mail\MessageAttachmentEntityCreator
 */
require_once 'Conjoon/Data/EntityCreator/Mail/MessageAttachmentEntityCreator.php';

/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class SimpleMessageAttachmentEntityCreator implements MessageAttachmentEntityCreator {

    public function createListFrom(\Conjoon\Mail\Message\RawMessage $message)
    {
        return array(
            new \Conjoon\Data\Entity\Mail\DefaultMessageAttachmentEntity(),
            new \Conjoon\Data\Entity\Mail\DefaultMessageAttachmentEntity()
        );
    }

    public function createFrom(array $options)
    {
        return new \Conjoon\Data\Entity\Mail\ImapMessageEntity();
    }

}
