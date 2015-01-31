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


namespace Conjoon\Mail\Client\Service\ServiceResult\Cache;

/**
 * @see GetMessageCacheKey
 */
require_once 'Conjoon/Mail/Client/Service/ServiceResult/Cache/DefaultGetMessageCacheKeyGen.php';

/**
 * @category   Conjoon
 * @package    Conjoon_Service
 * @subpackage UnitTests
 * @group      Conjoon_Service
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultGetMessageCacheKeyGenTest extends \PHPUnit_Framework_TestCase {


    protected $data;

    protected $keyGen;

    /**
     * @inheritdoc
     */
    protected function setUp() {
        parent::setUp();

        $this->keyGen = new DefaultGetMessageCacheKeyGen();

        $this->data = array(
            'userId' => 3,
            'messageId' => 233,
            'path' => "[2,5,6,7]",
            'format' => 'html',
            'externalResources' => true
        );

    }

    /**
     * @ticket CN-811
     */
    public function testPathInvalidCharacters() {

        $data = $this->data;
        $data['path'] = "[\"[MailFolder]\",\"Anotherone\"]";

        $key = $this->keyGen->generateKey($data);

        $this->assertTrue(
            $key instanceof
                \Conjoon\Mail\Client\Service\ServiceResult\Cache\GetMessageCacheKey
        );

        $this->assertTrue(preg_match('~^[a-zA-Z0-9_]+$~D', $key->getValue()) != 0);

        $this->assertTrue(is_string($key->getValue()));

    }

    /**
     * Ensures everything works as expected
     */
    public function testOkay() {


        $keyGen = $this->keyGen;

        $key = $keyGen->generateKey($this->data);

        $this->assertTrue(
            $key instanceof
                \Conjoon\Mail\Client\Service\ServiceResult\Cache\GetMessageCacheKey
        );

        $this->assertTrue(is_string($key->getValue()));

    }

    /**
     * @expectedException \Conjoon\Argument\InvalidArgumentException
     */
    public function testPathInvalid_1() {

        $this->data['path'] = json_encode(new \stdClass);

        $keyGen = $this->keyGen;

        $key = $keyGen->generateKey($this->data);

    }

    /**
     * @expectedException \Conjoon\Argument\InvalidArgumentException
     */
    public function testPathInvalid_2() {

        $this->data['path'] = 2324;

        $keyGen = $this->keyGen;

        $key = $keyGen->generateKey($this->data);

    }

    /**
     * @expectedException \Conjoon\Argument\InvalidArgumentException
     */
    public function testPathInvalid_3() {

        $this->data['path'] = "23;23 ssfsfsf";

        $keyGen = $this->keyGen;

        $key = $keyGen->generateKey($this->data);

    }

}
