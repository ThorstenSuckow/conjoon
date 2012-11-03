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
    define('PHPUnit_MAIN_METHOD', 'Conjoon_Text_AllTests::main');
}

require_once 'Conjoon/Text/Template/AllTests.php';
require_once 'Conjoon/Text/Transformer/AllTests.php';
require_once 'Conjoon/Text/Template.php';

/**
 * @category   Conjoon
 * @package    Conjoon_Text
 * @subpackage UnitTests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Text_AllTests
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
        $suite = new PHPUnit_Framework_TestSuite('conjoon - Conjoon_Text');

        $suite->addTest(Conjoon_Text_Template_AllTests::suite());
        $suite->addTest(Conjoon_Text_Transformer_AllTests::suite());

        $suite->addTestSuite('Conjoon_Text_Template');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Conjoon_Text_AllTests::main') {
    Conjoon_Text_AllTests::main();
}