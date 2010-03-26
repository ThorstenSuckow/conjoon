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
 * Checks if the environment matches the conjoon pre-requisites.
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */

 $CHECK = array();

 // check if parent_dir is writable
 $pathinfo = pathinfo(__FILE__);
 $pathinfo = realpath($pathinfo['dirname'] . "/../");
 $CHECK['parent_dir']          = str_replace("\\", "/", $pathinfo);
 $CHECK['parent_dir_writable'] = @is_writable($CHECK['parent_dir']);

 if ($CHECK['parent_dir_writable']) {
    $dirName  = 'conjoon_install_test';
    $i = 0;
    while (@file_exists('../' . $dirName)) {
        $i++;
        $dirName = $dirName . $i;
    }
    $fileName = $dirName . ".txt";

    $makeDir = @mkdir('../' . $dirName);

    if ($makeDir) {
        if (@file_put_contents(
            '../' . $dirName .'/' . $fileName,
            "File created during installation of conjoon on "
            . date('Y-m-d H:i:s', time()). " for testing purposes.\n"
            . "If this file still exists after installation, you can "
            . "safely remove it along with its parent directory."
            ) === false) {
            $CHECK['parent_dir_writable'] = false;
        } else {
            @unlink('../' . $dirName .'/' . $fileName);
            @rmdir('../' . $dirName);
        }

        if ($CHECK['parent_dir_writable']) {
            // if makeDir succeeded, we will check if we can move files
            $moveA = './_moveTest/1';
            $moveB = './_moveTest/2';

            $moveResult = @rename($moveB, $moveA . '/2');

            if ($moveResult) {
                @rename($moveA . '/2', $moveB);
                @file_put_contents($moveB .'/check.txt', "Moved successfully");
            }

            $CHECK['parent_dir_writable'] = $moveResult;
        }

    } else {
        $CHECK['parent_dir_writable'] = false;
    }

 }

 $fileownerCurrent = fileowner(__FILE__);

 // check here for safemode
 $CHECK['safe_mode_enabled'] = false;
 $CHECK['safe_mode_failure'] = false;
 if (ini_get('safe_mode')) {
    $CHECK['safe_mode_enabled'] = true;

    // create temp dir:
    $tmpDir = $_SESSION['setup_ini']['check']['safe_mode.tmp_dir'];
    $CHECK['safe_mode_tmp_dir'] = $tmpDir;
    if (file_exists('../' . $tmpDir) && (fileowner('../' . $tmpDir) == $fileownerCurrent)) {
        @rmdir('../' . $tmpDir);
    } else if (!file_exists('../' . $tmpDir)) {
        @mkdir('../' . $tmpDir);
    }

    if (file_exists('../' . $tmpDir)) {
        $CHECK['safe_mode_failure'] = fileowner('../' . $tmpDir) != $fileownerCurrent;
    }
 }


 // check PHP version
 $php_version_match   = version_compare(PHP_VERSION, '5.2.5' , '>=');
 $current_php_version = PHP_VERSION;

 $CHECK['php_version_required'] = '5.2.5';
 $CHECK['php_version_match']    = $php_version_match;
 $CHECK['current_php_version']  = $current_php_version;


 // check Apache available
 $neededApacheVersion  = '2.2.8';
 $apache_version_match = false;

 if (strtolower($_SERVER['SERVER_SOFTWARE']) == "apache") {
    $apache_available = true;
    $apache_version   = "(???)";
 } else if (preg_match('|Apache\/(\d+)\.(\d+)\.(\d+)|', $_SERVER['SERVER_SOFTWARE'], $apver)) {
     $apache_available     = true;
     $apache_version       = "${apver[1]}.${apver[2]}.${apver[3]}";
     $apache_version_match = version_compare($apache_version, $neededApacheVersion, '>=');

 } else {
     $apache_available = false;
     $apache_version   = 0;
 }

 $CHECK['apache_version_required'] = $neededApacheVersion;
 $CHECK['apache_available']        = $apache_available;
 $CHECK['apache_version']          = $apache_version;
 $CHECK['apache_version_match']    = $apache_version_match;

 // check mod_rewrite available
 $CHECK['mod_rewrite_available'] = false;
 $CHECK['apache_detected']       = true;
 if (function_exists('apache_get_modules')) {
     if (in_array("mod_rewrite", apache_get_modules())) {
         $CHECK['mod_rewrite_available'] = true;
     }
 }else {
    $CHECK['apache_detected'] = false;
 }

 // check for magic_quotes_gpc
 $magic_quotes_gpc = ini_get('magic_quotes_gpc');
 $CHECK['magic_quotes_gpc'] = strtolower($magic_quotes_gpc) != "on"
                              && $magic_quotes_gpc == 1;

 // check for register_globals
 // on == On or 1
 $register_globals = ini_get('register_globals');
 $CHECK['register_globals'] = $register_globals != 1
                              && strtolower($register_globals) == "on";

 // check for pdo
 $pdo_enabled = extension_loaded('pdo');
 $CHECK['pdo_extension_loaded']    = $pdo_enabled === true;
 $_SESSION['pdo_extension_loaded'] = $CHECK['pdo_extension_loaded'];

 // check for pdo_mysql IF, and only if pdo extension is loaded
 $pdoMysqlDriverAvailable      = false;
 $CHECK['pdo_mysql_available'] = false;
 if ($pdo_enabled === true) {
     $availablePdoDrivers = PDO::getAvailableDrivers();
     $pdoMysqlDriverAvailable = in_array('mysql', $availablePdoDrivers);
     $CHECK['pdo_mysql_available']    = $pdoMysqlDriverAvailable === true;
     $_SESSION['pdo_mysql_available'] = $CHECK['pdo_mysql_available'];
 }

 // check if fsockopen is enabled
 $server  = $_SESSION['setup_ini']['check']['fsockopen.host'];
 $port    = $_SESSION['setup_ini']['check']['fsockopen.port'];
 $timeout = $_SESSION['setup_ini']['check']['fsockopen.timeout'];

 $errno = "";
 $errstr = "";

 @fsockopen($server, $port, $errno, $errstr, $timeout);
 $CHECK['fsockopen_available'] = $errno != 13;

 $CHECK['simplexml'] = true;
 // check if simplexml is available
 if (!function_exists('simplexml_load_file')) {
    $CHECK['simplexml'] = false;
 }


 // if any warning or error was generated, save this into the session so the user
 // is informed

 if (!$CHECK['pdo_mysql_available'] ||
     !$CHECK['pdo_extension_loaded'] ||
     !$CHECK['fsockopen_available'] ||
     !$CHECK['php_version_match'] ||
     !$CHECK['apache_available'] ||
     !$CHECK['apache_version_match'] ||
     !$CHECK['mod_rewrite_available'] ||
     !$CHECK['parent_dir_writable'] ||
     $CHECK['magic_quotes_gpc'] ||
     $CHECK['register_globals'] ||
     ($CHECK['safe_mode_enabled'] && $CHECK['safe_mode_failure']) ||
     !$CHECK['simplexml']
     ) {
     $_SESSION['check_failed'] = true;
 }


 include_once './view/check.tpl';