<?php
/**
 * conjoon
 * (c) 2007-2014 conjoon.org
 * licensing@conjoon.org
 *
 * conjoon
 * Copyright (C) 2014 Thorsten Suckow-Homberg/conjoon.org
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
*
 * $Author: T. Suckow $
 * $Id: cache.php 1985 2014-07-05 13:00:08Z T. Suckow $
 * $Date: 2014-07-05 15:00:08 +0200 (Sa, 05 Jul 2014) $
 * $Revision: 1985 $
 * $LastChangedDate: 2014-07-05 15:00:08 +0200 (Sa, 05 Jul 2014) $
 * $LastChangedBy: T. Suckow $
 * $URL: http://svn.conjoon.org/trunk/src/www/htdocs/install/cache.php $
 */

/**
 * Install chunk_7
 *
 * Cleaning up
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */

/**
 * check if user is authorized to load script
 */
include('./scripts/check_auth.php');


    InstallLogger::getInstance($_SESSION['install_process']['INSTALL_LOGGER']);

    InstallLogger::stdout(InstallLogger::logMessage("Cleaning up"), true);

    $INSTALL = array();
    $INSTALL['IMREMOVING'] = array(
        '_configCache' => file_exists('../_configCache'),
        'js'           => file_exists('../js')
    );

    // delete folders from a previous installation
    if ($INSTALL['IMREMOVING']['js']) {
        InstallLogger::stdout(InstallLogger::logMessage("Removing js from previous installation"));
        conjoon_rmdir('../js');
        rmdir('../js');
    }
    if ($INSTALL['IMREMOVING']['_configCache']) {
        InstallLogger::stdout(InstallLogger::logMessage("Removing _configCache from previous installation"));
        conjoon_rmdir('../_configCache');
        rmdir('../_configCache');
    }

    // move js folder to htdocs
    InstallLogger::stdout(InstallLogger::logMessage("Moving js"));
    rename('./files/js', '../js');

    // move _configCache to htdocs
    InstallLogger::stdout(InstallLogger::logMessage("Moving _configCache"));
    rename('./files/_configCache', '../_configCache');
    conjoon_copy('./htaccess.deny.txt', '../_configCache/.htaccess');

    InstallLogger::stdout(InstallLogger::logMessage("Done!"));
    InstallLogger::stdout("Click \"Next\" to finish!");

    $txt = "Done! You can find a detailed log of the update progress here: ".
           "<a target=\"_blank\" href=\"".$_SESSION['install_process']['INSTALL_LOGGER']."\">".
            $_SESSION['install_process']['INSTALL_LOGGER'] ."</a>";

    echo "<script type=\"text/javascript\">parent.updateProgressNote('" . $txt. "');</script>";
    echo "<script type=\"text/javascript\">parent.document.getElementById('nextButton').disabled = false;</script>";