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
    define('PHPUnit_MAIN_METHOD', 'Conjoon_Mail_Service_AllTests::main');
}

require_once 'Conjoon/Mail/Service/MailServiceExceptionTest.php';
require_once 'Conjoon/Mail/Service/StorageServiceTest.php';
require_once 'Conjoon/Mail/Service/ImapStorageServiceTest.php';
require_once 'Conjoon/Mail/Service/Pop3StorageServiceTest.php';



/**
 * @category   Conjoon
 * @package    Conjoon_Mail
 * @subpackage UnitTests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Mail_Service_AllTests
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
        $suite = new PHPUnit_Framework_TestSuite('conjoon - Conjoon_Mail_Service');

        $suite->addTestSuite('Conjoon_Mail_Service_MailServiceException');
        $suite->addTestSuite('Conjoon_Mail_Service_StorageService');
        $suite->addTestSuite('Conjoon_Mail_Service_Pop3StorageService');
        $suite->addTestSuite('Conjoon_Mail_Service_ImapStorageService');


        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Conjoon_Mail_Service_AllTests::main') {
    Conjoon_Mail_Service_AllTests::main();
}
