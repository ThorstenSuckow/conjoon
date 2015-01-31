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
 * @see  Conjoon\Mail\Client\Service\ServicePatron\ReadMessagePatron
 */
require_once 'Conjoon/Mail/Client/Service/ServicePatron/ReadMessagePatron.php';

/**
 * @see  Conjoon\Mail\Client\Message\Strategy\DefaultPlainReadableStrategy
 */
require_once 'Conjoon/Mail/Client/Message/Strategy/DefaultPlainReadableStrategy.php';




/**
 * @category   Conjoon
 * @package    Conjoon_Mail
 * @subpackage UnitTests
 * @group      Conjoon_Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class ReadMessagePatronTest extends \PHPUnit_Framework_TestCase {

    protected $input;

    protected $patron;

    protected $date;

    protected $compDate;

    protected $plainStrategy;

    protected function setUp()
    {
        $this->plainStrategy = new \Conjoon\Mail\Client\Message\Strategy\DefaultPlainReadableStrategy;

        $this->date  = new \DateTime('1970-01-01 00:00:00', new \DateTimeZone('UTC'));
        $this->date->setTimezone(new \DateTimeZone(date_default_timezone_get()));
        $this->compDate = $this->date->format('Y-m-d H:i:s');
        $this->date->setTimezone(new \DateTimeZone('UTC'));

        $this->input = array(
            array(
                'input' => array(
                    'message' => array(
                        'contentTextPlain' => '',
                        'contentTextHtml' => '',
                        'date' => $this->date,
                        'to' => '',
                        'cc' => '',
                        'from' => '',
                        'bcc' => '',
                        'replyTo' => '',
                        'subject' => '',
                        'attachments' => array()
                    )
                ),
                'output' => array(
                    'message' => array(
                        'isPlainText' => 1,
                        'body' => '',
                        'date' => $this->compDate,
                        'to' => array('addresses' => array()),
                        'cc' => array('addresses' => array()),
                        'from' => array('addresses' => array()),
                        'bcc' => array('addresses' => array()),
                        'replyTo' => array('addresses' => array()),
                        'subject' => '',
                        'attachments' => array(),
                        'hasResources' => false,
                        'resourcesLoaded' => false
                    )
                )
            )
        );

        $this->patron = new ReadMessagePatron($this->plainStrategy);
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
     * Ensures everything works as expected.
     */
    public function testGetReadableStrategy()
    {
        $patron = new ReadMessagePatron($this->plainStrategy);

        $this->assertSame($patron->getReadableStrategy(), $this->plainStrategy);
    }


}
