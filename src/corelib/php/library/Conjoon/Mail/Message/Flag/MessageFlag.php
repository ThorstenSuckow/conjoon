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


namespace Conjoon\Mail\Message\Flag;

/**
 * An interface for message flags. Provides details about the flag and the
 * message id of the flag.
 *
 * @category   Conjoon_Mail
 * @package    Conjoon_Mail_Message_Flag
 *
 */
interface MessageFlag  {

    /**
     * Returns the id for the message which flag has to be set.
     *
     * @return string
     */
    public function getUId();

    /**
     * Returns whether the flag should be removed.
     *
     * @return bool
     */
    public function isClear();

}