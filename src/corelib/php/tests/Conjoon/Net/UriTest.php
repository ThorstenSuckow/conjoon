<?php
/**
 * conjoon
 * (c) 2007-2014 conjoon.org
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

    protected $dataWithQuery;

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

        $this->dataWithQuery = array(
            'input' => array(
                'scheme' => 'HTTP',
                'host' => 'tEst.doMain',
                'port' => 80,
                'path' => '/test/path/',
                'query' => 'somevar=1&somEvar2=2'
            ),
            'output' => array(
                'scheme' => 'http',
                'host' => 'test.domain',
                'port' => 80,
                'path' => '/test/path/',
                'query' => 'somevar=1&somEvar2=2'
            )
        );
    }

    /**
     * @ticket CN-797
     */
    public function testWithQuery() {
        $uri = new \Conjoon\Net\Uri($this->dataWithQuery['input']);

        foreach ($this->dataWithQuery['output'] as $key => $value) {
            $methodGet = "get" . ucfirst($key);
            $this->assertSame($value, $uri->$methodGet());
        }

        $uri = new \Conjoon\Net\Uri($this->dataWithQuery['input']);

        $query = 'foo';
        $uri2 = $uri->setQuery($query);

        $this->assertTrue($uri2 instanceof \Conjoon\Net\Uri);

        foreach ($this->dataWithQuery['output'] as $key => $value) {
            $methodGet = "get" . ucfirst($key);
            $this->assertSame($value, $uri->$methodGet());
        }

        foreach ($this->dataWithQuery['output'] as $key => $value) {
            if ($key == 'query') {
                $this->assertSame($query, $uri2->getQuery());
                continue;
            }
            $methodGet = "get" . ucfirst($key);
            $this->assertSame($uri->$methodGet(), $uri2->$methodGet());
        }

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
