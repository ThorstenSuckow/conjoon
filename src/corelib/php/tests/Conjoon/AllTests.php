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
    define('PHPUnit_MAIN_METHOD', 'Conjoon_AllTests::main');
}

require_once 'Conjoon/MailTest.php';
require_once 'Conjoon/Filter/AllTests.php';

/**
 * @category   Conjoon
 * @package    Conjoon
 * @subpackage UnitTests
 * @group      Conjoon
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    /**
     * Regular suite
     *
     * @return PHPUnit_Framework_TestSuite
     */
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('conjoon - Conjoon');

        $suite->addTestSuite('Conjoon_MailTest');

        $suite->addTest(Conjoon_Filter_AllTests::suite());

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Conjoon_AllTests::main') {
    Conjoon_AllTests::main();
}
