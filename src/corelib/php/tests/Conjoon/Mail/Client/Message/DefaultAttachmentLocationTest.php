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


namespace Conjoon\Mail\Client\Message;

/**
 * @see Conjoon\Mail\Client\Message\DefaultAttachmentLocation
 */
require_once 'Conjoon/Mail/Client/Message/DefaultAttachmentLocation.php';


/**
 * @category   Conjoon
 * @package    Conjoon_Mail
 * @subpackage UnitTests
 * @group      Conjoon_Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultAttachmentLocationTest extends \PHPUnit_Framework_TestCase {

    protected $messageLocation;

    protected function setUp()
    {
        parent::setUp();

        $this->messageLocation =
            new \Conjoon\Mail\Client\Message\DefaultMessageLocation(
            new \Conjoon\Mail\Client\Folder\Folder(
            new \Conjoon\Mail\Client\Folder\DefaultFolderPath(
                '["1", "2"]'
            )), "1"
        );
    }

    /**
     * @expectedException \Conjoon\Argument\InvalidArgumentException
     */
    public function testConstructor_Exception()
    {
        new DefaultAttachmentLocation($this->messageLocation, "");
    }

    /**
     * Ensures everything works as expected.
     */
    public function testOk()
    {
        $loc = new DefaultAttachmentLocation($this->messageLocation, 1);

        $this->assertSame($this->messageLocation, $loc->getMessageLocation());
        $this->assertSame("1", $loc->getIdentifier());

    }

}
