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
 * @see Conjoon\Data\EntityCreator\Mail\ImapMessageEntityCreator
 */
require_once 'Conjoon/Data/EntityCreator/Mail/ImapMessageEntityCreator.php';

/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class SimpleImapMessageEntityCreator implements ImapMessageEntityCreator {

    public function createFrom(\Conjoon\Mail\Message\RawMessage $message)
    {
        return new \Conjoon\Data\Entity\Mail\ImapMessageEntity();
    }

}
