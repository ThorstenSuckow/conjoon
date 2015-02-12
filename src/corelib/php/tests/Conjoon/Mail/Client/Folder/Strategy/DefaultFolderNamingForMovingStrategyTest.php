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


namespace Conjoon\Mail\Client\Folder\Strategy;

/**
 * @see  \Conjoon\Mail\Client\Folder\Strategy\DefaultFolderNamingForMovingStrategy
 */
require_once 'Conjoon/Mail/Client/Folder/Strategy/DefaultFolderNamingForMovingStrategy.php';


/**
 * @category   Conjoon
 * @package    Conjoon_Mail
 * @subpackage UnitTests
 * @group      Conjoon_Mail
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class DefaultFolderNamingForMovingStrategyTest extends \PHPUnit_Framework_TestCase {

    protected $template = "{0} {1}";

    protected $name = "Folder";

    protected $lists = array(
        array(
            'name'   => 'Folder',
            'result' => 'Folder (2)',
            'list'   => array('Folder A', 'Folder B', 'Folder C', 'Folder (1)', 'Folder')
        ),
        array(
            'name'   => 'Folder',
            'result' => 'Folder (1)',
            'list'   => array('Folder A', 'Folder B', 'Folder C', 'Folder ')
        ),
        array(
            'name'   => 'Folder',
            'result' => 'Folder (2)',
            'list'   => array('Folder A', 'Folder B', 'Folder C', 'Folder', 'Folder ')
        ),
        array(
            'name'   => 'Folder',
            'result' => 'Folder (900)',
            'list'   => array('Folder A', 'Folder B', 'Folder C', 'Folder', 'Folder (899) ')
        )
    );


    /**
     * Ensures everything works as expected.
     */
    public function testConstruct() {

        $strategy = new DefaultFolderNamingForMovingStrategy(array(
            'template' => $this->template
        ));
    }

    /**
     * Ensures everything works as expected.
     *
     * @expectedException \Conjoon\Mail\Client\Folder\Strategy\StrategyException
     */
    public function testConstructWithException() {
        $strategy = new DefaultFolderNamingForMovingStrategy(array(
            'template' => ""
        ));
    }


    /**
     * Ensures everything works as expected.
     */
    public function testExecute() {

        $strategy = new DefaultFolderNamingForMovingStrategy(array(
            'template' => $this->template
        ));

        foreach ($this->lists as $config) {
            $result = $strategy->execute(array(
                'name' => $config['name'],
                'list' => $config['list']
            ));

            $this->assertTrue($result instanceof \Conjoon\Mail\Client\Folder\Strategy\FolderNamingForMovingStrategyResult);

            $this->assertSame($config['result'], $result->getName());
        }
    }


    /**
     * Ensures everything works as expected.
     *
     * @expectedException \Conjoon\Mail\Client\Folder\Strategy\StrategyException
     */
    public function testExecuteWithException() {

        $strategy = new DefaultFolderNamingForMovingStrategy(array(
            'template' => $this->template
        ));

        $strategy->execute(array(
            'name' => "",
            'list' => array('A', 'B','C')
        ));
    }


    /**
     * Ensures everything works as expected.
     */
    public function testGetTemplate() {

        $strategy = new DefaultFolderNamingForMovingStrategy(array(
            'template' => $this->template
        ));

        $this->assertSame($strategy->getTemplate(), $this->template);
    }

}
