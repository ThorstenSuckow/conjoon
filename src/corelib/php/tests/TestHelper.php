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
    get_include_path()
    );
set_include_path(implode(PATH_SEPARATOR, $path));

/*
 * Unset global variables that are no longer needed.
 */
unset($cnRoot, $cnCoreLibrary, $zdLib, $path);

include_once 'setup.db.php';