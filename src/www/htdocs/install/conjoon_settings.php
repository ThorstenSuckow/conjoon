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
 * conjoon specific settings.
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */

/**
 * check if user is authorized to load script
 */
include('./scripts/check_auth.php');


$CN_SETTINGS = array();

$fileSetup =& $_SESSION['setup_ini']['files'];

if (isset($_SESSION['files'])) {
    $CN_SETTINGS = $_SESSION['files'];
} else {

    $getMyKeys = array(
        'storage.filesystem.dir' => array(),
        'storage.filesystem.enabled' => array()
    );

    foreach ($getMyKeys as $heresYourKey => $heresYourValue) {
        // gather default value!
        $CN_SETTINGS[$heresYourKey] = conjoon_cacheSetup_getConfigurationDefaultValue(
            $heresYourKey, 'files', $getMyKeys[$heresYourKey]
        );

        // adjust the value if necessary
        if (strpos($heresYourKey, '.dir') !== false) {
            $CN_SETTINGS[$heresYourKey] = conjoon_cacheSetup_assembleDir(
                $heresYourKey, 'files', $CN_SETTINGS[$heresYourKey]);
        }
    }

    // fetch max upload default values in bytes
    $CN_SETTINGS['upload.max_size'] =  min(array(
        conjoon_megaByteToByte($_SESSION['max_allowed_packet']),
        conjoon_megaByteToByte(ini_get('upload_max_filesize')),
        conjoon_megaByteToByte(ini_get('post_max_size'))
    ));

}

if (isset($_POST['cn_settings_post']) && $_POST['cn_settings_post'] == "1") {

    $_SESSION['cn_settings_failed'] = false;
    $_SESSION['files'] = $fileSetupSetup;

    // place processing logic!
    throw new Exception("Not yet implemented");

    if (!$_SESSION['cn_settings_failed']) {
        header("Location: ./?action=cn_settings_success");
        die();
    }

    $CN_SETTINGS =& $_SESSION['files'];

}

include_once './view/conjoon_settings.tpl';
