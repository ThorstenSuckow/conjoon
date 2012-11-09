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
 * Cleans up session and finishes the installation wizard.
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */

$_SESSION = array();
session_destroy();


//finally, lock the installation
file_put_contents('./inst.lock', date("Y-m-d H:i:s", time()));

include_once './view/finish.tpl';