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
    define('PHPUnit_MAIN_METHOD', 'Conjoon_Controller_AllTests::main');
}


require_once 'Conjoon/Controller/Action/AllTests.php';

/**
 * @category   Conjoon
 * @package    Conjoon_Controller
 * @subpackage UnitTests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Controller_AllTests {

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
        $suite = new PHPUnit_Framework_TestSuite('conjoon - Conjoon_Controller');

        $suite->addTest(Conjoon_Controller_Action_AllTests::suite());

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Conjoon_Controller_AllTests::main') {
    Conjoon_Controller_AllTests::main();
}
