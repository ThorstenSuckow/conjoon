<?php
/**
 * conjoon
 * (c) 2002-2009 siteartwork.de/conjoon.org
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
 * Asks for database connection information.
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */

$DATABASE = array(
    'missing'                   => array(),
    'max_allowed_packet_failed' => false,
    'max_allowed_packet'        => 0,
    'db_table_prefix_failed'    => false
);

$DATABASE['pdo_extension_loaded'] = true;
$DATABASE['pdo_mysql_available']  = true;

$_SESSION['db_failed'] = false;

// check here for database information from the previous setup
$tmpDb = array();

if (isset($_SESSION['installation_info'])) {
    $tmpDb = $_SESSION['installation_info'];
}

$_SESSION['db_host'] = isset($_SESSION['db_host'])
                       ? $_SESSION['db_host']
                       : (isset($tmpDb['db_host'])
                       ? $tmpDb['db_host']
                       : null);
$_SESSION['db_adapter'] = isset($_SESSION['db_adapter'])
                       ? $_SESSION['db_adapter']
                       : (isset($tmpDb['db_adapter'])
                       ? $tmpDb['db_adapter']
                       : null);
$_SESSION['db'] = isset($_SESSION['db'])
                       ? $_SESSION['db']
                       : (isset($tmpDb['db'])
                       ? $tmpDb['db']
                       : null);
$_SESSION['db_port'] = isset($_SESSION['db_port'])
                       ? $_SESSION['db_port']
                       : (isset($tmpDb['db_port'])
                       ? $tmpDb['db_port']
                       : null);
$_SESSION['db_user'] = isset($_SESSION['db_user'])
                       ? $_SESSION['db_user']
                       : (isset($tmpDb['db_user'])
                       ? $tmpDb['db_user']
                       : null);
$_SESSION['db_table_prefix'] = strtolower(isset($_SESSION['db_table_prefix'])
                       ? $_SESSION['db_table_prefix']
                       : (isset($tmpDb['db_table_prefix'])
                       ? $tmpDb['db_table_prefix']
                       : null));
$_SESSION['max_allowed_packet'] = isset($_SESSION['max_allowed_packet'])
                       ? $_SESSION['max_allowed_packet']
                       : (isset($tmpDb['max_allowed_packet'])
                       ? $tmpDb['max_allowed_packet']
                       : null);
$_SESSION['db_password'] = isset($_SESSION['db_password'])
                       ? $_SESSION['db_password']
                       : null;


// get the supported adapters out of setup.ini
$adapterString = $_SESSION['setup_ini']['database']['adapters'];
$parts = explode(';', $adapterString);

$DATABASE['adapters'] = array();

for ($i = 0, $len = count($parts); $i < $len; $i++) {
    $ps = explode(':', $parts[$i]);
    $DATABASE['adapters'][] = array(
        'option' => $ps[1],
        'value'  => $ps[0]
    );
}

// form posted! Check db conection settings!
if (isset($_POST['database_check'])) {

    $adapter          = trim(stripslashes((string)$_POST['db_adapter']));
    $host             = trim(stripslashes((string)$_POST['db_host']));
    $db               = trim(stripslashes((string)$_POST['db']));
    $port             = trim(stripslashes((string)$_POST['db_port']));
    $user             = trim(stripslashes((string)$_POST['db_user']));
    $prefix           = strtolower(trim(stripslashes((string)$_POST['db_table_prefix'])));
    $password         = trim(stripslashes((string)$_POST['db_password']));
    $maxAllowedPacket = trim(stripslashes((string)(float)$_POST['max_allowed_packet']));

    // check if all values are set!
    if ($adapter == "") {
        $DATABASE['missing'][] = 'db_adapter';
        $_SESSION['db_failed'] = true;
    }
    if ($host == "") {
        $DATABASE['missing'][] = 'db_host';
        $_SESSION['db_failed'] = true;
    }
    if ($db == "") {
        $DATABASE['missing'][] = 'db';
        $_SESSION['db_failed'] = true;
    }
    if ($port == "") {
        $DATABASE['missing'][] = 'db_port';
        $_SESSION['db_failed'] = true;
    }
    if ($user == "") {
        $DATABASE['missing'][] = 'db_user';
        $_SESSION['db_failed'] = true;
    }

    if ($prefix != "") {
        if (!preg_match("/^[a-zA-Z_0-9]+$/", $prefix)) {
            $_SESSION['db_failed']              = true;
            $DATABASE['db_table_prefix_failed'] = true;
        }
    }

    $_SESSION['db_adapter']      = addslashes($adapter);
    $_SESSION['db_host']         = addslashes($host);
    $_SESSION['db']              = addslashes($db);
    $_SESSION['db_port']         = addslashes($port);
    $_SESSION['db_user']         = addslashes($user);
    $_SESSION['db_table_prefix'] = addslashes($prefix);
    $_SESSION['db_password']     = addslashes($password);

    $_SESSION['max_allowed_packet'] = $maxAllowedPacket;

    // all values provided - check the connection!
    if (empty($DATABASE['missing'])) {
        $DATABASE['pdo_extension_loaded'] = $_SESSION['pdo_extension_loaded'];
        $DATABASE['pdo_mysql_available']  = $_SESSION['pdo_mysql_available'];
    }

    // extension and adapter available? check the connection
    if (empty($DATABASE['missing']) && $DATABASE['pdo_extension_loaded'] && $DATABASE['pdo_mysql_available']) {
        try {
            $dbconn = new PDO("mysql:host=$host;dbname=$db;port=$port", $user, $password);
        } catch (Exception $e) {
            $DATABASE['connection_error'] = $e->getMessage();
            $_SESSION['db_failed'] = true;
        }
    } else {
        $_SESSION['db_failed'] = true;
    }

    // read out max_allowed_packet only if everything works at this point
    if (!$_SESSION['db_failed']) {
        $map = conjoon_getMaxAllowedPacket($adapter, array(
            'host'     => $host,
            'port'     => $port,
            'user'     => $user,
            'password' => $password,
            'database' => $db
        ));

        if ($maxAllowedPacket > $map) {
            $DATABASE['max_allowed_packet'] = $map;
            $_SESSION['db_failed'] = true;
            $_SESSION['max_allowed_packet'] = "";
            $DATABASE['max_allowed_packet_failed'] = true;
        } else if ($maxAllowedPacket < $map && $maxAllowedPacket != 0) {
            $_SESSION['max_allowed_packet'] = $maxAllowedPacket;
        } else if ($maxAllowedPacket  == 0) {
            $_SESSION['max_allowed_packet'] = $map;
        }
    }

    if (!$_SESSION['db_failed']) {
        header("Location: ./?action=dbcheck_success");
        die();
    }

}

include_once './view/database.tpl';