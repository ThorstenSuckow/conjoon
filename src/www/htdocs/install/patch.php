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
 * Lists available patches for the current version when updating
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */

/**
 * check if user is authorized to load script
 */
include('./scripts/check_auth.php');

$currentVersion  = $_SESSION['current_version'];
$firstVersion    = isset($_SESSION['installation_info']['first_version'])
                   ? $_SESSION['installation_info']['first_version']
                   : -1;
$previousVersion = isset($_SESSION['installation_info']['previous_version'])
                   ? $_SESSION['installation_info']['previous_version']
                   : -1;

if ($previousVersion === -1 || $firstVersion === -1) {
    header("Location: ./?action=patch_success");
    die();
}


$appliedPatches = isset($_SESSION['installation_info']['applied_patches'])
                  ? $_SESSION['installation_info']['applied_patches']
                  : array();

$ignoredPatches = isset($_SESSION['installation_info']['ignored_patches'])
                  ? $_SESSION['installation_info']['ignored_patches']
                  : array();

$availablePatches = array();

 if ($handle = opendir('./patches')) {


    while (false !== ($file = readdir($handle))) {

        if (!is_dir('./patches/'.$file) || strpos($file, '.') === 0) {
            continue;
        }

        $patchVersion = trim($file);

        if (in_array($patchVersion, $appliedPatches)
            || version_compare($patchVersion, $firstVersion, '<=')) {
            continue;
        }

        if (version_compare($patchVersion, $previousVersion, '>=')) {
            $availablePatches[] = $patchVersion;
        }
    }

    closedir($handle);

    function ____cmp($a, $b) {
        return version_compare($a, $b, '>=');
    }

    foreach ($ignoredPatches as $ignoredPatch) {
        if (version_compare($ignoredPatch, $previousVersion, '<=')) {
            $availablePatches[] = $ignoredPatch;
        }
    }

    uasort($availablePatches, '____cmp');
}


// WE HAVE ALL THE INFORMATION - CHECK FOR POST NOW!
if (isset($_POST['patch_check'])) {

    $_SESSION['applied_patches'] =
        isset($_SESSION['installation_info']['applied_patches'])
        ? $_SESSION['installation_info']['applied_patches']
        : array();

    $_SESSION['ignored_patches'] =
        isset($_SESSION['installation_info']['ignored_patches'])
        ? $_SESSION['installation_info']['ignored_patches']
        : array();

    $_SESSION['patches']   = array();
    $_SESSION['patchdata'] = array();

    $patchData = !empty($_POST['patchdata'])
                 ? $_POST['patchdata']
                 : array();

    $postedPatches = !empty($_POST['patch'])
                     ? $_POST['patch']
                     : array();

    foreach ($patchData as $postPatchVersion => $data) {
        $_SESSION['patchdata'][str_replace('_', '.', $postPatchVersion)] =
            $data;
    }

    $tign = array();
    foreach ($postedPatches as $postPatchVersion => $postPatch) {

        $postPatchVersion = str_replace('_', '.', $postPatchVersion);
        $postPatch        = (bool)$postPatch;

        if ($postPatch) {
            if (in_array($postPatchVersion, $_SESSION['ignored_patches'])) {
                $tign[] = $postPatchVersion;
            }
            $_SESSION['applied_patches'][] = $postPatchVersion;
        } else {
            if (!in_array($postPatchVersion, $_SESSION['ignored_patches'])) {
                $_SESSION['ignored_patches'][] = $postPatchVersion;
            }
        }

        $_SESSION['patches'][$postPatchVersion] = $postPatch;
    }

    $_SESSION['ignored_patches'] = array_diff($_SESSION['ignored_patches'], $tign);

    header("Location: ./?action=patch_success");
    die();
}



$PATCH_NOTES = array();

foreach ($availablePatches as $patchVersion) {

    if (file_exists("./patches/$patchVersion/notes.php")) {
        include_once "./patches/$patchVersion/notes.php";
    }
}

include_once './view/patch.tpl';

