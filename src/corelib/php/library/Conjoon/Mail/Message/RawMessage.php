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

namespace Conjoon\Mail\Message;


/**
 * An interface all raw messages have to implement.
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
interface RawMessage {

    /**
     * Returns the raw header for this message.
     *
     * @return string
     */
    public function getHeader();

    /**
     * Returns the raw body for this message.
     *
     * @return string
     */
    public function getBody();

}
