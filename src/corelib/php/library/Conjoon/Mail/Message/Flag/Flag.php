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
 * A tagging interface for flags. Should not be used directly. Instead,
 * sub classes should be used
 * project.
 *
 * @category   Conjoon_Mail
 * @package    Conjoon_Mail_Message_Flag
 *
 */
interface Flag  {

    /**
     * Returns a textual representation of this flag.
     *
     * @return string
     *
     * @abstract
     */
    public function __toString();

}