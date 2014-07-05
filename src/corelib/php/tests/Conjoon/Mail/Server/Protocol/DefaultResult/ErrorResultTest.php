<?php
/**
 * conjoon
 * (c) 2007-2014 conjoon.org
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
