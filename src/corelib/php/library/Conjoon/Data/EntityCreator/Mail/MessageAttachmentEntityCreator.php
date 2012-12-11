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
 * Interface all MessageAttachmentEntityCreator classes have to implement.
 *
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
interface MessageAttachmentEntityCreator {


    /**
     * Returns an instance of \Conjoon\Data\Entity\Mail\MessageAttachmentEntity.
     * This method must generate a key accordingly.
     *
     * @param array $options An array with the following key/value pairs:
     *              - mimeType
     *              - encoding
     *              - fileName
     *              - content
     *              - contentId
     *
     * @return \Conjoon\Data\Entity\Mail\MessageAttachmentEntity
     *
     * @throws MailEntityCreatorException
     */
    public function createFrom(array $options);

}