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
 * Interface all ImapMessageEntityCreator classes have to implement.
 *
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
interface ImapMessageEntityCreator {

    /**
     * Returns an instance of \Conjoon\Data\Entity\Mail\ImapMessageEntity.
     *
     * @param \Conjoon\Mail\Message\RawMessage
     *
     * @return \Conjoon\Data\Entity\Mail\ImapMessageEntity
     *
     * @throws MailEntityCreatorException
     */
    public function createFrom(\Conjoon\Mail\Message\RawMessage $message);

}