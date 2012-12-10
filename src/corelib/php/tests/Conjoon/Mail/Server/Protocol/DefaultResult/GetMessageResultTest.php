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


namespace Conjoon\Mail\Server\Protocol\DefaultResult;

/**
 * @see SetFlagsResult
 */
require_once 'Conjoon/Mail/Server/Protocol/DefaultResult/GetMessageResult.php';


/**
 * @category   Conjoon
 * @package    Conjoon_Mail
 * @subpackage UnitTests
 * @group      Conjoon_Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class GetMessageResultTest extends \PHPUnit_Framework_TestCase {

    protected $input;

    protected function setUp()
    {
        parent::setUp();

        $toArray = array(
            'getMessage' => array('__FAIL__')
        );

        $this->input = array(array(
            'data' => array(
                '__toString' => json_encode($toArray),
                'toJson'     => json_encode($toArray),
                'toArray'    => $toArray
            )
        ));

    }

    /**
     * Ensures everathing works as expected
     */
    public function testOk()
    {
        $this->fail();
        return;

        foreach ($this->input as $input) {

            $successResult = new GetMessageResult();

            foreach ($input['data'] as $method => $result) {
                $this->assertEquals($result, $successResult->$method());
            }
        }
    }

}
