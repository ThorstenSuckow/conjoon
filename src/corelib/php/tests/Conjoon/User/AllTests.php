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

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Conjoon_User_AllTests::main');
}

require_once 'Conjoon/User/UserExceptionTest.php';
require_once 'Conjoon/User/UserTest.php';
require_once 'Conjoon/User/AppUserTest.php';


/**
 * @category   Conjoon
 * @package    Conjoon_User
 * @subpackage UnitTests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_User_AllTests
{
    /**
     * Runs this test suite
     *
     * @return void
     */
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    /**
     * Creates and returns this test suite
     *
     * @return PHPUnit_Framework_TestSuite
     */
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('conjoon - Conjoon_User');

        $suite->addTestSuite('Conjoon_User_UserExceptionTest');
        $suite->addTestSuite('Conjoon_User_UserTest');
        $suite->addTestSuite('Conjoon_User_AppUserTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Conjoon_User_AllTests::main') {
    Conjoon_User_AllTests::main();
}
