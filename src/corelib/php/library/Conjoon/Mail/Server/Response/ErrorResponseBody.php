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


namespace Conjoon\Mail\Server\Response;

/**
 * @see \Conjoon\Mail\Server\Response\DefaultResponseBody
 */
require_once 'Conjoon/Mail/Server/Response/DefaultResponseBody.php';


/**
 * A response implementation to be used for setting the response body of a
 * \Conjoon\Mail\Server\Response.
 * This class should be used whenever a response indicates that the request was
 * processed properly.
 *
 * @package Conjoon
 * @category Conjoon\Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class ErrorResponseBody extends DefaultResponseBody {


}