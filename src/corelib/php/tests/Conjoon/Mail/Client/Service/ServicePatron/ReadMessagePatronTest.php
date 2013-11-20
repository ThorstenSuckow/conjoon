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


namespace Conjoon\Mail\Client\Service\ServicePatron;

/**
 * @see  Conjoon\Mail\Client\Service\ServicePatron\ReadMessagePatron
 */
require_once 'Conjoon/Mail/Client/Service/ServicePatron/ReadMessagePatron.php';

/**
 * @see  Conjoon\Mail\Client\Message\Strategy\PlainReadableStrategy
 */
require_once 'Conjoon/Mail/Client/Message/Strategy/PlainReadableStrategy.php';

/**
 * @see  Conjoon\Mail\Client\Message\Strategy\HtmlReadableStrategy
 */
require_once 'Conjoon/Mail/Client/Message/Strategy/HtmlReadableStrategy.php';


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

    protected $htmlStrategy;

    protected $plainStrategy;

    protected function setUp()
    {
        $this->plainStrategy = new \Conjoon\Mail\Client\Message\Strategy\PlainReadableStrategy;
        $this->htmlStrategy = new \Conjoon\Mail\Client\Message\Strategy\HtmlReadableStrategy;

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
                        'attachments' => array()
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
        $patron = new ReadMessagePatron($this->htmlStrategy);

        $this->assertSame($patron->getReadableStrategy(), $this->htmlStrategy);
    }


}
