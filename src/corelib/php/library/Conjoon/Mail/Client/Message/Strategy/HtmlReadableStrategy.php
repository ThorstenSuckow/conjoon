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

namespace Conjoon\Mail\Client\Message\Strategy;

/**
 * @see \Conjoon\Mail\Client\Message\Strategy\ReadableStrategy;
 */
require_once 'Conjoon/Mail/Client/Message/Strategy/ReadableStrategy.php';


/**
 * Tagging interface for classes which implement a readable strategy for transforming
 * and sanitizing html text of mail bodies into a readable format.
 *
 * @package Conjoon
 * @category Conjoon\Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
interface HtmlReadableStrategy extends ReadableStrategy {


    /**
     * Returns true if this strategy allows external resources in email messages,
     * otehrwise false
     *
     * @return bool
     */
    public function areExternalResourcesAllowed();


}
