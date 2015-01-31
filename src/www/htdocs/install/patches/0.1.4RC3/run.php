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
function patch_0_1_4RC3()
{
    $dbAdapter  = $_SESSION['db_adapter'];
    $prefix     = $_SESSION['db_table_prefix'];
    $dbHost     = $_SESSION['db_host'];
    $db         = $_SESSION['db'];
    $dbPort     = $_SESSION['db_port'];
    $dbUser     = $_SESSION['db_user'];
    $dbPassword = $_SESSION['db_password'];
    $patchData  = $_SESSION['patchdata']['0.1.4RC3'];

    $timezone = $patchData['timezone'];
    $dbType   = strtolower(str_replace("pdo_", "", $dbAdapter));

    switch ($dbType) {
        case 'mysql':
            $db = new PDO(
                $dbType . ":" .
                "host=" . $dbHost . ";".
                "dbname=".$db.";".
                "port=".$dbPort,
                $dbUser, $dbPassword
            );
         break;

        default:
            die("No support for adapter \"$dbType\"");
        break;
    }
    InstallLogger::logMessage("Applying patch 0.1.4RC3 - Start");

    InstallLogger::logMessage("Using timezone $timezone to convert to UTC");

    $oldTimezone = date_default_timezone_get();
    date_default_timezone_set($timezone);

    // EMAIL ITEMS
    InstallLogger::logMessage("--- Applying patch 0.1.4RC3 to email items ---");
    $sql = "SELECT `id`,`date` FROM ".$prefix."groupware_email_items";
    $datetimes = $db->query($sql);

    if (!$datetimes) {
        InstallLogger::logMessage("Could not update email items table with patch 0.1.4RC3");
    } else {
        foreach ($datetimes as $row) {
            $time = $row['date'];
            $id   = $row['id'];

            $new = strtotime($time);
            $new = gmdate("Y-m-d H:i:s", $new);

            InstallLogger::logMessage("Changing date in email items for id $id from $time to $new");

            $sql = "UPDATE ".$prefix."groupware_email_items "
                   ."SET `date`='".$new."' WHERE `id`=".$id;

            $db->query($sql);

        }
    }

    // FEED ITEMS
    InstallLogger::logMessage("--- Applying patch 0.1.4RC3 to feeds items ---");
    $sql = "SELECT `id`,`pub_date` FROM ".$prefix."groupware_feeds_items";
    $datetimes = $db->query($sql);

    if (!$datetimes) {
        InstallLogger::logMessage("Could not update feed items table with patch 0.1.4RC3");
    } else {
        foreach ($datetimes as $row) {
            $time = $row['pub_date'];
            $id   = $row['id'];

            $new = strtotime($time);
            $new = gmdate("Y-m-d H:i:s", $new);

            InstallLogger::logMessage("Changing date in feed items for id $id from $time to $new");

            $sql = "UPDATE ".$prefix."groupware_feeds_items "
                   ."SET `pub_date`='".$new."' WHERE `id`=".$id;

            $db->query($sql);
        }
    }

    date_default_timezone_set($oldTimezone);

    InstallLogger::logMessage("Applying patch 0.1.4RC3 - End");
}

patch_0_1_4RC3();


?>
