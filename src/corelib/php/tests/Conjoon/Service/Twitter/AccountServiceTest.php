<?php
/**
 * conjoon
 * (c) 2007-2015 conjoon.org
 * licensing@conjoon.org
 *
 * conjoon
 * Copyright (C) 2014 Thorsten Suckow-Homberg/conjoon.org
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
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