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
 * Patch notes view
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
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
