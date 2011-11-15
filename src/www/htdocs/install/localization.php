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
 * Asks for localization information.
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */

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
