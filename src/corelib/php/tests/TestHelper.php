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
 * Include PHPUnit dependencies
 */
require_once 'PHPUnit/Runner/Version.php';

require_once 'PHPUnit/Autoload.php'; // >= PHPUnit 3.5.5

/*
 * Set error reporting to the level to which conjoon code must comply.
 */
error_reporting(E_ALL);

/*
 * Determine the root, library, and tests directories of the framework
 * distribution.
 */
$cnRoot        = realpath(dirname(dirname(__FILE__)));
$cnCoreLibrary = "$cnRoot/library";
$cnCoreTests   = "$cnRoot/tests";
$zdLib         = "$cnRoot/../../../vendor/zendframework/library";
$doctrine      = "$cnRoot/../../../vendor/doctrine";

/*
 * Prepend the conjoon library/ and tests/ directories to the
 * include_path. This allows the tests to run out of the box and helps prevent
 * loading other copies of the framework code and tests that would supersede
 * this copy.
 */
$path = array(
    $cnCoreLibrary,
    $cnCoreTests,
    $zdLib,
    $doctrine,
    get_include_path()
    );
set_include_path(implode(PATH_SEPARATOR, $path));

/**
 * @see Doctrine\ORM\Tools\Setup
 */
require_once 'Doctrine/ORM/Tools/Setup.php';

Doctrine\ORM\Tools\Setup::registerAutoloadDirectory($doctrine);

/**
 * @see Doctrine\Common\ClassLoader
 */
require_once 'Doctrine/Common/ClassLoader.php';

$classLoader = new \Doctrine\Common\ClassLoader(
    'Conjoon', dirname(__FILE__) . '/../library'
);
$classLoader->register();


/*
 * Unset global variables that are no longer needed.
 */
unset(
    $cnRoot, $cnCoreLibrary,
    $zdLib, $path,
    $doctrine
);

include_once 'setup.db.php';