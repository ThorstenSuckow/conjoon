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


namespace Conjoon\Net;

/**
 * @see Conjoon\Net\Uri
 */
require_once 'Conjoon/Net/Uri.php';


/**
 * @category   Conjoon
 * @package    Conjoon\Net
 * @subpackage UnitTests
 * @group      Conjoon\Net
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class UriTest extends \PHPUnit_Framework_TestCase {

    protected $data;

    protected $dataOnlySchemeAndHost;

    protected $dataNotLowercased;

    public function setUp() {
        parent::setUp();


        $this->data = array(
            'input' => array(
                'scheme' => 'HTTP',
                'host' => 'tEst.doMain',
                'port' => 80,
                'path' => '/test/path/'
            ),
            'output' => array(
                'scheme' => 'http',
                'host' => 'test.domain',
                'port' => 80,
                'path' => '/test/path/'
            )
        );

        $this->dataOnlySchemeAndHost = array(
            'input' => array(
                'scheme' => 'http',
                'host' => 'test.domain'
            ),
            'output' => array(
                'scheme' => 'http',
                'host' => 'test.domain',
                'port' => null,
                'path' => null
            )
        );

        $this->dataNotLowercased = array(
            'input' => array(
                'scheme' => 'http',
                'host' => 'tEst.domain',
                'path' => 'TeStPath'
            ),
            'output' => array(
                'scheme' => 'http',
                'host' => 'test.domain',
                'port' => null,
                'path' => 'TeStPath'
            )
        );
    }


    /**
     * @ticket CN-796
     */
    public function testPathNotLowercased() {
        $uri = new \Conjoon\Net\Uri($this->dataNotLowercased['input']);

        foreach ($this->dataNotLowercased['output'] as $key => $value) {
            $methodGet = "get" . ucfirst($key);
            $this->assertSame($value, $uri->$methodGet());
        }
    }

    /**
     * Ensures everything works as expected.
     */
    public function testOk()
    {
        $uri = new \Conjoon\Net\Uri($this->data['input']);

        foreach ($this->data['output'] as $key => $value) {
            $methodGet = "get" . ucfirst($key);
            $this->assertSame($value, $uri->$methodGet());
        }

        $uri = new \Conjoon\Net\Uri($this->dataOnlySchemeAndHost['input']);

        foreach ($this->dataOnlySchemeAndHost['output'] as $key => $value) {
            $methodGet = "get" . ucfirst($key);
            $this->assertSame($value, $uri->$methodGet());
        }
    }

    /**
     * @expectedException \Conjoon\Argument\InvalidArgumentException
     */
    public function testException()
    {
        $uri = new \Conjoon\Net\Uri(array('path' => '/test'));
    }

    /**
     * Ensures everything works as expected.
     */
    public function testSetPath() {

        $uri = new \Conjoon\Net\Uri($this->data['input']);

        $path = 'foo';
        $uri2 = $uri->setPath($path);

        foreach ($this->data['output'] as $key => $value) {
            $methodGet = "get" . ucfirst($key);
            $this->assertSame($value, $uri->$methodGet());
        }

        foreach ($this->data['output'] as $key => $value) {
            if ($key == 'path') {
                $this->assertSame($path, $uri2->getPath());
                continue;
            }
            $methodGet = "get" . ucfirst($key);
            $this->assertSame($uri->$methodGet(), $uri2->$methodGet());
        }

    }

}
