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
 * Utility methods for the conjoon installation process.
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */

/**
 * Helps to determine the path of a config option by first looking into
 * installation info, then falling back to the setup-ini specified
 * values. Returns either the current configured value or the value
 * prepended with the application folder.
 *
 * @param $key
 * @param $section
 * @param null $value
 *
 * @return null|string
 */
function conjoon_cacheSetup_getCacheDir($key, $section, $value = null)
{
    if (isset($_SESSION['installation_info'][$section . '.' . $key])) {
        return $_SESSION['installation_info'][$section . '.' . $key];
    }

    $cacheSetup =& $_SESSION['setup_ini'][$section];

    if (strpos($key, 'cache_dir') !== false ||
        strpos($key, '.dir') !== false) {
        return rtrim($_SESSION['app_path'], '/')
        . '/'
        . rtrim($_SESSION['setup_ini']['app_path']['folder'], '/')
        . '/'
        . $cacheSetup[$key];
    } else {
        return $value;
    }
}

/**
 * Helper function for converting bytes to megabyte.
 *
 * @param integer $filesize
 *
 * @return string
 */
function conjoon_bytesToMegaByte($filesize) {
    $filesize = (int) $filesize;

    if ($filesize < 1024 * 1024) {
        return (string) $filesize;
    }
    return round((int) $filesize / (1024 * 1024)) . 'M';
}

/**
 * Helper function for converting Megabyte values to bytes
 *
 * @param integer $filesize
 *
 * @return string
 */
function conjoon_megaByteToByte($filesize) {

    // allowed units as specified by php
    $units = array('K' => 1, 'M' => 2, 'G' => 3);
    $unit  = strtoupper(trim(substr($filesize, -1)));

    if (!in_array($unit, array_keys($units))) {
        return $filesize;
    }

    $size = trim(substr($filesize, 0, strlen($filesize) - 1));

    if (!intval($size) == $size) {
        return $filesize;
    }

    return $size * pow(1024, $units[$unit]);
}


/**
 * Helps to retrieve a default value for a specific setting.
 * The default value is looked up in the config_ini, then installation_info, then
 * setup_ini.
 *
 * @param string $key
 * @param string $section
 * @param array $config
 *
 * @return string
 */
function conjoon_cacheSetup_getConfigurationDefaultValue($key, $section, $config = array())
{
    $allowEmpty = isset($config['allowEmpty']) ? $config['allowEmpty'] : true;

    // check first of value is available in config
    if (isset($_SESSION['config_info']) &&
        is_array($_SESSION['config_info']) &&
        isset($_SESSION['config_info'][$section]) &&
        array_key_exists($key, $_SESSION['config_info'][$section])) {
        if ($allowEmpty || (!$allowEmpty && $_SESSION['config_info'][$section][$key])) {
            return $_SESSION['config_info'][$section][$key];
        }
    }

    // check if value is available in installation_info
    if (array_key_exists($section . '.' . $key, $_SESSION['installation_info'])
        && !empty($_SESSION['installation_info']['application.' . $key])) {
        if ($allowEmpty || (!$allowEmpty && $_SESSION['installation_info']['application.' . $key])) {
            return $_SESSION['installation_info']['application.' . $key];
        }
    }

    // check if value is available in setup ini
    if (isset($_SESSION['setup_ini'][$section]) &&
        array_key_exists($key, $_SESSION['setup_ini'][$section])) {
        return $_SESSION['setup_ini'][$section][$key];
    }

    return null;
}



/**
 * Helper function to assign setup default values for directory path values.
 *
 * @param $key
 * @param null $value
 *
 * @return null|string
 */
function conjoon_cacheSetup_assembleDir($key, $section, $value = null)
{
    $cacheSetup =& $_SESSION['setup_ini'][$section];

    if (!$value) {
        $value = $cacheSetup[$key];
    }

    if ($value && strpos($value, '/') === 0) {
        return $value;
    }

    // fall back to default value from setup.ini, prepending app_path
    return rtrim($_SESSION['app_path'], '/')
        . '/'
        . rtrim($_SESSION['setup_ini']['app_path']['folder'], '/')
        . '/'
        . $value;

}

/**
 * Moves orm template files to production ready orm files and replaces
 * table prefix placeholders with its specific counterpart
 *
 * @param string $path path to the folder where orm files can be found
 * @param string $prefix prefix to use for tables found in orm files
 */
function conjoon_createOrmFiles($path, $prefix = "") {

    InstallLogger::stdout(InstallLogger::getInstance()->logMessage(
        "[ORM FILE CREATOR]: " .
        "Trying to move orm-template files to production ready files in \"" .
        $path .
        "\" using table-prefix \"".$prefix."\""
    ));

    if (is_dir($path)) {

        $d = dir($path);

        while (($entry = $d->read()) !== false) {
            if ($entry == '.' || $entry == '..') {
                continue;
            }

            $_entry = $path . '/' . $entry;
            if (is_dir($_entry)) {
                continue;
            }

            $needle = '.dcm.yml.template';

            // if filename ends with...
            if (substr($_entry, -strlen($needle)) === $needle) {
                $target = substr($_entry, 0, strlen($_entry) - strlen($needle)) .
                          '.dcm.yml';

                InstallLogger::stdout(InstallLogger::getInstance()->logMessage(
                    "[ORM FILE CREATOR]: " .
                        "Renaming \"" .
                        $_entry .
                        "\" to \"".$target."\""
                ));

                rename($_entry, $target);

                $ormFile = file_get_contents($target);
                //replace prefix
                $ormFile = str_replace('{DATABASE.TABLE.PREFIX}', $prefix, $ormFile);

                InstallLogger::stdout(InstallLogger::getInstance()->logMessage(
                    "[ORM FILE CREATOR]: " .
                        "replacing placeholder with prefix \"" .
                        $prefix .
                        "\" in \"".$target."\""
                ));

                file_put_contents($target, $ormFile);
            }
        }

        $d->close();
    } else {
        InstallLogger::stdout(InstallLogger::stdout(InstallLogger::getInstance()->logMessage(
            "[ORM FILE CREATOR]: " .
                "\"" . $path . "\" does not seem to be a directory"
        )));
    }

    InstallLogger::stdout(InstallLogger::getInstance()->logMessage(
        "[ORM FILE CREATOR]: Done."
    ));

}


/**
 * Searches through the HTML5 manifest files for {BASE_PATH} and replaces it with
 * the value found in $basePath.
 *
 * @param $applicationDir
 * @param $basePath
 */
function conjoon_updateHtml5ManifestFilesWithBasePath($applicationDir, $basePath)
{
    $dir = $applicationDir . '/manifest/';

    $in  = array(
        'flash',
        'html',
        'images',
        'javascript',
        'sounds',
        'stylesheets'
    );
    $out = array(array());

    foreach ($in as $val1) {
        $temp = $out;
        foreach ($temp as $val2) {
            $out[] = array_merge($val2, array($val1));
        }
    }

    $fileCombos = array();

    for ($i = 0, $len = count($out); $i < $len; $i++) {
        if (count($out[$i]) == 0) {
            continue;
        }

        sort($out[$i], SORT_STRING);

        $file = $dir . implode('.', $out[$i]) . '.list';

        InstallLogger::stdout(InstallLogger::getInstance()->logMessage(
            "[HTML5 MANIFEST]: "
            . "Trying to update \"" . $file ."\" "
            . "with basePath \"".$basePath."\""
        ));

        if (!file_exists($file)) {
            InstallLogger::stdout(InstallLogger::getInstance()->logMessage(
                "[HTML5 MANIFEST]: "
                . "File \"" . $file ."\" not found"
            ));

            continue;
        }

        $fileContent = file_get_contents($file);

        $fileContent = str_replace(
            '{BASE_PATH}',
            $basePath == "/"
                ? ""
                : '/' . trim($basePath, '/'),
            $fileContent
        );

        $ret = file_put_contents($file, $fileContent);

        if ($ret !== false) {
            InstallLogger::stdout(InstallLogger::getInstance()->logMessage(
                "[HTML5 MANIFEST]: "
                . "Updated \"" . $file ."\" "
            ));
        } else {
            InstallLogger::stdout(InstallLogger::getInstance()->logMessage(
                "[HTML5 MANIFEST]: "
                    . "Failed to update \"" . $file ."\" "
            ));
        }

        $fileContent = "";
    }



}


/**
 * Returns true if the key could be found in a previous version of
 * config.ini.php, otherwise false.
 *
 * @param string $key
 *
 * @return boolean
 */
function conjoon_configInfoExists($key)
{
    $keys   = explode('.', $key);
    $key    = array_shift($keys);
    $remKey = implode('.', $keys);

    if (isset($_SESSION['config_info'])
        && isset($_SESSION['config_info'][$key])
        && isset($_SESSION['config_info'][$key][$remKey])) {
        return true;
    }


    return false;
}

/**
 * Returns the value found withing the config.ini.php for this key, or false
 * if it is not existing.
 *
 * @param string $key
 *
 * @return mixed
 */
function conjoon_getConfigInfo($key)
{
    if (!conjoon_configInfoExists($key)) {
        return false;
    }

    $keys   = explode('.', $key);
    $key    = array_shift($keys);
    $remKey = implode('.', $keys);

    return $_SESSION['config_info'][$key][$remKey];
}

/**
 * Returns a special snippet to be used with cache settings
 *
 * @param $wording
 * @param $key
 *
 * @return string
 */
function conjoon_cacheEnabledSnippet($wording, $key)
{
    if (!conjoon_configInfoExists($key)) {
        return "";
    }
    return
        "<table class=\"configInfo\"><tr>"
        . "<td class=\"key\">"
        . $wording ." (config.ini.php):</td>"
        . "<td class=\"value\">" . (conjoon_getConfigInfo($key)
            ? "Yes"
            : "No")
        . "</td></tr></table><br />";
}

/**
 * Returns a special snippet to be used with cache settings
 *
 * @param $wording
 * @param $key
 *
 * @return string
 *
 * @deprecated use conjoon_configInfoSnippet instead
 */
function conjoon_cacheDirSnippet($wording, $key)
{
    return conjoon_configInfoSnippet($wording, $key);
}

/**
 * Returns a snippet to be used for showing config.ini.php settings.
 *
 * @param $wording
 * @param $key
 *
 * @return string
 *
 */
function conjoon_configInfoSnippet($wording, $key)
{
    if (!conjoon_configInfoExists($key)) {
        return "";
    }

    return
        "<table class=\"configInfo\"><tr>"
        . "<td class=\"key\">"
        . $wording ." (config.ini.php):</td>"
        . "<td class=\"value\">" . (conjoon_getConfigInfo($key)
                                    /**
                                     * @ticket CN-865
                                     */
                                    && conjoon_getConfigInfo($key) !== '{FILES.UPLOAD.MAX_SIZE}'
                                    ? conjoon_getConfigInfo($key)
                                    : '<code> - empty string - </code>')
        . "</td></tr></table><br />";
}

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

    $prefix = $config['prefix'];

    switch ($dbType) {
        case 'mysql':
            $db = new PDO(
                $dbType . ":" .
                "host=" . $config['host'] . ";".
                "dbname=".$config['database'].";".
                "port=".$config['port'],
                $config['user'], $config['password']
            );

            conjoon_createDatastructure($db, $path, $prefix);

        break;

        default:
            die("No support for adapter \"$dbType\"");
        break;
    }
}

/**
 * Parses the sql file and executes the given statements.
 *
 * @param Object $db The db adapter to use
 * @param String $path The path to the sql file to execute
 * @param String $prefix The prefix to use for the tables
 */
function conjoon_createDatastructure($db, $path, $prefix = "")
{
    // check here if we need to migrate data
    $migrate = false;
    $sql = "SELECT * FROM ".$prefix."groupware_email_folders_users";
    $result = $db->query($sql);
    if (!$result) {
        $migrate = true;
    }

    // twitter migrate
    $twittersql = "SELECT twitter_id FROM ".$prefix."service_twitter_accounts";
    $twitterresult = $db->query($twittersql);

    if (!$twitterresult) {
        $db->query("TRUNCATE TABLE `".$prefix."service_twitter_accounts`");
    }

    $sqlFile = file_get_contents($path);

    // remove sql comments
    $sqlFile = preg_replace("/^--.*?$/ims", "", $sqlFile);
    //replace prefix
    $sqlFile = str_replace('{DATABASE.TABLE.PREFIX}', $prefix, $sqlFile);

    $statements = explode(';', $sqlFile);

    for ($i = 0, $len = count($statements); $i < $len; $i++) {
        $statement = trim($statements[$i]);
        if ($statement == "") {
            continue;
        }

        InstallLogger::stdout(InstallLogger::getInstance()->logMessage("[STRUCTURE]: " . $statement));
        if (!$db->query($statement)) {
            $err = $db->errorInfo();
            InstallLogger::stdout(InstallLogger::getInstance()->logMessage(
                "[STRUCTURE:FAILED]: "
                    . (!empty($err) ? $err[2] : $statement)
            ));
        };

    }

    if ($migrate) {
        sleep(1);
        // migrate to groupware_email_folders_users
        // get the user ids associated with the user accounts
        $folderAccountsQuery = "SELECT ".$prefix."groupware_email_folders_accounts.*, "
                             ."".$prefix."groupware_email_accounts.user_id FROM "
                             ." ".$prefix."groupware_email_folders_accounts, "
                             ."".$prefix."groupware_email_accounts"
                             ." ".$prefix."groupware_email_accounts "
                             ." WHERE ".$prefix."groupware_email_accounts.id = "
                             ."".$prefix."groupware_email_folders_accounts.groupware_email_accounts_id";

        $folderAccountsResult = $db->query($folderAccountsQuery);

        if (!$folderAccountsResult) {
            // error or something - return
            return;
        }
        $folderAccountsResultCount = 0;
        $folderMapping = array();
        foreach ($folderAccountsResult as $row) {
            $folderAccountsResultCount++;
            $folderMapping[] = $row;
        }

        if ($folderAccountsResultCount == 0) {
            return;
        }

        for ( $i = 0, $len = count($folderMapping); $i < $len; $i++) {
            $query = "INSERT INTO ".$prefix."groupware_email_folders_users "
                   ."(groupware_email_folders_id, users_id, relationship) "
                   ."VALUES ("
                   ."".$folderMapping[$i]['groupware_email_folders_id'].","
                   ."".$folderMapping[$i]['user_id'].","
                   ."'owner'"
                   .")";

            $db->query($query);
        }
    }

}

/**
 * Parses the sql file and executes the given statements.
 *
 * @param String $path The path to the sql file to execute
 * @param Object $dbAdapter
 * @param Array $dbConfig
 */
function conjoon_insertFixtures($path, $dbAdapter, Array $dbConfig)
{
    $path = str_replace("\\", "/", $path);

    $dbType = strtolower(str_replace("pdo_", "", $dbAdapter));

    $prefix = $dbConfig['prefix'];

    switch ($dbType) {
        case 'mysql':
            $db = new PDO(
                $dbType . ":" .
                    "host=" . $dbConfig['host'] . ";".
                    "dbname=".$dbConfig['database'].";".
                    "port=".$dbConfig['port'],
                $dbConfig['user'], $dbConfig['password']
            );
            break;

        default:
            die("No support for adapter \"$dbType\"");
            break;
    }

    $sqlFile = file_get_contents($path);

    // remove sql comments
    $sqlFile = preg_replace("/^--.*?$/ims", "", $sqlFile);
    //replace prefix
    $sqlFile = str_replace('{DATABASE.TABLE.PREFIX}', $prefix, $sqlFile);

    $statements = explode(';', $sqlFile);

    for ($i = 0, $len = count($statements); $i < $len; $i++) {
        $statement = trim($statements[$i]);
        if ($statement == "") {
            continue;
        }
        InstallLogger::stdout(InstallLogger::getInstance()->logMessage("[FIXTURE]: " . $statement));
        if (!$db->query($statement)) {
            $err = $db->errorInfo();
            InstallLogger::stdout(InstallLogger::getInstance()->logMessage(
                "[FIXTURE:FAILED]: "
                . (!empty($err) ? $err[2] : $statement)
            ));
        };
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

    $prefix = $config['prefix'];

    // insert lowercase
    $userData['user'] = strtolower($userData['user']);

    switch ($dbType) {
        case 'mysql':
            $db = new PDO(
                $dbType . ":" .
                "host=" . $config['host'] . ";".
                "dbname=".$config['database'].";".
                "port=".$config['port'],
                $config['user'], $config['password']
            );

            $sql = "SELECT COUNT(id) as count_id FROM ".$prefix."users WHERE is_root = 1";
            $count = 0;
            foreach ($db->query($sql) as $row) {
                $count = $row['count_id'];
            }

            if ($count == 0) {
                $sql = "INSERT INTO ".$prefix."users (
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

                InstallLogger::stdout(InstallLogger::getInstance()->logMessage(
                    "[CREATE_ADMIN]: $sql with values " . implode(", ", array_values($userData))
                ));

                if (!$sth->execute(array(
                    $userData['firstname'],
                    $userData['lastname'],
                    $userData['email_address'],
                    $userData['user'],
                    md5($userData['password']),
                    1
                ))) {
                    $err = $db->errorInfo();
                    InstallLogger::stdout(InstallLogger::getInstance()->logMessage(
                        "[CREATE_ADMIN:FAILED]: Duplicate entry for user name or email address? "
                            . (!empty($err) && isset($err[2]) ? $err[2] : $sql)
                    ));
                }
            } else {
                InstallLogger::stdout(InstallLogger::getInstance()->logMessage(
                    "[CREATE_ADMIN:FAILED]: admin already in table"
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

    InstallLogger::stdout(InstallLogger::getInstance()
        ->logMessage("[INFO] recursively deleting $path"));

    if (!file_exists($path)) {
        InstallLogger::stdout(InstallLogger::getInstance()
            ->logMessage("[WARNING] rmdir $path: directory does not exist"));
        return;
    }

    $handle = opendir($path);

    for (;false !== ($file = readdir($handle));) {
        if($file != "." and $file != ".." ) {
            $fullpath= $path.$file;

            if(is_dir($fullpath)) {
                conjoon_rmdir($fullpath);
                if (file_exists($fullpath) && is_dir($fullpath)) {
                    if (!rmdir($fullpath)) {
                        InstallLogger::stdout(InstallLogger::getInstance()
                            ->logMessage("[ERROR] could not rmdir $fullpath"));
                    } else {
                        InstallLogger::stdout(InstallLogger::getInstance()
                            ->logMessage("[SUCCESS] rmdir $fullpath"));
                    }
                }
            } else {
                if (!unlink($fullpath)) {
                    InstallLogger::stdout(InstallLogger::getInstance()
                        ->logMessage("[ERROR] could not unlink $fullpath"));
                } else {
                    InstallLogger::stdout(InstallLogger::getInstance()
                        ->logMessage("[SUCCESS] unlink $fullpath"));
                }
            }
        }
    }
    closedir($handle);

    if(file_exists($path) &&  is_dir($path)) {
        if (!rmdir($path)) {
            InstallLogger::stdout(InstallLogger::getInstance()
                ->logMessage("[ERROR] could not rmdir $path"));
        } else {
            InstallLogger::stdout(InstallLogger::getInstance()
                ->logMessage("[SUCCESS] rmdir $path"));
        }
    } else {
        InstallLogger::stdout(InstallLogger::getInstance()
            ->logMessage("[WARNING] rmdir $path: directory does not exist"));
    }
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
    if ($parts[0] == "") {
        $parts[0] = "/";
    }
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

class InstallLogger {

    private static $_logFile = "";

    private static $_instance = null;

    public static function getInstance($fileName = null)
    {
        if (!self::$_instance) {
            self::$_instance = new InstallLogger();
            self::$_logFile = $fileName;
            if (!file_exists($fileName)) {
                file_put_contents($fileName, "INSTALL LOG\n==========\n\n");
            }
        }

        return self::$_instance;
    }

    public function logMessage($message, $date = null)
    {
        $str = date("H:i:s", time()) . " - " . $message;

        file_put_contents(
            self::$_logFile,
            $str . "\n",
            FILE_APPEND
        );

        return $str;

    }

    public function stdout($message, $parentNote = false) {
        echo $message . '<br />';
        if ($parentNote === true) {
            echo "<script type=\"text/javascript\">parent.updateProgressNote('".$message."');</script>";
        }

        echo "<script type=\"text/javascript\">this.scrollTo(0, document.body.offsetHeight);</script>";
        flush();
        ob_flush();
    }

}
