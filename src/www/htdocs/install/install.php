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
 * Will sum up all the installation data and install conjoon upon a post request.
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */

$INSTALL = array();

$INSTALL['include_path']['ini'] = rtrim($_SESSION['lib_path'], '/')
                                  . '/'
                                  . $_SESSION['setup_ini']['lib_path']['folder'];

$INSTALL['include_path']['delete'] = explode(",", $_SESSION['setup_ini']['lib_path']['delete']);

$INSTALL['app_path']['full']   = rtrim($_SESSION['app_path'], '/')
                                  . '/'
                                  . $_SESSION['setup_ini']['app_path']['folder'];
$INSTALL['app_path']['delete'] = explode(",", $_SESSION['setup_ini']['app_path']['delete']);

$INSTALL['include_path']['delete_warning'] = false;
$INSTALL['app_path']['delete_warning']     = false;

$fAppPaths = $INSTALL['app_path']['delete'];

$INSTALL['app_path']['delete'] = array();
for ($i = 0, $len = count($fAppPaths); $i < $len; $i++) {
    if (file_exists($INSTALL['app_path']['full'] .'/' . $fAppPaths[$i])) {
        $INSTALL['app_path']['delete'][] = $fAppPaths[$i];
    }
}

if (file_exists($INSTALL['app_path']['full'])) {
    $INSTALL['app_path']['delete_warning'] = true;
}

if (count($INSTALL['app_path']['delete']) == 0) {
    $INSTALL['app_path']['delete_warning'] = false;
}

if (file_exists($INSTALL['include_path']['ini'])) {
    $INSTALL['include_path']['delete_warning'] = true;
}

$INSTALL['IMREMOVING'] = array(
    '_configCache' => file_exists('../_configCache'),
    'js'           => file_exists('../js')
);

$INSTALL['CACHE_REMOVE'] = array(
    'WARNING' => false,
    'FILES'   => array()
);

if (isset($_SESSION['installation_info'])) {
    if (isset($_SESSION['installation_info']['cache.default.caching'])
        && $_SESSION['installation_info']['cache.default.caching']) {
        $INSTALL['CACHE_REMOVE']['WARNING'] = true;

        if (isset($_SESSION['installation_info']['cache.db.metadata.backend.cache_dir'])
            && file_exists($_SESSION['installation_info']['cache.db.metadata.backend.cache_dir'])) {
            $INSTALL['CACHE_REMOVE']['FILES'][] = $_SESSION['installation_info']['cache.db.metadata.backend.cache_dir'];
        }

        if (isset($_SESSION['installation_info']['cache.email.message.backend.cache_dir'])
            && file_exists($_SESSION['installation_info']['cache.email.message.backend.cache_dir'])) {
            $INSTALL['CACHE_REMOVE']['FILES'][] = $_SESSION['installation_info']['cache.email.message.backend.cache_dir'];
        }

        if (isset($_SESSION['installation_info']['cache.email.accounts.backend.cache_dir'])
            && file_exists($_SESSION['installation_info']['cache.email.accounts.backend.cache_dir'])) {
            $INSTALL['CACHE_REMOVE']['FILES'][] = $_SESSION['installation_info']['cache.email.accounts.backend.cache_dir'];
        }

        if (isset($_SESSION['installation_info']['cache.feed.item.backend.cache_dir'])
            && file_exists($_SESSION['installation_info']['cache.feed.item.backend.cache_dir'])) {
            $INSTALL['CACHE_REMOVE']['FILES'][] = $_SESSION['installation_info']['cache.feed.item.backend.cache_dir'];
        }

        if (isset($_SESSION['installation_info']['cache.feed.item_list.backend.cache_dir'])
            && file_exists($_SESSION['installation_info']['cache.feed.item_list.backend.cache_dir'])) {
            $INSTALL['CACHE_REMOVE']['FILES'][] = $_SESSION['installation_info']['cache.feed.item_list.backend.cache_dir'];
        }

        if (isset($_SESSION['installation_info']['cache.feed.reader.backend.cache_dir'])
            && file_exists($_SESSION['installation_info']['cache.feed.reader.backend.cache_dir'])) {
            $INSTALL['CACHE_REMOVE']['FILES'][] = $_SESSION['installation_info']['cache.feed.reader.backend.cache_dir'];
        }

        if (isset($_SESSION['installation_info']['cache.feed.account.backend.cache_dir'])
            && file_exists($_SESSION['installation_info']['cache.feed.account.backend.cache_dir'])) {
            $INSTALL['CACHE_REMOVE']['FILES'][] = $_SESSION['installation_info']['cache.feed.account.backend.cache_dir'];
        }

        if (isset($_SESSION['installation_info']['cache.feed.account_list.backend.cache_dir'])
            && file_exists($_SESSION['installation_info']['cache.feed.account_list.backend.cache_dir'])) {
            $INSTALL['CACHE_REMOVE']['FILES'][] = $_SESSION['installation_info']['cache.feed.account_list.backend.cache_dir'];
        }

        if (isset($_SESSION['installation_info']['cache.twitter.accounts.backend.cache_dir'])
            && file_exists($_SESSION['installation_info']['cache.twitter.accounts.backend.cache_dir'])) {
            $INSTALL['CACHE_REMOVE']['FILES'][] = $_SESSION['installation_info']['cache.twitter.accounts.backend.cache_dir'];
        }
    }
}


if (isset($_POST['install_post'])) {


    // generate logging file
    InstallLogger::getInstance('./INSTALL_LOG-' . date("Y.m.d-H.i.s", time()). '.log');

    // proceed installation. First of, generate .htaccess
    $htaccess = file_get_contents('./htaccess.template');
    $htaccess = str_replace("{REWRITE_BASE}", $_SESSION['doc_path'], $htaccess);
    file_put_contents('../.htaccess', $htaccess);
    $htaccess = "";

    $libFolder = $_SESSION['setup_ini']['lib_path']['folder'];
    $appFolder = $_SESSION['setup_ini']['app_path']['folder'];

    // generate config.ini.php
    $configini = file_get_contents('./config.ini.php.template');

    if ($_SESSION['add_include_path']) {
        $configini = str_replace(
            "{INCLUDE_PATH}",
            $_SESSION['lib_path'] . "/" . $libFolder,
            $configini
        );
    } else {
        $configini = str_replace("{INCLUDE_PATH}", "", $configini);
    }

    $configini = str_replace(
        "{APPLICATION_PATH}",
        $_SESSION['app_path'] . '/' . $_SESSION['setup_ini']['app_path']['folder'],
        $configini
    );

    $configini = str_replace("{BASE_URL}", $_SESSION['doc_path'], $configini);
    $configini = str_replace("{EDITION}", '"' . $_SESSION['edition']. '"', $configini);
    $configini = str_replace("{DATABASE.ADAPTER}", $_SESSION['db_adapter'], $configini);
    $configini = str_replace("{DATABASE.HOST}", $_SESSION['db_host'], $configini);
    $configini = str_replace("{DATABASE.PORT}", $_SESSION['db_port'], $configini);
    $configini = str_replace("{DATABASE.USER}", $_SESSION['db_user'], $configini);
    $configini = str_replace("{DATABASE.TABLE.PREFIX}", $_SESSION['db_table_prefix'], $configini);
    $configini = str_replace("{DATABASE.PASSWORD}", $_SESSION['db_password'], $configini);
    $configini = str_replace("{DATABASE.DATABASE}", $_SESSION['db'], $configini);
    $configini = str_replace("{DATABASE_MAX_ALLOWED_PACKET}", $_SESSION['max_allowed_packet'], $configini);

    // localization
    $configini = str_replace("{LOCALE.DATE.TIMEZONE}", $_SESSION['locale_timezone_default'], $configini);

    // caching
    $configini = str_replace("{CACHE.DEFAULT.CACHING}", $_SESSION['cache']['default.caching'] ? '1' : '0', $configini);

    $configini = str_replace("{CACHE.DB.METADATA.CACHING}", $_SESSION['cache']['db.metadata.caching'] ? '1' : '0', $configini);
    $configini = str_replace("{CACHE.DB.METADATA.BACKEND.CACHE_DIR}",
                    $_SESSION['cache']['db.metadata.caching']
                    ? $_SESSION['cache']['db.metadata.backend.cache_dir']
                    : "",
                 $configini);

    $configini = str_replace("{CACHE.EMAIL.MESSAGE.CACHING}", $_SESSION['cache']['email.message.caching'] ? '1' : '0', $configini);
    $configini = str_replace("{CACHE.EMAIL.MESSAGE.BACKEND.CACHE_DIR}",
                    $_SESSION['cache']['email.message.caching']
                    ? $_SESSION['cache']['email.message.backend.cache_dir']
                    : "",
                 $configini);

    $configini = str_replace("{CACHE.EMAIL.ACCOUNTS.CACHING}", $_SESSION['cache']['email.accounts.caching'] ? '1' : '0', $configini);
    $configini = str_replace("{CACHE.EMAIL.ACCOUNTS.BACKEND.CACHE_DIR}",
                    $_SESSION['cache']['email.accounts.caching']
                    ? $_SESSION['cache']['email.accounts.backend.cache_dir']
                    : "",
                 $configini);

    $configini = str_replace("{CACHE.FEED.ITEM.CACHING}", $_SESSION['cache']['feed.item.caching'] ? '1' : '0', $configini);
    $configini = str_replace("{CACHE.FEED.ITEM.BACKEND.CACHE_DIR}",
                    $_SESSION['cache']['feed.item.caching']
                    ? $_SESSION['cache']['feed.item.backend.cache_dir']
                    : "",
                 $configini);

    $configini = str_replace("{CACHE.FEED.ITEM_LIST.CACHING}", $_SESSION['cache']['feed.item_list.caching'] ? '1' : '0', $configini);
    $configini = str_replace("{CACHE.FEED.ITEM_LIST.BACKEND.CACHE_DIR}",
                    $_SESSION['cache']['feed.item_list.caching']
                    ? $_SESSION['cache']['feed.item_list.backend.cache_dir']
                    : "",
                 $configini);

    $configini = str_replace("{CACHE.FEED.READER.CACHING}", $_SESSION['cache']['feed.reader.caching'] ? '1' : '0', $configini);
    $configini = str_replace("{CACHE.FEED.READER.BACKEND.CACHE_DIR}",
                    $_SESSION['cache']['feed.reader.caching']
                    ? $_SESSION['cache']['feed.reader.backend.cache_dir']
                    : "",
                 $configini);

    $configini = str_replace("{CACHE.FEED.ACCOUNT.CACHING}", $_SESSION['cache']['feed.account.caching'] ? '1' : '0', $configini);
    $configini = str_replace("{CACHE.FEED.ACCOUNT.BACKEND.CACHE_DIR}",
                    $_SESSION['cache']['feed.account.caching']
                    ? $_SESSION['cache']['feed.account.backend.cache_dir']
                    : "",
                 $configini);

    $configini = str_replace("{CACHE.FEED.ACCOUNT_LIST.CACHING}", $_SESSION['cache']['feed.account_list.caching'] ? '1' : '0', $configini);
    $configini = str_replace("{CACHE.FEED.ACCOUNT_LIST.BACKEND.CACHE_DIR}",
                    $_SESSION['cache']['feed.account_list.caching']
                    ? $_SESSION['cache']['feed.account_list.backend.cache_dir']
                    : "",
                 $configini);

    $configini = str_replace("{CACHE.TWITTER.ACCOUNTS.CACHING}", $_SESSION['cache']['twitter.accounts.caching'] ? '1' : '0', $configini);
    $configini = str_replace("{CACHE.TWITTER.ACCOUNTS.BACKEND.CACHE_DIR}",
                    $_SESSION['cache']['twitter.accounts.caching']
                    ? $_SESSION['cache']['twitter.accounts.backend.cache_dir']
                    : "",
                 $configini);



    file_put_contents('../config.ini.php', $configini);
    $configini = "";


    // generate and update install.info.php
    if (!file_exists('../installation.info.php')) {
        $installationinfo = file_get_contents('./installation.info.php.template');
    } else {
        $installationinfo = file_get_contents('../installation.info.php');
    }
    $installationinfo .= "\n
        // generated by conjoon V".$_SESSION['current_version']."
        // on ".date("m-d-Y H:i:s", time())."
        \$INSTALLATION_INFO[] = array(
            'locale_timezone_default'  => '"
                                      .$_SESSION['locale_timezone_default']."',
            'locale_timezone_fallback' => '"
                                      .$_SESSION['locale_timezone_fallback']."',
            'support_key'        => '".$_SESSION['support_key']."',
            'version'            => '".$_SESSION['current_version']."',
            'date'               => '".time()."',
            'db_host'            => '".$_SESSION['db_host']."',
            'db_adapter'         => '".$_SESSION['db_adapter']."',
            'db'                 => '".$_SESSION['db']."',
            'db_port'            => '".$_SESSION['db_port']."',
            'db_user'            => '".$_SESSION['db_user']."',
            'db_table_prefix'    => '".$_SESSION['db_table_prefix']."',
            'edition'            => '".$_SESSION['edition']."',
            'max_allowed_packet' => '".$_SESSION['max_allowed_packet']."',
            'app_path'           => '".$_SESSION['app_path']."',
            'lib_path'           => '".$_SESSION['lib_path']."',
            'add_include_path'   => '".$_SESSION['add_include_path']."',
            'doc_path'           => '".$_SESSION['doc_path']."',
            'applied_patches'    => array(".(empty($_SESSION['applied_patches']) ? "" : "'").implode(
                                                "','",
                                                array_values($_SESSION['applied_patches'])
                                            ).(empty($_SESSION['applied_patches']) ? "" : "'")."),
            'ignored_patches'    => array(".(empty($_SESSION['ignored_patches']) ? "" : "'").implode(
                                                "','",
                                                array_values($_SESSION['ignored_patches'])
                                            ).(empty($_SESSION['ignored_patches']) ? "" : "'")."),


            ".($_SESSION['cache']['default.caching']
              ? "

              'cache.default.caching' => 1,

              'cache.db.metadata.caching' => ".($_SESSION['cache']['db.metadata.caching'] ? "1" : "0").",
              ".($_SESSION['cache']['db.metadata.caching'] ? "'cache.db.metadata.backend.cache_dir' => '".$_SESSION['cache']['db.metadata.backend.cache_dir']."'," : "")."

              'cache.email.message.caching' => ".($_SESSION['cache']['email.message.caching'] ? "1" : "0").",
              ".($_SESSION['cache']['email.message.caching'] ? "'cache.email.message.backend.cache_dir' => '".$_SESSION['cache']['email.message.backend.cache_dir']."'," : "")."

              'cache.email.accounts.caching' => ".($_SESSION['cache']['email.accounts.caching'] ? "1" : "0").",
              ".($_SESSION['cache']['email.accounts.caching'] ? "'cache.email.accounts.backend.cache_dir' => '".$_SESSION['cache']['email.accounts.backend.cache_dir']."'," : "")."

              'cache.feed.item.caching' => ".($_SESSION['cache']['feed.item.caching'] ? "1" : "0").",
              ".($_SESSION['cache']['feed.item.caching'] ? "'cache.feed.item.backend.cache_dir' => '".$_SESSION['cache']['feed.item.backend.cache_dir']."'," : "")."

              'cache.feed.item_list.caching' => ".($_SESSION['cache']['feed.item_list.caching'] ? "1" : "0").",
              ".($_SESSION['cache']['feed.item_list.caching'] ? "'cache.feed.item_list.backend.cache_dir' => '".$_SESSION['cache']['feed.item_list.backend.cache_dir']."'," : "")."

              'cache.feed.reader.caching' => ".($_SESSION['cache']['feed.reader.caching'] ? "1" : "0").",
              ".($_SESSION['cache']['feed.reader.caching'] ? "'cache.feed.reader.backend.cache_dir' => '".$_SESSION['cache']['feed.reader.backend.cache_dir']."'," : "")."

              'cache.feed.account.caching' => ".($_SESSION['cache']['feed.account.caching'] ? "1" : "0").",
              ".($_SESSION['cache']['feed.account.caching'] ? "'cache.feed.account.backend.cache_dir' => '".$_SESSION['cache']['feed.account.backend.cache_dir']."'," : "")."

              'cache.feed.account_list.caching' => ".($_SESSION['cache']['feed.account_list.caching'] ? "1" : "0").",
              ".($_SESSION['cache']['feed.account_list.caching'] ? "'cache.feed.account_list.backend.cache_dir' => '".$_SESSION['cache']['feed.account_list.backend.cache_dir']."'," : "")."

              'cache.twitter.accounts.caching' => ".($_SESSION['cache']['twitter.accounts.caching'] ? "1" : "0").",
              ".($_SESSION['cache']['twitter.accounts.caching'] ? "'cache.twitter.accounts.backend.cache_dir' => '".$_SESSION['cache']['twitter.accounts.backend.cache_dir']."'," : "")."


              "
              : "'cache.default.caching' => 0,"
            )."

            'app_credentials'    => array('user' => '".$_SESSION['app_credentials']['user']."')
        );
    ";

    file_put_contents('../installation.info.php', $installationinfo);
    $installationinfo = "";

    // import the sql file for the selected database
    $path = realpath('./files/datastore/mysql/conjoon.sql');
    conjoon_createTables($path, $_SESSION['db_adapter'], array(
        'host'     => $_SESSION['db_host'],
        'port'     => $_SESSION['db_port'],
        'database' => $_SESSION['db'],
        'user'     => $_SESSION['db_user'],
        'password' => $_SESSION['db_password'],
        'prefix'   => $_SESSION['db_table_prefix']
    ));
    $table = "";
    sleep(1);
    // create root user if needed
    if (!isset($_SESSION['installation_info']['app_credentials'])) {
        conjoon_createAdmin($_SESSION['db_adapter'], array(
            'user'          => $_SESSION['app_credentials']['user'],
            'password'      => $_SESSION['app_credentials']['password'],
            'firstname'     => $_SESSION['app_credentials']['firstname'],
            'lastname'      => $_SESSION['app_credentials']['lastname'],
            'email_address' => $_SESSION['app_credentials']['email_address']
        ), array(
            'host'     => $_SESSION['db_host'],
            'port'     => $_SESSION['db_port'],
            'database' => $_SESSION['db'],
            'user'     => $_SESSION['db_user'],
            'password' => $_SESSION['db_password'],
            'prefix'   => $_SESSION['db_table_prefix']
        ));
    }

    // move libs folders
    $libFolders = explode(",", $_SESSION['setup_ini']['lib_path']['delete']);
    for ($i = 0, $len = count($libFolders); $i < $len; $i++) {
        $libFolders[$i] = trim($libFolders[$i]);
        conjoon_rmdir($_SESSION['lib_path'] . "/" . $libFolder . "/" . $libFolders[$i]);
        rmdir($_SESSION['lib_path'] . "/" . $libFolder . "/" . $libFolders[$i]);
        if (!file_exists($_SESSION['lib_path'] . "/" . $libFolder)) {
            mkdir($_SESSION['lib_path'] . "/" . $libFolder);
        }
        if (file_exists('./files/' . $libFolder . "/" . $libFolders[$i])) {
            rename(
                './files/' . $libFolder . "/" . $libFolders[$i],
                $_SESSION['lib_path'] . "/" . $libFolder . "/" . $libFolders[$i]
            );
        }
    }

    // replace $LIBRARY_PATH_BOOTSTRAP in index.php to enable autoloader
    // replace $LOCALE_DEFAULT_TIMEZONE in index.php for local.timezone
    // fallback
    $indexFile = file_get_contents('../index.php');
    $indexFile = str_replace(
        array('$LIBRARY_PATH_BOOTSTRAP', '$LOCALE_DEFAULT_TIMEZONE'),
        array(
            "'".$_SESSION['lib_path'] . '/' . $libFolder ."'",
            "'".$_SESSION['locale_timezone_fallback']."'"
        ),
        $indexFile
    );
    file_put_contents('../index.php', $indexFile);



    // move application folders
    $appFolders = explode(",", $_SESSION['setup_ini']['app_path']['delete']);
    for ($i = 0, $len = count($appFolders); $i < $len; $i++) {
        $appFolders[$i] = trim($appFolders[$i]);
        conjoon_rmdir($_SESSION['app_path'] . "/" . $appFolder . "/" . $appFolders[$i]);
        rmdir($_SESSION['app_path'] . "/" . $appFolder . "/" . $appFolders[$i]);
        if (!file_exists($_SESSION['app_path'] . "/" . $appFolder)) {
            mkdir($_SESSION['app_path'] . "/" . $appFolder);
        }
        if (file_exists('./files/' . $appFolder . "/" . $appFolders[$i])) {
            rename(
                './files/' . $appFolder . "/" . $appFolders[$i],
                $_SESSION['app_path'] . "/" . $appFolder . "/" . $appFolders[$i]
            );
        }
    }

    // remove old cache folders
    if (isset($_SESSION['installation_info']['cache.default.caching'])
        && $_SESSION['installation_info']['cache.default.caching']) {

        if (isset($_SESSION['installation_info']['cache.db.metadata.backend.cache_dir'])
            && file_exists($_SESSION['installation_info']['cache.db.metadata.backend.cache_dir'])) {
            @conjoon_rmdir($_SESSION['installation_info']['cache.db.metadata.backend.cache_dir']);
            @rmdir($_SESSION['installation_info']['cache.db.metadata.backend.cache_dir']);
        }

        if (isset($_SESSION['installation_info']['cache.email.message.backend.cache_dir'])
            && file_exists($_SESSION['installation_info']['cache.email.message.backend.cache_dir'])) {
            @conjoon_rmdir($_SESSION['installation_info']['cache.email.message.backend.cache_dir']);
            @rmdir($_SESSION['installation_info']['cache.email.message.backend.cache_dir']);
        }

        if (isset($_SESSION['installation_info']['cache.email.accounts.backend.cache_dir'])
            && file_exists($_SESSION['installation_info']['cache.email.accounts.backend.cache_dir'])) {
            @conjoon_rmdir($_SESSION['installation_info']['cache.email.accounts.backend.cache_dir']);
            @rmdir($_SESSION['installation_info']['cache.email.accounts.backend.cache_dir']);
        }

        if (isset($_SESSION['installation_info']['cache.feed.item.backend.cache_dir'])
            && file_exists($_SESSION['installation_info']['cache.feed.item.backend.cache_dir'])) {
            @conjoon_rmdir($_SESSION['installation_info']['cache.feed.item.backend.cache_dir']);
            @rmdir($_SESSION['installation_info']['cache.feed.item.backend.cache_dir']);
        }

        if (isset($_SESSION['installation_info']['cache.feed.item_list.backend.cache_dir'])
            && file_exists($_SESSION['installation_info']['cache.feed.item_list.backend.cache_dir'])) {
            @conjoon_rmdir($_SESSION['installation_info']['cache.feed.item_list.backend.cache_dir']);
            @rmdir($_SESSION['installation_info']['cache.feed.item_list.backend.cache_dir']);
        }

        if (isset($_SESSION['installation_info']['cache.feed.reader.backend.cache_dir'])
            && file_exists($_SESSION['installation_info']['cache.feed.reader.backend.cache_dir'])) {
            @conjoon_rmdir($_SESSION['installation_info']['cache.feed.reader.backend.cache_dir']);
            @rmdir($_SESSION['installation_info']['cache.feed.reader.backend.cache_dir']);
        }

        if (isset($_SESSION['installation_info']['cache.feed.account.backend.cache_dir'])
            && file_exists($_SESSION['installation_info']['cache.feed.account.backend.cache_dir'])) {
            @conjoon_rmdir($_SESSION['installation_info']['cache.feed.account.backend.cache_dir']);
            @rmdir($_SESSION['installation_info']['cache.feed.account.backend.cache_dir']);
        }

        if (isset($_SESSION['installation_info']['cache.feed.account_list.backend.cache_dir'])
            && file_exists($_SESSION['installation_info']['cache.feed.account_list.backend.cache_dir'])) {
            @conjoon_rmdir($_SESSION['installation_info']['cache.feed.account_list.backend.cache_dir']);
            @rmdir($_SESSION['installation_info']['cache.feed.account_list.backend.cache_dir']);
        }

        if (isset($_SESSION['installation_info']['cache.twitter.accounts.backend.cache_dir'])
            && file_exists($_SESSION['installation_info']['cache.twitter.accounts.backend.cache_dir'])) {
            @conjoon_rmdir($_SESSION['installation_info']['cache.twitter.accounts.backend.cache_dir']);
            @rmdir($_SESSION['installation_info']['cache.twitter.accounts.backend.cache_dir']);
        }
    }

    // create caching folders
    if ($_SESSION['cache']['default.caching']) {

        if ($_SESSION['cache']['db.metadata.caching']) {
            conjoon_mkdir($_SESSION['cache']['db.metadata.backend.cache_dir']);
        }

        if ($_SESSION['cache']['email.message.caching']) {
            conjoon_mkdir($_SESSION['cache']['email.message.backend.cache_dir']);
        }

        if ($_SESSION['cache']['email.accounts.caching']) {
            conjoon_mkdir($_SESSION['cache']['email.accounts.backend.cache_dir']);
        }

        if ($_SESSION['cache']['feed.item.caching']) {
            conjoon_mkdir($_SESSION['cache']['feed.item.backend.cache_dir']);
        }

        if ($_SESSION['cache']['feed.item_list.caching']) {
            conjoon_mkdir($_SESSION['cache']['feed.item_list.backend.cache_dir']);
        }

        if ($_SESSION['cache']['feed.reader.caching']) {
            conjoon_mkdir($_SESSION['cache']['feed.reader.backend.cache_dir']);
        }

        if ($_SESSION['cache']['feed.account.caching']) {
            conjoon_mkdir($_SESSION['cache']['feed.account.backend.cache_dir']);
        }

        if ($_SESSION['cache']['feed.account_list.caching']) {
            conjoon_mkdir($_SESSION['cache']['feed.account_list.backend.cache_dir']);
        }

        if ($_SESSION['cache']['twitter.accounts.caching']) {
            conjoon_mkdir($_SESSION['cache']['twitter.accounts.backend.cache_dir']);
        }
    }

    // apply patches, if any
    foreach ($_SESSION['patches'] as $patch => $doApply) {
        if ($doApply) {
            if (file_exists('./patches/'.$patch.'/run.php')) {
                include_once './patches/'.$patch.'/run.php';
            }
        }
    }

    // delete folders from a previous installation
    if ($INSTALL['IMREMOVING']['js']) {
        conjoon_rmdir('../js');
        rmdir('../js');
    }
    if ($INSTALL['IMREMOVING']['_configCache']) {
        conjoon_rmdir('../_configCache');
        rmdir('../_configCache');
    }

    // move js folder to htdocs
    rename('./files/js', '../js');

    // move _configCache to htdocs
    rename('./files/_configCache', '../_configCache');

    header("Location: ./?action=install_success");
    die();
}

include_once './view/install.tpl';