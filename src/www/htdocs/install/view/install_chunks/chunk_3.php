<?php
/**
 * conjoon
 * (c) 2007-2015 conjoon.org
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
 * Install chunk_3
 *
 * Takes care of db updates
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */

/**
 * check if user is authorized to load script
 */
include('./scripts/check_auth.php');

    $libFolder = $_SESSION['setup_ini']['lib_path']['folder'];

    $text = "Updating database. Depending on your database size, this can take a while. " .
            "Please note that progress marked with \"failure\" " .
            "is not an indication for a failed update attempt... ".
            "Don\'t worry, we got this.";

    InstallLogger::getInstance($_SESSION['install_process']['INSTALL_LOGGER']);

    InstallLogger::stdout(InstallLogger::logMessage($text), true);
    InstallLogger::stdout(InstallLogger::logMessage("Updating database"));

    $dbConnInfo = array(
        'host'     => $_SESSION['db_host'],
        'port'     => $_SESSION['db_port'],
        'database' => $_SESSION['db'],
        'user'     => $_SESSION['db_user'],
        'password' => $_SESSION['db_password'],
        'prefix'   => $_SESSION['db_table_prefix']
    );

    // import the sql file for the selected database
    $path = realpath('./files/datastore/mysql/conjoon.sql');
    conjoon_createTables($path, $_SESSION['db_adapter'], $dbConnInfo);
    $table = "";
    sleep(1);
    // create root user if needed
    if (!isset($_SESSION['installation_info']['app_credentials'])) {
        conjoon_createAdmin($_SESSION['db_adapter'], array(
            'user'          => $_SESSION['app_credentials']['user'],
            'password'      => $_SESSION['app_credentials']['password'],
            'firstname'     => $_SESSION['app_credentials']['firstname'],
            'lastname'      => $_SESSION['app_credentials']['lastname'],
            'email_address' => $_SESSION['app_credentials']['email_address']
        ), $dbConnInfo);
    }
    sleep(1);
    // add fixtures
    $path = realpath('./files/datastore/mysql/fixtures.sql');
    conjoon_insertFixtures($path, $_SESSION['db_adapter'], $dbConnInfo);


    echo "<script type=\"text/javascript\">this.location.href=\"./index.php?action=install_chunk_4\"</script>";