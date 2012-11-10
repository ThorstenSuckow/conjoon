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
 * Simple check whether user is registered.
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */

if (!isset($_SESSION)
    || !array_key_exists('com.conjoon.session.install.authorized', $_SESSION)
    || !isset($_SESSION['com.conjoon.session.install.authorized'])
    || $_SESSION['com.conjoon.session.install.authorized'] !== true) {
    //die("Not authorized.");
}