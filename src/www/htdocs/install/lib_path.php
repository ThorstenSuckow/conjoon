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
 * Tests the path where the libs folder was moved to.
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */

/**
 * check if user is authorized to load script
 */
include('./scripts/check_auth.php');

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