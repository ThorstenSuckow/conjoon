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


namespace Conjoon\Mail\Client\Service\ServicePatron;

/**
 * @see  Conjoon\Mail\Client\Service\ServicePatron\DownloadAttachmentPatron
 */
require_once 'Conjoon/Mail/Client/Service/ServicePatron/DownloadAttachmentPatron.php';


/**
 * @category   Conjoon
 * @package    Conjoon_Mail
 * @subpackage UnitTests
 * @group      Conjoon_Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DownloadAttachmentPatronTest extends \PHPUnit_Framework_TestCase {

    protected $input;

    protected $patron;

    protected function setUp()
    {
        $this->input = array(
            array(
                'input' => array(
                    'encoding' => 'sssfasaf',
                    'content' => 'sfasaf',
                    'contentId' => 'asf',
                    'key' => 'asf',
                    'fileName' => 'safsa',
                    'mimeType' => ''
                ),
                'output' => array(
                    'resource' => 'sfasaf',
                    'contentId' => 'asf',
                    'key' => 'asf',
                    'name' => 'safsa',
                    'mimeType' => 'text/plain'
                )
            )
        );

        $this->patron = new DownloadAttachmentPatron();
    }

    /**
     * @expectedException \Conjoon\Mail\Client\Service\ServicePatron\ServicePatronException
     */
    public function testApplyForData_Exception()
    {
        $this->patron->applyForData(array(
           'test' => array()
        ));
    }

    /**
     * Ensures everything works as expected.
     */
    public function testOk()
    {
        $this->assertEquals(
            $this->input[0]['output'],
            $this->patron->applyForData($this->input[0]['input'])
        );
    }

    /**
     * @ticket CN-804
     */
    public function test_CN_804()
    {
        $res = $this->patron->applyForData($this->input[0]['input']);

        $this->assertFalse(array_key_exists('encoding', $res));
    }

}
