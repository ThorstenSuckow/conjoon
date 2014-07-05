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

            $sth = $dbNew->prepare($sql);

            $updValues = array_values($convert);
            array_push($updValues, $id);

            InstallLogger::logMessage(
                "Executing $sql with values " . implode(", ", $updValues)
            );

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

            $sth = $dbNew->prepare($sql);

            $updValues = array_values($convert);
            array_push($updValues, $id);

            InstallLogger::logMessage(
                "Executing $sql with values " . implode(", ", $updValues)
            );

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

            $sth = $dbNew->prepare($sql);

            $updValues = array_values($convert);
            array_push($updValues, $id);

            InstallLogger::logMessage(
                "Executing $sql with values " . implode(", ", $updValues)
            );

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

            $sth = $dbNew->prepare($sql);

            $updValues = array_values($convert);
            array_push($updValues, $id);

            InstallLogger::logMessage(
                "Executing $sql with values " . implode(", ", $updValues)
            );

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
