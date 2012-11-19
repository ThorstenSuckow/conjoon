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
    define('PHPUnit_MAIN_METHOD', 'Conjoon_Mail_Protocol_AllTests::main');
}

require_once 'Conjoon/Mail/Protocol/Pop3Test.php';
require_once 'Conjoon/Mail/Protocol/ImapTest.php';



/**
 * @category   Conjoon
 * @package    Conjoon_Mail
 * @subpackage UnitTests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Mail_Protocol_AllTests
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
        $suite = new PHPUnit_Framework_TestSuite('conjoon - Conjoon_Mail_Protocol');

        $suite->addTestSuite('Conjoon_Mail_Protocol_Pop3');
        $suite->addTestSuite('Conjoon_Mail_Protocol_Imap');


        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Conjoon_Mail_Protocol_AllTests::main') {
    Conjoon_Mail_Protocol_AllTests::main();
}
