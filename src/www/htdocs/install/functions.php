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
 * Utility methods for the conjoon installation process.
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */

/**
 * Reads out the max allowed packets setting for the database type.
 * Returns "0" if the value for this db setting could not be retrieved.
 *
 * @param string $dbAdapter
 * @param array  $connectionInfo An array with the connection info to conenct
 * to the database and read out the value. Possible keys are:
 *  host
 *  user
 *  password
 *  database
 *  port
 * This function relies on the PDO extension of PHP.
 *
 * @return float
 */
function conjoon_getMaxAllowedPacket($dbAdapter, Array $connectionInfo)
{
    $dbType = strtolower(str_replace("pdo_", "", $dbAdapter));

    $bytes = 0;

    switch ($dbType) {
        case 'mysql':
            $db = new PDO(
                $dbType . ":" .
                "host=" . $connectionInfo['host'] . ";".
                "dbname=".$connectionInfo['database'].";".
                "port=".$connectionInfo['port'],
                $connectionInfo['user'], $connectionInfo['password']
            );

            $sql = "SHOW VARIABLES WHERE Variable_name = 'max_allowed_packet'";
            foreach ($db->query($sql) as $row) {
                $bytes = $row['Value'];
            }
            $db = null;
        break;

        default:
            die("No support for adapter \"$dbType\"");
        break;
    }

    return $bytes;
}

/**
 * Fills the db (specified in $config['database']) with the sql from
 * as found in the file specified via $path.
 *
 * @param string $sql
 * @param string $path
 * @param array $config
 *
 */
function conjoon_createTables($path, $dbAdapter, Array $config)
{
    $path = str_replace("\\", "/", $path);

    $dbType = strtolower(str_replace("pdo_", "", $dbAdapter));

    $bytes = 0;

    switch ($dbType) {
        case 'mysql':
            $db = new PDO(
                $dbType . ":" .
                "host=" . $config['host'] . ";".
                "dbname=".$config['database'].";".
                "port=".$config['port'],
                $config['user'], $config['password']
            );

            $sql = "SOURCE '" . $path ."'";
            $db->query(file_get_contents($path));
            $db = null;
        break;

        default:
            die("No support for adapter \"$dbType\"");
        break;
    }
}

/**
 * Creates an admin user, only if the user table is empty.
 *
 * @param string $user
 * @param string $password
 * @param array $config
 *
 */
function conjoon_createAdmin($dbAdapter, $userData, Array $config)
{
    $dbType = strtolower(str_replace("pdo_", "", $dbAdapter));

    $bytes = 0;

    switch ($dbType) {
        case 'mysql':
            $db = new PDO(
                $dbType . ":" .
                "host=" . $config['host'] . ";".
                "dbname=".$config['database'].";".
                "port=".$config['port'],
                $config['user'], $config['password']
            );

            $sql = "SELECT COUNT(id) as count_id FROM users WHERE is_root = 1";
            $count = 0;
            foreach ($db->query($sql) as $row) {
                $count = $row['count_id'];
            }

            if ($count == 0) {
                $sql = "INSERT INTO users (
                    firstname,
                    lastname,
                    email_address,
                    user_name,
                    password,
                    is_root
                ) VALUES (
                    ?,?,?,?,?,?
                )";
                $sth = $db->prepare($sql);
                $sth->execute(array(
                    $userData['firstname'],
                    $userData['lastname'],
                    $userData['email_address'],
                    $userData['user'],
                    md5($userData['password']),
                    1
                ));
            }

            $db = null;

        break;

        default:
            die("No support for adapter \"$dbType\"");
        break;
    }
}

/**
 * Removes a directory recursively.
 *
 * @param string $path
 */
function conjoon_rmdir($path)
{
    $path = rtrim(str_replace("\\", "/", $path), '/').'/';

    if (!file_exists($path)) {
        return;
    }

    $handle = opendir($path);

    for (;false !== ($file = readdir($handle));) {
        if($file != "." and $file != ".." ) {
            $fullpath= $path.$file;

            if(is_dir($fullpath)) {
                conjoon_rmdir($fullpath);
                rmdir($fullpath);
            } else {
                unlink($fullpath);
            }
        }
    }
    closedir($handle);
}

/**
 * Tries to create a directory. Will try to create each directory level.
 * if the second parameter is set to true, the created directory will be removed
 * afterwards.
 * The directory has to be specified absolutely.
 *
 */
function conjoon_mkdir($dir, $remove = false)
{
    if (strpos($dir, '/') !== 0 && strpos($dir, ':') !== 1) {
        return false;
    }

    $dir = str_replace("\\", "/", $dir);

    $parts  = explode('/', $dir);
    $tmpDir = realpath($parts[0]);

    if ($tmpDir === false) {
        return false;
    }

    $existing   = array();
    $removeDirs = array();
    for ($i = 1, $len = count($parts); $i < $len+1; $i++) {

        if (!file_exists($tmpDir)) {

            $removeDirs[] = $tmpDir;

            $res = @mkdir($tmpDir);
            if ($res === false) {
                conjoon_rmdir($tmpDir);
                return false;
            }
        } else {
            $existing[$tmpDir] = true;
        }

        if (!isset($parts[$i])) {
            break;
        }
        $tmpDir .= '/' . $parts[$i];
    }

    $isCool = conjoon_validateDir($dir);

    if ($remove === true) {
        for ($i = count($removeDirs) -1; $i >= 0; $i--) {
            rmdir($removeDirs[$i]);
        }
    }

    return $isCool;
}

/**
 * Returns true if the specified directory is existing and both read/writable,
 * otherwise false.
 *
 */
function conjoon_validateDir($dir)
{
    $dir = @realpath($dir);

    if ($dir === false) {
        return false;
    }

    $dir = str_replace("\\", "/", $dir);
    $is_readable = @is_readable($dir);
    $is_writable = @is_writable($dir);
    if (!$is_readable || !$is_writable) {
        return false;
    }

    return true;
}


/**
 * Copies a directory recursively.
 *
 *
 */
function conjoon_copy($source, $target)
{
    $source = str_replace("\\", "/", $source);
    $target = str_replace("\\", "/", $target);

    if (is_dir($source)) {
        @mkdir($target);

        $d = dir($source);

        while (($entry = $d->read()) !== false) {
            if ($entry == '.' || $entry == '..') {
                continue;
            }

            $_entry = $source . '/' . $entry;
            if (is_dir($_entry)) {
                conjoon_copy($_entry, $target . '/' . $entry);
                continue;
            }
            copy($_entry, $target . '/' . $entry);
        }

        $d->close();
    }else {
        copy($source, $target);
    }
}


/**
 * Takes a camelized string as the argument and returns it underscored, all
 * lowercased.
 * For example, passing the string "underScore" to this function will return
 * the string "under_score".
 *
 * @param {String} $value
 *
 * @return
 */
function conjoon_underscoreString($value)
{
    return strtolower(preg_replace('/([a-z])([A-Z])/', "$1_$2", $value));
}