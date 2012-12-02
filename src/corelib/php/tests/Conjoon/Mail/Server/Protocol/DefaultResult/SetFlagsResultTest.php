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
require_once 'Conjoon/Mail/Server/Protocol/DefaultResult/SetFlagsResult.php';


/**
 * @category   Conjoon
 * @package    Conjoon_Mail
 * @subpackage UnitTests
 * @group      Conjoon_Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class SetFlagsResultTest extends \PHPUnit_Framework_TestCase {

    protected $input;

    protected function setUp()
    {
        parent::setUp();

        $toArray = array(
            'setFlags' => true
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
        foreach ($this->input as $input) {

            $successResult = new SetFlagsResult();

            foreach ($input['data'] as $method => $result) {
                $this->assertEquals($result, $successResult->$method());
            }
        }
    }

}
