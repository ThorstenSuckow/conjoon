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
    define('PHPUnit_MAIN_METHOD', 'Conjoon_Argument_AllTests::main');
}

require_once 'Conjoon/Argument/CheckTest.php';

/**
 * @category   Conjoon
 * @package    Conjoon_Argument
 * @subpackage UnitTests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Argument_AllTests
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
        $suite = new PHPUnit_Framework_TestSuite('conjoon - Conjoon_Argument');

        $suite->addTestSuite('Conjoon_Argument_CheckTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Conjoon_Argument_AllTests::main') {
    Conjoon_Filter_AllTests::main();
}
