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

/**
 * @see Conjoon_Exception
 */
require_once 'Conjoon/Exception.php';

/**
 * Exception for Conjoon_Controller_Action namespace.
 *
 * Exception to be thrown if an action is called in an invalid context.
 *
 * @uses Conjoon_Exception
 * @package Conjoon
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Controller_Action_InvalidContextException
    extends Conjoon_Exception {
}