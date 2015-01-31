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

/**
 * @see Conjoon_Mail_Service_StorageService
 */
require_once 'Conjoon/Mail/Service/StorageService.php';


/**
 * An interface for services related to common mail storage operations for the
 * IMAP protocol.
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
interface Conjoon_Mail_Service_ImapStorageService
    extends Conjoon_Mail_Service_StorageService  {

    /**
     * Returns the message headers for the global name.
     *
     * @param string $globalName
     *
     * @return array An array with raw header informations.
     *
     * @throws Conjoon_Mail_Service_MailServiceException
     * @throws Conjoon_Argument_Exception
     */
    public function getHeaderListForGlobalName($globalName);

    /**
     * Returns the message headers, flags, uid and the bodystructure for all
     * messages found for the global name.
     *
     * @param string $globalName
     *
     * @return array A numeric array where each value is in turn an array with
     * the following keys:
     *  - header: the raw header text
     *  - bodystructure: an array with the bodystructure informations
     *  - flags: an array with all flags for the message.
     *  - uid: The uinique identifier
     *
     * @throws Conjoon_Mail_Service_MailServiceException
     * @throws Conjoon_Argument_Exception
     */
    public function getHeaderListAndMetaInformationForGlobalName($globalName);

}