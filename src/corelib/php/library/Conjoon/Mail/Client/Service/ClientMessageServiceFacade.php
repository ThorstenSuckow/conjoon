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
 * Service facade for operations related to messages.
 *
 * @category   Conjoon_Mail
 * @package    Conjoon_Mail_Client
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
interface Conjoon_Mail_Client_Service_ClientMessageServiceFacade {

    /**
     * Updates the messages in the specified folder with the specified flag
     * settings.
     *
     * @param mixed $flag Information about the flags which should be set/unset
     * @param mixed $path A path to the folder where the messages which should
     *                    be flagged can be found.
     * @param Conjoon_User_AppUser $user The user object representing the user
     *                                   who triggered this operation
     *
     * @throws Conjoon_Mail_Client_Service_ClientMessageServiceException
     *
     */
    public function setFlagsForMessagesInFolder($flag, $path,
        Conjoon_User_AppUser $user);


}