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
    define('PHPUnit_MAIN_METHOD', 'AllTests::main');
}

require_once 'Conjoon/AllTests.php';

/**
 * @category   Conjoon
 * @package    Conjoon
 * @subpackage UnitTests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class AllTests
{
    public static function main()
    {
        $parameters = array();

        PHPUnit_TextUI_TestRunner::run(self::suite(), $parameters);
    }

    /**
     * Regular suite
     *
     * @return PHPUnit_Framework_TestSuite
     */
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('conjoon');

        $suite->addTest(Conjoon_AllTests::suite());

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'AllTests::main') {
    AllTests::main();
}
