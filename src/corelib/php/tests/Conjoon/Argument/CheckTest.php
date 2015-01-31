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


/**
 * @see Conjoon_Argument_Check
 */
require_once 'Conjoon/Argument/Check.php';


/**
 * @category   Conjoon
 * @package    Conjoon_Filter
 * @subpackage UnitTests
 * @group      Conjoon_Filter
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Argument_CheckTest extends PHPUnit_Framework_TestCase {

    protected $_checks = array();

    /**
     *
     * @return void
     */
    public function setUp()
    {
        $this->_checks = array(
            'testString' => array(
                'testFor' => array(
                    'fail' => array(
                            // first test
                        array(
                            array(
                                'input' => array(
                                    'type'       => 'string',
                                    'allowEmpty' => false
                            )),
                            array(
                                new stdClass,
                                array(),
                                "",
                                null
                    ))),
                    'success' => array(
                        // first test
                        array(
                            array(
                                'input' => array(
                                    'type'       => 'string',
                                    'allowEmpty' => true
                            )),
                            array(
                                "",
                                0,
                                "1",
                                null,
                                "yo"
                    )))
                )
            ),
            'testInt' => array(
                'testFor' => array(
                    'fail' => array(
                        // first test
                        array(
                            array(
                                'input' => array(
                                    'type'       => 'int',
                                    'allowEmpty' => false
                                )),
                            array(
                                new stdClass,
                                array(),
                                null
                        )),
                        // second test
                        array(
                            array(
                                'input' => array(
                                    'type'        => 'int',
                                    'allowEmpty'  => false,
                                    'greaterThan' => 0
                                )),
                            array(
                                0, "0", -1, array(), null
                        )),
                    ),
                    'success' => array(
                        // first test
                        array(
                            array(
                                'input' => array(
                                    'type'       => 'int',
                                    'allowEmpty' => true
                                )),
                            array(
                                "",
                                0,
                                "1",
                                null,
                                "yo"
                        )),
                        // second test
                        array(
                            array(
                                'input' => array(
                                    'type'        => 'int',
                                    'allowEmpty'  => true,
                                    'greaterThan' => -1
                                )),
                            array(
                                "",
                                4557,
                                "1",
                                null,
                                "yo"
                        ))
                    )
                )
            )
        );


    }

    /**
     *
     * @return void
     */
    public function tearDown()
    {
    }

    /**
     * Ensures everything works as expected
     *
     */
    public function testStringException()
    {
        $tests = $this->_checks['testString']['testFor']['fail'];

        for ($i = 0, $len = count($tests); $i < $len; $i++) {
            $rule   = $tests[$i][0];
            $inputs = $tests[$i][1];

            for ($a = 0, $lena = count($inputs); $a < $lena; $a++) {
                $in = array('input' => $inputs[$a]);

                try {
                    Conjoon_Argument_Check::check($rule, $in);
                } catch (Conjoon_Argument_Exception $e) {
                    continue;
                }

                $this->fail(
                    "No Conjoon_Argument_Exception thrown for "
                    . "test $i and input $a"
                );
            }
        }
    }

    /**
     * Ensures everything works as expected
     *
     */
    public function testString()
    {
        $tests = $this->_checks['testString']['testFor']['success'];

        for ($i = 0, $len = count($tests); $i < $len; $i++) {
            $rule   = $tests[$i][0];
            $inputs = $tests[$i][1];

            for ($a = 0, $lena = count($inputs); $a < $lena; $a++) {
                $in = array('input' => $inputs[$a]);

                Conjoon_Argument_Check::check($rule, $in);

                $this->assertSame($in['input'], (string)$inputs[$a]);
            }
        }
    }

    /**
     * Ensures everything works as expected
     *
     */
    public function testIntException()
    {
        $tests = $this->_checks['testInt']['testFor']['fail'];

        for ($i = 0, $len = count($tests); $i < $len; $i++) {
            $rule   = $tests[$i][0];
            $inputs = $tests[$i][1];

            for ($a = 0, $lena = count($inputs); $a < $lena; $a++) {
                $in = array('input' => $inputs[$a]);

                try {
                    Conjoon_Argument_Check::check($rule, $in);
                } catch (Conjoon_Argument_Exception $e) {
                    continue;
                }

                $this->fail(
                    "No Conjoon_Argument_Exception thrown for "
                        . "test $i and input $a"
                );
            }
        }
    }

    /**
     * Ensures everything works as expected
     *
     */
    public function testInt()
    {
        $tests = $this->_checks['testInt']['testFor']['success'];

        for ($i = 0, $len = count($tests); $i < $len; $i++) {
            $rule   = $tests[$i][0];
            $inputs = $tests[$i][1];

            for ($a = 0, $lena = count($inputs); $a < $lena; $a++) {
                $in = array('input' => $inputs[$a]);

                Conjoon_Argument_Check::check($rule, $in);

                $this->assertSame($in['input'], (int)$inputs[$a]);
            }
        }
    }

}
