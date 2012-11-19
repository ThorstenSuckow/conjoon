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
 * An interface for services related to common mail storage operations.
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
interface Conjoon_Mail_Service_StorageService {


    /**
     * Return the raw message for the specified message number.
     *
     * @param  int $id The number of the message to retrieve.
     * @return string raw message
     *
     * @throws Conjoon_Mail_Service_MailServiceException
     * @throws Conjoon_Argument_Exception
     */
    public function getRawMessage($id);

}