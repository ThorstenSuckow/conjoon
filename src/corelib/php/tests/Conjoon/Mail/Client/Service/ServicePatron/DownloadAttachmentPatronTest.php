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
