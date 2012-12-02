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


namespace Conjoon\Mail\Server\Protocol;

/**
 * @see \Conjoon\Mail\Server\Protocol\ProtocolResult
 */
require_once 'Conjoon/Mail/Server/Protocol/ProtocolResult.php';

/**
 * An interface for tagging an implementing class as a failed or erroneous
 * protocol operation result.
 *
 * @package Conjoon
 * @category Conjoon\Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
interface ErrorResult extends ProtocolResult {

}