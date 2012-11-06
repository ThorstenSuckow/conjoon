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
 * processes the data supplied by the welcome screen.
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */

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
$WELCOME['config_okay_missing']   = false;

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

    if ($_SESSION['remove_config_ini_php'] && (!isset($_POST['config_okay']) || (isset($_POST['config_okay']) && $_POST['config_okay'] != 1))) {
        $WELCOME['config_okay_missing'] = true;
    } else if (!isset($_POST['license_agree']) || (isset($_POST['license_agree']) && $_POST['license_agree'] != 1)) {
        $WELCOME['license_agree_missing'] = true;
    } else if (!isset($_POST['backup_check']) || (isset($_POST['backup_check']) && $_POST['backup_check'] != 1)) {
        $WELCOME['backup_check_missing'] = true;
    } else {
        header("Location: ./?action=welcome_success");
        die();
    }
}

include_once './view/welcome.tpl';