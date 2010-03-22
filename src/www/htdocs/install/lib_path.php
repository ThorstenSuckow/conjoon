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
 * Tests the path where the libs folder was moved to.
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */

$LIBPATH = array();
$LIBPATH['not_existing'] = false;
$LIBPATH['is_readable']  = true;
$LIBPATH['is_writable']  = true;
$LIBPATH['not_allowed']  = false;

$_SESSION['lib_path_failed'] = false;

$_SESSION['lib_path'] = isset($_SESSION['lib_path'])
                        ? $_SESSION['lib_path']
                        : (isset($_SESSION['installation_info']['lib_path'])
                           ? $_SESSION['installation_info']['lib_path']
                           : ''
                        );

$_SESSION['add_include_path'] = isset($_SESSION['add_include_path'])
                        ? $_SESSION['add_include_path']
                        : (isset($_SESSION['installation_info']['add_include_path'])
                           ? $_SESSION['installation_info']['add_include_path']
                           : true
                        );

if (isset($_POST['lib_path_post'])) {

    $libPath = str_replace("\\", "/", trim((string)$_POST['lib_path']));

    if ($libPath == "") {
        $libPath = "../";
    }

    $_SESSION['lib_path'] = $libPath;
    $libPath              = @realpath($libPath);

    if ($libPath === false) {
        $LIBPATH['not_existing'] = true;
        $_SESSION['lib_path_failed'] = true;
    } else {
        $libPath = str_replace("\\", "/", $libPath);
        $_SESSION['lib_path'] = $libPath;
        $LIBPATH['is_readable'] = @is_readable($libPath);
        if (!$LIBPATH['is_readable']) {
            $_SESSION['lib_path_failed'] = true;
        }
    }

    // /home/user/apppath
    $cPath  = strtolower(rtrim($libPath, '/')) .'/';
    // /home/user/apppath/install/login
    $cPath2 = strtolower(rtrim(str_replace("\\", "/", getcwd()), '/')) . '/';

    if (strpos($cPath, $cPath2) === 0) {
        $LIBPATH['not_allowed']      = true;
        $_SESSION['lib_path_failed'] = true;
    }

    if (!$_SESSION['lib_path_failed']) {
        $_SESSION['add_include_path'] = false;

        if (isset($_POST['add_include_path']) && $_POST['add_include_path'] == 1) {
            $_SESSION['add_include_path'] = true;
        }

        header("Location: ./?action=lib_path_success");
        die();
    }


}

include_once './view/lib_path.tpl';