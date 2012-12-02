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


/**
 * @see Conjoon_Service_Twitter_AccountService
 */
require_once 'Conjoon/Service/Twitter/AccountService.php';


/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Service_Twitter_AccountServiceTest extends PHPUnit_Framework_TestCase {

    protected $input;

    /**
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();


        $this->input = array(
            'http://unittest.conjoon:80/test/get/base/./service/twitter/'
                => array(
                    'oauthCallbackUrl' => './service/twitter/',
                    'baseUrl'          => '/test/get/base/',
                    'port'             => '80',
                    'host'             => 'unittest.conjoon',
                    'protocol'         => 'http'
                ),
            'https://unittest.conjoon:80/test/get/base/./service/twitter.php'
            => array(
                'oauthCallbackUrl' => './service/twitter.php',
                'baseUrl'          => 'test/get/base',
                'port'             => '80',
                'host'             => 'unittest.conjoon',
                'protocol'         => 'https'
            ),
            'https://unittest.conjoon:80/./service/twitter.php'
            => array(
                'oauthCallbackUrl' => './service/twitter.php',
                'baseUrl'          => '/',
                'port'             => '80',
                'host'             => 'unittest.conjoon',
                'protocol'         => 'https'
            ),
            'https://unittest.conjoon:80/test/./service/twitter.php'
            => array(
                'oauthCallbackUrl' => './service/twitter.php',
                'baseUrl'          => 'test',
                'port'             => '80',
                'host'             => 'unittest.conjoon',
                'protocol'         => 'https'
            ),
            'https://unittest.conjoon:80/test/service/twitter.php'
            => array(
                'oauthCallbackUrl' => 'service/twitter.php',
                'baseUrl'          => '/test',
                'port'             => '80',
                'host'             => 'unittest.conjoon',
                'protocol'         => 'https'
            )
        );


    }

    /**
     *
     * @return void
     */
    public function tearDown()
    {

    }

    /**
     * @expectedException Conjoon_Argument_Exception
     */
    public function testGetOauthCallbackUrlException()
    {
        $accountService = new Conjoon_Service_Twitter_AccountService();

        $accountService->getOauthCallbackUrl(array(
            'key' => 'somevalue'
        ));
    }


    /**
     * Ensures everything works as expected.
     */
    public function testGetOauthCallbackUrl()
    {
        $accountService = new Conjoon_Service_Twitter_AccountService();

        foreach ($this->input as $expected => $input) {
            $this->assertSame(
                $expected,
                $accountService->getOauthCallbackUrl($input)
            );
        }
    }


}