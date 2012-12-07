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
 * check if user is authorized to load script
 */
include('../../scripts/check_auth.php');

/**
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
function patch_prepare_0_1_5RC2()
{

    $dbAdapter  = $_SESSION['db_adapter'];
    $prefix     = $_SESSION['db_table_prefix'];
    $dbHost     = $_SESSION['db_host'];
    $db         = $_SESSION['db'];
    $dbPort     = $_SESSION['db_port'];
    $dbUser     = $_SESSION['db_user'];
    $dbPassword = $_SESSION['db_password'];
    $dbType     = strtolower(str_replace("pdo_", "", $dbAdapter));

    if (!isset($_SESSION['patchdata']['0.1.5RC2'])) {
        $_SESSION['patchdata']['0.1.5RC2'] = array();
    }

    $_SESSION['patchdata']['0.1.5RC2']['dataFilePath']
        = realpath(dirname(__FILE__) . '/../../') . '/patch.0.1.5RC2.data.txt';

    InstallLogger::logMessage("Preparing patch 0.1.5RC2 - collecting data");

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

    // groupware_feeds_items_flags
    $feedsItemsFlagsTbl = $prefix."groupware_feeds_items_flags";
    $sql                = "SELECT * FROM $feedsItemsFlagsTbl";
    $feedsItemsFlags    = $db->query($sql);

    if (!$feedsItemsFlags) {
        InstallLogger::logMessage(
            "Could not read out data for patch 0.1.5RC2"
        );
    } else {
        $strs = array();
        foreach ($feedsItemsFlags as $row) {

            $strs[] = $row['groupware_feeds_accounts_id']
                      . ", "
                      . $row['guid'];

            InstallLogger::logMessage(
                "Collecting: " .$strs[count($strs) - 1]
            );

        }

        // write data into file
        InstallLogger::logMessage(
            "Writing " . count($strs) . " lines into "
            . $_SESSION['patchdata']['0.1.5RC2']['dataFilePath']
        );

        $res = @file_put_contents(
            $_SESSION['patchdata']['0.1.5RC2']['dataFilePath'],
            implode($strs, "\n")
        );

        if ($res === false) {
            InstallLogger::logMessage(
                "Could not write patch data into "
                . $_SESSION['patchdata']['0.1.5RC2']['dataFilePath']
            );
        } else {
            $sql = "DELETE FROM $feedsItemsFlagsTbl";

            InstallLogger::logMessage("Executing $sql");

            $deleted = $db->exec($sql);

            if ($deleted === false) {
                $rt = $db->errorInfo();
                InstallLogger::logMessage(
                    "Could not delete from $feedsItemsFlagsTbl: "
                        .$rt[2]
                );
            }
        }

    }

    InstallLogger::logMessage("Collecting patch data 0.1.5RC2 - End");
}

patch_prepare_0_1_5RC2();


?>