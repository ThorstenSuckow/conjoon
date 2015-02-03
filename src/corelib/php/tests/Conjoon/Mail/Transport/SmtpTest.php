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

namespace Conjoon\Mail\Transport;

/**
 * @see Conjoon_Mail_Transport_Smtp
 */
require_once 'Conjoon/Mail/Transport/Smtp.php';

/**
 * @package    Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class SmtpTest extends \PHPUnit_Framework_TestCase {


    public function testName()
    {
        $host = 'my.fqdn.com';

        $class = new \ReflectionClass('\Conjoon\Mail\Transport\Smtp');
        $prop = $class->getProperty('_name');
        $prop->setAccessible(true);
        $transport = new Smtp('host', array('name' => $host));

        $this->assertSame($host, $prop->getValue($transport));

    }

}
