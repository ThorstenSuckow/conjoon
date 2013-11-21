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
 * @see Conjoon\Argument\ArgumentCheck
 */
require_once 'Conjoon/Argument/ArgumentCheck.php';

/**
 * @see \Conjoon\Mail\Client\Message\Strategy\ReadableStrategy;
 */
require_once 'Conjoon/Mail/Client/Message/Strategy/ReadableStrategy.php';

use \Conjoon\Argument\ArgumentCheck;

/**
 * Tagging interface for classes which implement a readable strategy for transforming
 * plain text of mail bodies into a readable format.
 *
 * @package Conjoon
 * @category Conjoon\Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
interface PlainReadableStrategy extends ReadableStrategy {

}
