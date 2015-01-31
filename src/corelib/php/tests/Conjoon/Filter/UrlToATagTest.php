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
 * $Author: T. Suckow $
 * $Id: ShortenStringTest.php 1985 2014-07-05 13:00:08Z T. Suckow $
 * $Date: 2014-07-05 15:00:08 +0200 (Sa, 05 Jul 2014) $
 * $Revision: 1985 $
 * $LastChangedDate: 2014-07-05 15:00:08 +0200 (Sa, 05 Jul 2014) $
 * $LastChangedBy: T. Suckow $
 * $URL: http://svn.conjoon.org/trunk/src/corelib/php/tests/Conjoon/Filter/ShortenStringTest.php $
 */


/**
 * @see Conjoon_Filter_UrlToATag
 */
require_once 'Conjoon/Filter/UrlToATag.php';


/**
 * @category   Conjoon
 * @package    Conjoon_Filter
 * @subpackage UnitTests
 * @group      Conjoon_Filter
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Filter_UrlToATagTest extends PHPUnit_Framework_TestCase {

    protected $inputOutput = array();

    /**
     * Conjoon_Filter_UrlToATagTest object
     *
     * @var Conjoon_Filter_UrlToATagTest
     */
    protected $_filter;

    /**
     * Creates a new Conjoon_Filter_UrlToATagTest object for each test method
     *
     * @return void
     */
    public function setUp(array $attributes = array())
    {
        $attributeString = "";

        if (!empty($attributes)) {
            $keyValues = array();
            foreach ($attributes as $key => $value) {
                $keyValues[] = $key . '="'.$value.'"';
            }
            $attributeString = implode(' ', $keyValues) . ' ';
        }

        $this->inputOutput = array(
            'Hallo www.conjoon.org test text http://testest.de' =>
                'Hallo <a '.$attributeString.'href="http://www.conjoon.org">www.conjoon.org</a> '.
                'test text <a '.$attributeString.'href="http://testest.de">http://testest.de</a>',

            'Hallo ftp://www.conjoon.org test text testest.de' =>
                'Hallo <a '.$attributeString.'href="ftp://www.conjoon.org">ftp://www.conjoon.org</a> '.
                'test text testest.de',

            'Hallo https://www.conjoon.org test text http://testest.de' =>
                'Hallo <a '.$attributeString.'href="https://www.conjoon.org">https://www.conjoon.org</a> '.
                'test text <a '.$attributeString.'href="http://testest.de">http://testest.de</a>',

            'Hallo http://www.conjoon.org test text http://testest.de' =>
                'Hallo <a '.$attributeString.'href="http://www.conjoon.org">http://www.conjoon.org</a> '.
                'test text <a '.$attributeString.'href="http://testest.de">http://testest.de</a>',

        );

        $this->_filter = new Conjoon_Filter_UrlToATag($attributes);
    }

    /**
     * Ensures everything works as expected
     *
     * @return void
     */
    public function testFilterNoConstructArguments()
    {
        foreach ($this->inputOutput as $input => $output) {
            $this->assertEquals($output, $this->_filter->filter($input));
        }
    }

    /**
     * Ensures everything works as expected
     *
     * @return void
     */
    public function testFilterWithAttributes()
    {
        $this->setUp(array(
            'target' => '_blank',
            'class'  => 'cssclass'
        ));

        foreach ($this->inputOutput as $input => $output) {
            $this->assertEquals($output, $this->_filter->filter($input));
        }
    }

}