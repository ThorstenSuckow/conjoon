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
$htmlpurifier  = "$cnRoot/../../../vendor/htmlpurifier/library";

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
    $htmlpurifier,
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

/**
 * @see HTMLPurifier_Bootstrap
 */
require_once 'HTMLPurifier/Bootstrap.php';

/**
 * @see HTMLPurifier.autoload
 */
require_once 'HTMLPurifier.autoload.php';

/*
 * Unset global variables that are no longer needed.
 */
unset(
    $cnRoot, $cnCoreLibrary,
    $zdLib, $path,
    $doctrine, $htmlpurifier
);

include_once 'setup.db.php';
