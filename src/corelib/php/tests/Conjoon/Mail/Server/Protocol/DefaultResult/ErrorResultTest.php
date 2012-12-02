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
 * @see ErrorResult
 */
require_once 'Conjoon/Mail/Server/Protocol/DefaultResult/ErrorResult.php';


/**
 * @category   Conjoon
 * @package    Conjoon_Mail
 * @subpackage UnitTests
 * @group      Conjoon_Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class ErrorResultTest extends \PHPUnit_Framework_TestCase {

    protected $input;

    protected function setUp()
    {
        parent::setUp();

        $previousException = new \Exception("Message Previous", 0);

        $newException = new \Exception("Message current", 1, $previousException);

        $toArrayPrevious = array(
            'exceptionClass'    => get_class($previousException),
            'message'           => $previousException->getMessage(),
            'code'              => $previousException->getCode(),
            'previousException' => null
        );

        $toArrayCurrent = array(
            'exceptionClass'    => get_class($newException),
            'message'           => $newException->getMessage(),
            'code'              => $newException->getCode(),
            'previousException' => $toArrayPrevious
        );

        $this->input = array(array(
            'exception' => $newException,
            'data'      => array(
                '__toString' => json_encode($toArrayCurrent),
                'toJson'     => json_encode($toArrayCurrent),
                'toArray'    => $toArrayCurrent
            )
        ));

    }

    /**
     * Ensures everathing works as expected
     */
    public function testOk()
    {
        foreach ($this->input as $input) {

            $errorResult = new ErrorResult($input['exception']);

            foreach ($input['data'] as $method => $result) {

                $this->assertEquals($result, $errorResult->$method());

            }


        }
    }

}
