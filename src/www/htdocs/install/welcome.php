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
 * processes the data supplied by the welcome screen.
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */

/**
 * check if user is authorized to load script
 */
include('./scripts/check_auth.php');

if (!isset($_SESSION['support_key'])) {
    if (isset($_SESSION['installation_info']['support_key'])) {
        $_SESSION['support_key'] = $_SESSION['installation_info']['support_key'];
    }
}

if (!isset($_SESSION['edition'])) {
    if (isset($_SESSION['installation_info']['edition'])) {
        $_SESSION['edition'] = $_SESSION['installation_info']['edition'];
    } else {
        $_SESSION['edition'] = $_SESSION['setup_ini']['environment']['edition'];
    }
}

$WELCOME = array();
$WELCOME['license_agree_missing'] = false;
$WELCOME['backup_check_missing']  = false;

if (isset($_POST['welcome_post'])) {

    $supportKey = trim((string)$_POST['support_key']);
    $_SESSION['support_key'] = $supportKey;

    $htmlEntFlags = defined('ENT_HTML401')
                    ? ENT_COMPAT | ENT_HTML401
                    : ENT_COMPAT;

    $edition = htmlentities(
        trim((string)$_POST['edition']), $htmlEntFlags, 'UTF-8'
    );

    if ($edition == "") {
        $edition = $_SESSION['setup_ini']['environment']['edition'];
    }

    $_SESSION['edition'] = $edition;

    if (!isset($_POST['license_agree']) || (isset($_POST['license_agree']) && $_POST['license_agree'] != 1)) {
        $WELCOME['license_agree_missing'] = true;
    } else if (!isset($_POST['backup_check']) || (isset($_POST['backup_check']) && $_POST['backup_check'] != 1)) {
        $WELCOME['backup_check_missing'] = true;
    } else {
        header("Location: ./?action=welcome_success");
        die();
    }
}

include_once './view/welcome.tpl';