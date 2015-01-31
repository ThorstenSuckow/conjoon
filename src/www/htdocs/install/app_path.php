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
 * Tests the path where the application folder was moved to.
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */

/**
 * check if user is authorized to load script
 */
include('./scripts/check_auth.php');


$APPPATH = array();
$APPPATH['not_existing'] = false;
$APPPATH['is_readable']  = true;
$APPPATH['is_writable']  = true;
$APPPATH['not_allowed']  = false;

$_SESSION['app_path_failed'] = false;

$_SESSION['app_path'] = isset($_SESSION['app_path'])
                        ? $_SESSION['app_path']
                        : (isset($_SESSION['installation_info']['app_path'])
                           ? $_SESSION['installation_info']['app_path']
                           : ''
                        );

if (isset($_POST['app_path_post'])) {

    $appPath = str_replace("\\", "/", trim((string)$_POST['app_path']));

    if ($appPath == "") {
        $appPath = "../";
    }

    $_SESSION['app_path'] = $appPath;
    $appPath              = @realpath($appPath);

    if ($appPath === false) {
        $APPPATH['not_existing'] = true;
        $_SESSION['app_path_failed'] = true;
    } else {
        $appPath = str_replace("\\", "/", $appPath);
        $_SESSION['app_path'] = $appPath;
        $APPPATH['is_readable'] = @is_readable($appPath);
        $APPPATH['is_writable'] = @is_writable($appPath);
        if (!$APPPATH['is_readable'] || !$APPPATH['is_writable']) {
            $_SESSION['app_path_failed'] = true;
        }
    }

    // /home/user/apppath
    $cPath  = strtolower(rtrim($appPath, '/')) .'/';
    // /home/user/apppath/install/login
    $cPath2 = strtolower(rtrim(str_replace("\\", "/", getcwd()), '/')) . '/';

    if (strpos($cPath, $cPath2) === 0) {
        $APPPATH['not_allowed']      = true;
        $_SESSION['app_path_failed'] = true;
    }

    if (!$_SESSION['app_path_failed']) {
        header("Location: ./?action=app_path_success");
        die();
    }


}

include_once './view/app_path.tpl';