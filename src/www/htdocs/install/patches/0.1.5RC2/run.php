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
 * $Author$
 * $Id$
 * $Date$
 * $Revision$
 * $LastChangedDate$
 * $LastChangedBy$
 * $URL$
 */

/**
 * check if user is authorized to load script
 */
include('../../scripts/check_auth.php');

/**
 * Patch notes view
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
function patch_0_1_5RC2()
{

    $dbAdapter  = $_SESSION['db_adapter'];
    $prefix     = $_SESSION['db_table_prefix'];
    $dbHost     = $_SESSION['db_host'];
    $db         = $_SESSION['db'];
    $dbPort     = $_SESSION['db_port'];
    $dbUser     = $_SESSION['db_user'];
    $dbPassword = $_SESSION['db_password'];
    $dbType     = strtolower(str_replace("pdo_", "", $dbAdapter));

    $patchData  = $_SESSION['patchdata']['0.1.5RC2'];


    switch ($dbType) {
        case 'mysql':
            $db = new PDO(
                $dbType . ":" .
                    "host=" . $dbHost . ";".
                    "dbname=".$_SESSION['db'].";".
                    "port=".$dbPort,
                $dbUser, $dbPassword, array(
                    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8',
                    PDO::ATTR_PERSISTENT         => false
                )
            );

            break;

        default:
            die("No support for adapter \"$dbType\"");
            break;
    }

    $file = $patchData['dataFilePath'];

    if (!@file_exists($file)) {
        InstallLogger::logMessage("Cannot apply patch 0.1.5RC2 - $file not found");
        return;
    }

    $contents = trim(@file_get_contents($file));

    if (!$contents){
        InstallLogger::logMessage(
            "Cannot apply patch 0.1.5RC2 - $file "
            ."was empty or I could not access it"
        );
        return;
    }

    InstallLogger::logMessage(
        "Deleting from groupware_feeds_items_flags for Patch 0.1.5RC2"
    );


    $feedsItemsFlagsTbl = $prefix."groupware_feeds_items_flags";

    $lines = explode("\n", $contents);

    for ($i = 0, $len = count($lines); $i < $len; $i++) {
        $line   = $lines[$i];
        $values = explode(',', $line);

        $groupware_feeds_accounts_id = trim(array_shift($values));
        $guid = trim(implode($values, ','));

        $sql = "INSERT INTO $feedsItemsFlagsTbl "
            . "(`groupware_feeds_accounts_id`,`guid`) "
            . "VALUES"
            . "(?, ?)";

        $sth = $db->prepare($sql);

        $updValues = array(
            $groupware_feeds_accounts_id,
            md5($guid)
        );

        InstallLogger::logMessage(
            "Executing $sql with values " . implode(", ", $updValues)
        );

        if (!$sth->execute($updValues)) {
            $rt = $sth->errorInfo();
            InstallLogger::logMessage(
                "Could not insert into $feedsItemsFlagsTbl: "
                    .$rt[2]
            );
        }
    }

    InstallLogger::logMessage("Applying patch 0.1.5RC2 - End");
}

patch_0_1_5RC2();


?>