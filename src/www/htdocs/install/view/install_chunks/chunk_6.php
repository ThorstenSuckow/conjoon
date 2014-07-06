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
 * Install chunk_6
 *
 * Takes care of patching - final run
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */

/**
 * check if user is authorized to load script
 */
include('./scripts/check_auth.php');


    InstallLogger::getInstance($_SESSION['install_process']['INSTALL_LOGGER']);

    InstallLogger::stdout(InstallLogger::logMessage("Applying patches"));
    InstallLogger::stdout(InstallLogger::logMessage("Final run!"));

    // PREPARE PATCHES, IF ANY!
    if (isset($_SESSION['patches'])) {

        // apply patches, if any
        foreach ($_SESSION['patches'] as $patch => $doApply) {
            InstallLogger::stdout(InstallLogger::logMessage("Applying patch $patch"));
            if ($doApply) {
                if (file_exists('./patches/'.$patch.'/run.php')) {
                    InstallLogger::stdout(InstallLogger::logMessage("Running $patch..."));
                    include_once './patches/'.$patch.'/run.php';
                } else {
                    InstallLogger::stdout(InstallLogger::logMessage("$patch did not exist!"));
                }
            }
        }
    } else {
        InstallLogger::stdout(InstallLogger::logMessage("Nothing to do here..."));
    }


     echo "<script type=\"text/javascript\">this.location.href=\"./index.php?action=install_chunk_7\"</script>";