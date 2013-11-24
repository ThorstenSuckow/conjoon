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

namespace Conjoon\Mail\Client\Message;

/**
 * Exception to be thrown whenever external resources from a message are requested,
 * but this method does not provide external resources.
 *
 * @category   Conjoon
 * @package    Exception
 *
 * @uses \RuntimeException
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class NoExternalResourcesAvailableException extends \RuntimeException {

}
