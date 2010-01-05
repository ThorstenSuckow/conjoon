<?php
/**
 * conjoon
 * (c) 2002-2010 siteartwork.de/conjoon.org
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
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */

$_SESSION = array();
session_destroy();

include_once './view/finish.tpl';