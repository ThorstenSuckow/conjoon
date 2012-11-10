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
 * Lets the user chose login credentials for conjoon. This will be only shown if the user
 * is installing conjoon from scratch.
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */

/**
 * check if user is authorized to load script
 */
include('./scripts/check_auth.php');

$APPCREDENTIALS = array();
$APPCREDENTIALS['user_missing']     = false;
$APPCREDENTIALS['password_missing'] = false;
$APPCREDENTIALS['firstname_missing'] = false;
$APPCREDENTIALS['lastname_missing'] = false;
$APPCREDENTIALS['emailaddress_missing'] = false;

if (!isset($_SESSION['app_credentials'])) {
    $_SESSION['app_credentials'] = array(
        'user'          => 'admin',
        'password'      => 'password',
        'firstname'     => '',
        'lastname'      => '',
        'email_address' => ''
    );
}

$_SESSION['app_credentials_failed'] = false;

if (isset($_POST['app_credentials_post'])) {

    $user         = addslashes(trim($_POST['user']));
    $password     = addslashes(trim($_POST['password']));
    $firstname    = addslashes(trim($_POST['firstname']));
    $lastname     = addslashes(trim($_POST['lastname']));
    $emailaddress = addslashes(trim($_POST['email_address']));

    $_SESSION['app_credentials'] = array(
        'user'     => $user,
        'password' => $password,
        'firstname'     => $firstname,
        'lastname'      => $lastname,
        'email_address' => $emailaddress
    );

    if ($user == "") {
        $APPCREDENTIALS['user_missing'] = true;
        $_SESSION['app_credentials_failed'] = true;
    }

    if ($password == "") {
        $APPCREDENTIALS['password_missing'] = true;
        $_SESSION['app_credentials_failed'] = true;
    }

    if ($firstname == "") {
        $APPCREDENTIALS['firstname_missing'] = true;
        $_SESSION['app_credentials_failed'] = true;
    }

    if ($lastname == "") {
        $APPCREDENTIALS['lastname_missing'] = true;
        $_SESSION['app_credentials_failed'] = true;
    }

    if ($emailaddress == "") {
        $APPCREDENTIALS['emailaddress_missing'] = true;
        $_SESSION['app_credentials_failed'] = true;
    }

    if (!$_SESSION['app_credentials_failed']) {
        header("Location: ./?action=app_credentials_success");
        die();
    }

}

include_once './view/app_credentials.tpl';