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
 * Patch notes view
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
function patch_0_1_4RC5()
{

    $dbAdapter  = $_SESSION['db_adapter'];
    $prefix     = $_SESSION['db_table_prefix'];
    $dbHost     = $_SESSION['db_host'];
    $db         = $_SESSION['db'];
    $dbPort     = $_SESSION['db_port'];
    $dbUser     = $_SESSION['db_user'];
    $dbPassword = $_SESSION['db_password'];
    $patchData  = $_SESSION['patchdata']['0.1.4RC5'];

    $inCharset = strtolower($patchData['in_charset']);
    $dbType   = strtolower(str_replace("pdo_", "", $dbAdapter));

    InstallLogger::logMessage("Applying patch 0.1.4RC5 - Start");

    if ($inCharset == "utf8") {
        InstallLogger::logMessage("in charset is already utf8... exiting.");
        InstallLogger::logMessage("Applying patch 0.1.4RC5 - End");
        return;
    }

    switch ($dbType) {
        case 'mysql':
            $db = new PDO(
                $dbType . ":" .
                "host=" . $dbHost . ";".
                "dbname=".$db.";".
                "port=".$dbPort,
                $dbUser, $dbPassword,
                array(
                    PDO::ATTR_PERSISTENT => false
                )
            );

            $dbNew = new PDO(
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

    // EMAIL ITEMS
    InstallLogger::logMessage("--- Applying patch 0.1.4RC5 to email items ---");
    $emailItemsTbl = $prefix."groupware_email_items";
    $sql           = "SELECT * FROM $emailItemsTbl";
    $emailItems    = $db->query($sql);

    if (!$emailItems) {
        InstallLogger::logMessage(
            "Could not update email items table with patch 0.1.4RC5"
        );
    } else {
        foreach ($emailItems as $row) {
            $id = $row['id'];

            $convert = array(
                'subject'            => $row['subject'],
                'from'               => $row['from'],
                'reply_to'           => $row['reply_to'],
                'to'                 => $row['to'],
                'cc'                 => $row['cc'],
                'bcc'                => $row['bcc'],
                'in_reply_to'        => $row['in_reply_to'],
                'content_text_plain' => $row['content_text_plain'],
	            'content_text_html'  => $row['content_text_html'],
                'recipients'         => $row['recipients'],
                'sender'             => $row['sender']
            );

            $updStr = array();
            foreach ($convert as $key => $value) {
                $convert[$key] = iconv(
                    'UTF-8', 'UTF-8//TRANSLIT//IGNORE', $value
                );
                $updStr[] = '`' . $key . '` = ?';
            }

            $sql = "UPDATE ".$emailItemsTbl." "
                   ."SET "
                   . implode(',', $updStr)
                   ." WHERE `id`=?";

            InstallLogger::logMessage(
                "Executing $sql"
            );

            $sth = $dbNew->prepare($sql);

            $updValues = array_values($convert);
            array_push($updValues, $id);

            if (!$sth->execute($updValues)) {
                $rt = $sth->errorInfo();
                InstallLogger::logMessage(
                    "Could not update $emailItemsTbl: "
                    .$rt[2]
                );
            }
        }
    }

    // FEED ITEMS
    InstallLogger::logMessage("--- Applying patch 0.1.4RC5 to feeds items ---");
    $feedItemsTbl = $prefix."groupware_feeds_items";
    $sql           = "SELECT * FROM $feedItemsTbl";
    $feedItems    = $db->query($sql);

    if (!$feedItems) {
        InstallLogger::logMessage(
            "Could not update $feedItemsTbl table with patch 0.1.4RC5"
        );
    } else {
        foreach ($feedItems as $row) {
            $id = $row['id'];

            $convert = array(
                'title'       => $row['title'],
                'description' => $row['description'],
                'content'     => $row['content']
            );

            $updStr = array();
            foreach ($convert as $key => $value) {
                $convert[$key] = iconv(
                    'UTF-8', 'UTF-8//TRANSLIT//IGNORE', $value
                );
                $updStr[] = '`' . $key . '` = ?';
            }

            $sql = "UPDATE ".$feedItemsTbl." "
                ."SET "
                . implode(',', $updStr)
                ." WHERE `id`=?";

            InstallLogger::logMessage(
                "Executing $sql"
            );

            $sth = $dbNew->prepare($sql);

            $updValues = array_values($convert);
            array_push($updValues, $id);

            if (!$sth->execute($updValues)) {
                $rt = $sth->errorInfo();
                InstallLogger::logMessage(
                    "Could not update $feedItemsTbl: "
                    .$rt[2]
                );
            }
        }
    }

    // FEED ACCOUNTS
    InstallLogger::logMessage("--- Applying patch 0.1.4RC5 to feeds accounts ---");
    $feedAccountsTbl = $prefix."groupware_feeds_accounts";
    $sql             = "SELECT * FROM $feedAccountsTbl";
    $feedAccounts    = $db->query($sql);

    if (!$feedAccounts) {
        InstallLogger::logMessage(
            "Could not update $feedAccountsTbl table with patch 0.1.4RC5"
        );
    } else {
        foreach ($feedAccounts as $row) {
            $id = $row['id'];

            $convert = array(
                'title'       => $row['title'],
                'description' => $row['description'],
                'name'        => $row['name']
            );

            $updStr = array();
            foreach ($convert as $key => $value) {
                $convert[$key] = iconv(
                    'UTF-8', 'UTF-8//TRANSLIT//IGNORE', $value
                );
                $updStr[] = '`' . $key . '` = ?';
            }

            $sql = "UPDATE ".$feedAccountsTbl." "
                ."SET "
                . implode(',', $updStr)
                ." WHERE `id`=?";

            InstallLogger::logMessage(
                "Executing $sql"
            );

            $sth = $dbNew->prepare($sql);

            $updValues = array_values($convert);
            array_push($updValues, $id);

            if (!$sth->execute($updValues)) {
                $rt = $sth->errorInfo();
                InstallLogger::logMessage(
                    "Could not update $feedAccountsTbl: "
                        .$rt[2]
                );
            }
        }
    }

    // USERS
    InstallLogger::logMessage("--- Applying patch 0.1.4RC5 to users ---");
    $usersTbl  = $prefix."users";
    $sql       = "SELECT * FROM $usersTbl";
    $userItems = $db->query($sql);

    if (!$userItems) {
        InstallLogger::logMessage(
            "Could not update $usersTbl table with patch 0.1.4RC5"
        );
    } else {
        foreach ($userItems as $row) {
            $id = $row['id'];

            $convert = array(
                'firstname'  => $row['firstname'],
                'lastname'   => $row['lastname'],
                'user_name'  => $row['user_name']
            );

            $updStr = array();
            foreach ($convert as $key => $value) {
                $convert[$key] = iconv(
                    'UTF-8', 'UTF-8//TRANSLIT//IGNORE', $value
                );
                $updStr[] = '`' . $key . '` = ?';
            }

            $sql = "UPDATE ".$usersTbl." "
                ."SET "
                . implode(',', $updStr)
                ." WHERE `id`=?";

             InstallLogger::logMessage(
                 "Executing $sql"
             );

            $sth = $dbNew->prepare($sql);

            $updValues = array_values($convert);
            array_push($updValues, $id);

            if (!$sth->execute($updValues)) {
                $rt = $sth->errorInfo();
                InstallLogger::logMessage(
                    "Could not update $usersTbl: "
                    .$rt[2]
                );
            }
        }
    }


    InstallLogger::logMessage("Applying patch 0.1.4RC5 - End");
}

patch_0_1_4RC5();


?>
