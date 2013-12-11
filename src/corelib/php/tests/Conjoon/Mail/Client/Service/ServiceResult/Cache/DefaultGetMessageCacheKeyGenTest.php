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
