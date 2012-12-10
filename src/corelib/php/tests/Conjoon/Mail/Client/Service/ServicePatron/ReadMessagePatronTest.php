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

    protected function setUp()
    {
        $this->input = array(
            array(
                'input' => array(
                    'message' => array(
                        'contentTextPlain' => '',
                        'contentTextHtml' => '',
                        'date' => '',
                        'to' => '',
                        'cc' => '',
                        'from' => '',
                        'bcc' => '',
                        'replyTo' => '',
                        'subject' => ''
                    )
                ),
                'output' => array(
                    'message' => array(
                        'isPlainText' => 1,
                        'body' => '',
                        'date' => '1970-01-01 00:00:00',
                        'to' => array('addresses' => array()),
                        'cc' => array('addresses' => array()),
                        'from' => array('addresses' => array()),
                        'bcc' => array('addresses' => array()),
                        'replyTo' => array('addresses' => array()),
                        'subject' => ''
                    )
                )
            )
        );

        $this->patron = new ReadMessagePatron();
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

}
