<?php
/**
 * conjoon
 * (c) 2002-2011 siteartwork.de/conjoon.org
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
    define('PHPUnit_MAIN_METHOD', 'Conjoon_Filter_AllTests::main');
}

require_once 'Conjoon/Filter/ExceptionTest.php';
require_once 'Conjoon/Filter/DateToUtcTest.php';
require_once 'Conjoon/Filter/DateUtcToLocalTest.php';

/**
 * @category   Conjoon
 * @package    Conjoon_Filter
 * @subpackage UnitTests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Filter_AllTests
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
        $suite = new PHPUnit_Framework_TestSuite('conjoon - Conjoon_Filter');

        $suite->addTestSuite('Conjoon_Filter_ExceptionTest');
        $suite->addTestSuite('Conjoon_Filter_DateToUtcTest');
        $suite->addTestSuite('Conjoon_Filter_DateUtcToLocalTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Conjoon_Filter_AllTests::main') {
    Conjoon_Filter_AllTests::main();
}
