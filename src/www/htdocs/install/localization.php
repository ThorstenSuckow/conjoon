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
 * Asks for localization information.
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */

/**
 * check if user is authorized to load script
 */
include('./scripts/check_auth.php');

$LOCALIZATION = array(
    'timezone_options' => array(),
    'date_timezone'    => date_default_timezone_get()
);

$tzs = file_get_contents('./timezones.txt');

$lns = explode("\n", $tzs);

for ($i = 0, $len = count($lns); $i < $len; $i++) {
    $LOCALIZATION['timezone_options'][] = trim($lns[$i]);
}

$tmpLocale = array();
if (isset($_SESSION['installation_info'])) {
    $tmpLocale = $_SESSION['installation_info'];
}

$_SESSION['locale_timezone_default'] = isset($_SESSION['locale_timezone_default'])
                                       ? $_SESSION['locale_timezone_default']
                                       : (isset($tmpLocale['locale_timezone_default'])
                                       ? $tmpLocale['locale_timezone_default']
                                       : $LOCALIZATION['date_timezone']);

$_SESSION['locale_timezone_fallback'] = isset($_SESSION['locale_timezone_fallback'])
                                       ? $_SESSION['locale_timezone_fallback']
                                       : (isset($tmpLocale['locale_timezone_fallback'])
                                       ? $tmpLocale['locale_timezone_fallback']
                                       : $LOCALIZATION['date_timezone']);

if (isset($_POST['localization_check'])) {

    $_SESSION['locale_timezone_default']  = $_POST['locale_timezone_default'];
    $_SESSION['locale_timezone_fallback'] = $_POST['locale_timezone_fallback'];


    header("Location: ./?action=localization_success");
    die();
}


include_once './view/localization.tpl';
