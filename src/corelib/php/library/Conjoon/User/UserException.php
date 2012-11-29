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

namespace Conjoon\User;

/**
 * @see Conjoon_Exception
 */
require_once 'Conjoon/Exception.php';


/**
 * This is a generic user exception class. Classes are advised to use
 * sub-classes of this exception which give more detail about the type of
 * exception.
 *
 *
 * @category   Conjoon
 * @package    Exception
 *
 * @uses Conjoon_Exception
 */
class UserException extends \Conjoon_Exception {

}

