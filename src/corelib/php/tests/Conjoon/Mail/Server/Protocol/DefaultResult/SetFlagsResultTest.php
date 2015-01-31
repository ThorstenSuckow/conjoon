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
