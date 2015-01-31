<?php
/**
 * conjoon
 * (c) 2007-2015 conjoon.org
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


namespace Conjoon\Data\EntityCreator\Mail;

/**
 * Interface all AttachmentEntityCreator classes have to implement.
 *
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
interface AttachmentEntityCreator {


    /**
     * Returns an instance of \Conjoon\Data\Entity\Mail\AttachmentEntity.
     * This method must generate a key accordingly.
     *
     * @param array $options An array with the following key/value pairs:
     *              - mimeType
     *              - encoding
     *              - fileName
     *              - content
     *              - contentId
     *
     * @return \Conjoon\Data\Entity\Mail\AttachmentEntity
     *
     * @throws MailEntityCreatorException
     */
    public function createFrom(array $options);

    /**
     * Returns an array with all attachments found in the raw message. The
     * returned array may be empty.
     *
     * @param \Conjoon\Mail\Message\RawMessage
     *
     * @return array an array with instances of
     * \Conjoon\Data\Entity\Mail\AttachmentEntity
     *
     * @throws MailEntityCreatorException
     */
    public function createListFrom(\Conjoon\Mail\Message\RawMessage $message);
}
