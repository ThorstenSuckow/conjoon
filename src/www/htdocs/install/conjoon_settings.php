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
        'upload.max_size' => array(),
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
    $sizeComp = array(
        conjoon_megaByteToByte($_SESSION['max_allowed_packet']),
        conjoon_megaByteToByte(ini_get('upload_max_filesize')),
        conjoon_megaByteToByte(ini_get('post_max_size'))
    );

    if (!empty($CN_SETTINGS['upload.max_size'])) {
        $sizeComp[] = $CN_SETTINGS['upload.max_size'];
    }
    $CN_SETTINGS['upload.max_size'] =  min($sizeComp);

}

if (isset($_POST['cn_settings_post']) && $_POST['cn_settings_post'] == "1") {

    $_SESSION['cn_settings_failed'] = false;
    $_SESSION['files'] = $fileSetup;

    $_SESSION['files']['upload.max_size'] = (int) $_POST['upload_max_size'];

    if(!$_POST['storage_filesystem_enabled']) {
        $_SESSION['files']['storage.filesystem.enabled'] = false;
        $_SESSION['files']['storage.filesystem.dir'] =
            $_SESSION['setup_ini']['files']['storage.filesystem.dir'];

        $_SESSION['cn_settings_failed'] = false;
    } else {

        $_SESSION['files']['storage.filesystem.enabled'] = true;

        $tryCacheDir = $_POST['storage_filesystem_dir'];

        if (trim($tryCacheDir) == "") {
            $tryCacheDir = conjoon_cacheSetup_getCacheDir(
                'storage.filesystem.dir', 'files');
        }

        if (strpos($tryCacheDir, '/') !== 0 && strpos($tryCacheDir, ':') !== 1) {
            $tryCacheDir = rtrim($_SESSION['app_path'], '/')
                .'/'
                . rtrim($_SESSION['setup_ini']['app_path']['folder'], '/')
                . '/'
                . $tryCacheDir;
        }

        $dirCheck = conjoon_mkdir($tryCacheDir, true);

        $_SESSION['files']['storage.filesystem.dir'] = $tryCacheDir;

        if ($dirCheck === false) {
            $_SESSION['files']['storage.filesystem.dir.install_failed'] = true;
            $_SESSION['cn_settings_failed'] = true;
        }
    }



    if (!$_SESSION['cn_settings_failed']) {
        header("Location: ./?action=conjoon_settings_success");
        die();
    }

    $CN_SETTINGS =& $_SESSION['files'];

}

include_once './view/conjoon_settings.tpl';
