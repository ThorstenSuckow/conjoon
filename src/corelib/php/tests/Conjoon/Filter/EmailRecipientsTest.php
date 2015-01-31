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
 * @see Conjoon_Filter_EmailRecipients
 */
require_once 'Conjoon/Filter/EmailRecipients.php';


/**
 * @category   Conjoon
 * @package    Conjoon_Filter
 * @subpackage UnitTests
 * @group      Conjoon_Filter
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Filter_EmailRecipientsTest extends PHPUnit_Framework_TestCase {

    protected $filter;

    protected $input;

    public function setUp()
    {
        parent::setUp();

        $this->input = array(array(
            'input' => array(
                "\"Thorsten Suckow-Homberg\" <tsuckow@conjoon.org>, yo@mtv.com",
                "\"Pit Bull\" <pit@doggydog.com>"
            ),
            'output' => array(
                array("tsuckow@conjoon.org", "Thorsten Suckow-Homberg"),
                array("yo@mtv.com"),
                array("pit@doggydog.com", "Pit Bull"),
            )
        ));

        $this->filter = new Conjoon_Filter_EmailRecipients();
    }

    /**
     * Ensure everything works as expected.
     */
    public function testOk()
    {
        foreach ($this->input as $values) {

            $this->assertEquals(
                $values['output'],
                $this->filter->filter($values['input'])
            );

        }
    }

}
