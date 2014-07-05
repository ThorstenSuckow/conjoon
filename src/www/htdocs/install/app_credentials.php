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

    $user         = strtolower(addslashes(trim($_POST['user'])));
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