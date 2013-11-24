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
 * An interface for strategies that transform the body of a mail message
 * into a readable format.
 *
 * @package Conjoon
 * @category Conjoon\Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
interface ReadableStrategy {

    /**
     * Returns a text based on the specified data found in the
     * passed argument.
     *
     * @param array $data
     *
     * @return \Conjoon\Mail\Client\Message\Strategy\ReadableStrategyResult;
     *
     * @throws \Conjoon\Mail\Client\Message\Strategy\StrategyException if anything fails
     */
    public function execute(array $data);

}
