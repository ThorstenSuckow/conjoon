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