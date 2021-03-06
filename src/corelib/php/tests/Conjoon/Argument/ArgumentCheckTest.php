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

namespace Conjoon\Argument;

/**
 * @see Conjoon\Argument\ArgumentCheck
 */
require_once 'Conjoon/Argument/ArgumentCheck.php';


/**
 * @category   Conjoon
 * @package    Conjoon_Filter
 * @subpackage UnitTests
 * @group      Conjoon_Filter
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class ArgumentCheckTest extends \PHPUnit_Framework_TestCase {

    protected $_checks = array();

    /**
     *
     * @return void
     */
    public function setUp()
    {
        $this->_checks = array(
            'testBool' => array(
                'testFor' => array(
                    'fail' => array(
                        // first test
                        array(
                            array(
                                'input' => array(
                                    'type'       => 'bool',
                                    'allowEmpty' => false,
                                    'strict'     => true
                                )),
                            array(
                                new \stdClass,
                                array(),
                                "",
                                null,
                                1,
                                0,
                                "1",
                                "0"
                            )),
                        //second test
                        array(
                            array(
                                'input' => array(
                                    'type'       => 'bool',
                                    'allowEmpty' => false,
                                    'strict'     => true
                                )),
                            array(
                                null
                            )),
                        array(
                            array(
                                'input' => array(
                                    'type'       => 'bool',
                                    'allowEmpty' => false,
                                    'strict'     => false
                                )),
                            array(
                                null
                            ))
                    ),
                    'success' => array(
                        // first test
                        array(
                            array(
                                'input' => array(
                                    'type'       => 'bool',
                                    'allowEmpty' => false
                                )),
                            array(
                                true,
                                false
                            )),
                        // second test
                        array(
                            array(
                                'input' => array(
                                    'type'       => 'bool',
                                    'allowEmpty' => true
                                )),
                            array(
                                true,
                                false
                            )),
                        // third test
                        array(
                            array(
                                'input' => array(
                                    'type'       => 'bool',
                                    'allowEmpty' => true,
                                    'strict'     => true
                                )),
                            array(
                                true,
                                false
                            )),
                        // fourth test
                        array(
                            array(
                                'input' => array(
                                    'type'       => 'bool',
                                    'allowEmpty' => true
                                )),
                            array(
                                0,
                                1
                            )),
                        // fifth test
                        array(
                            array(
                                'input' => array(
                                    'type'       => 'bool',
                                    'allowEmpty' => true,
                                    'strict'     => false
                                )),
                            array(
                                1,
                                0
                            )),
                        // sixth test
                        array(
                            array(
                                'input' => array(
                                    'type'       => 'bool',
                                    'allowEmpty' => true,
                                    'strict'     => true
                                )),
                            array(
                                null
                            ))
                    )
                )
            ),
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
                                new \stdClass,
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
                        )),
                        // second test
                        array(
                            array(
                                'input' => array(
                                    'type'       => 'string',
                                    'allowEmpty' => true,
                                    'strict'     => true
                                )),
                            array(
                                null
                            ))
                    )
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
                                new \stdClass,
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
                        // third test
                        array(
                            array(
                                'input' => array(
                                    'type'        => 'int',
                                    'allowEmpty'  => false,
                                    'greaterThan' => 3
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
                        )),
                        // third test
                        array(
                            array(
                                'input' => array(
                                    'type'        => 'integer',
                                    'allowEmpty'  => true,
                                    'greaterThan' => 3
                                )),
                            array(
                                4557,
                                "5",
                                "8yo",
                                "",
                                null
                            )),
                        // fourth test
                        array(
                            array(
                                'input' => array(
                                    'type'        => 'integer',
                                    'allowEmpty'  => false,
                                    'greaterThan' => 3
                                )),
                            array(
                                4557,
                                "5",
                                "8yo"
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
     * @ticket CN-961
     */
    public function test_CN967() {

        $dataOk = array('value' => array(1, 2, 3));
        ArgumentCheck::check(array(
            'value' =>  array(
                'type'      => 'array',
                'minLength' => 3
            )), $dataOk);
        $this->assertSame(3, count($dataOk['value']));

        $dataOk = array('value' => array(1, 2, 3));
        $ce = null;
        try {
        ArgumentCheck::check(array(
            'value' =>  array(
                'type'      => 'array',
                'minLength' => 4
            )), $dataOk);
        } catch (\Exception $e) {
            $ce = $e;
        }
        $this->assertInstanceOf('\Conjoon\Argument\InvalidArgumentException', $ce);
        // ---
        $dataOk = array('value' => array(
            new \stdClass, new \stdClass, new \stdClass));
        ArgumentCheck::check(array(
            'value' =>  array(
                'type'      => 'arrayType',
                'class'     => '\stdClass',
                'minLength' => 3
            )), $dataOk);
        $this->assertSame(3, count($dataOk['value']));

        $dataOk = array('value' => array(
            new \stdClass, new \stdClass, new \stdClass));
        $ce = null;
        try {
            ArgumentCheck::check(array(
                'value' =>  array(
                    'type'      => 'arrayType',
                    'class'     => '\stdClass',
                    'minLength' => 4
                )), $dataOk);
        } catch (\Exception $e) {
            $ce = $e;
        }

        $this->assertInstanceOf('\Conjoon\Argument\InvalidArgumentException', $ce);

    }


        /**
     * @ticket CN-961
     */
    public function test_CN961() {

        $dataOk = array('value' => null);

        ArgumentCheck::check(array(
            'value' =>  array(
                    'type'        => 'int',
                    'greaterThan' => 3,
                    'allowEmpty' => true
        )), $dataOk);

        $this->assertSame(0, $dataOk['value']);

        // ----------
        $dataOk = array('value' => null);
        $exp = null;
        try{
            ArgumentCheck::check(array(
                'value' =>  array(
                    'type'        => 'int',
                    'greaterThan' => 5,
                    'allowEmpty' => false
                )), $dataOk);

        } catch (\Conjoon\Argument\InvalidArgumentException $e) {
            $exp = $e;
        }
        $this->assertNotNull($exp);

        // ----------
        $dataOk = array('value' => 4);
        $exp = null;
        try{
            ArgumentCheck::check(array(
                'value' =>  array(
                    'type'        => 'int',
                    'greaterThan' => 5,
                    'allowEmpty' => true
                )), $dataOk);

        } catch (\Conjoon\Argument\InvalidArgumentException $e) {
            $exp = $e;
        }

        $this->assertNotNull($exp);
    }

    /**
     * @ticket CN-942
     */
    public function test_CN942_InvalidArgumentException_BecauseOfMisplacedConfig() {

        $dataOk = array('values' => array(
            1, 2, 3, 4, 5
        ));

        $config = array(
            'values' =>  array(
                array(
                    'type'  => 'int'
                ),
                array(
                    'type'  => 'string'
                ),
            ),
        );

        $str = "";

        try {
            ArgumentCheck::check($config, $dataOk);
        } catch (\Conjoon\Argument\InvalidArgumentException $e) {
            $str = $e->getMessage();
        }
        $this->assertTrue(strpos($str, "Boolean Operator expected, got") === 0);

    }

    /**
     * @ticket CN-942
     */
    public function test_CN942_InvalidArgumentException_BecauseOfMisplacedBooleanArgument() {

        $dataOk = array('values' => array(
            1, 2, 3, 4, 5
        ));

        $config = array(
            'values' =>  array(
                'OR',
                array(
                    'type'  => 'int'
                ),
                array(
                    'type'  => 'string'
                ),
            ),
        );

        $str = "";

        try {
            ArgumentCheck::check($config, $dataOk);
        } catch (\Conjoon\Argument\InvalidArgumentException $e) {
            $str = $e->getMessage();
        }
        $this->assertTrue(strpos($str, "Configuration expected, got") === 0);

    }

    /**
     * @ticket CN-942
     */
    public function test_CN942_InvalidArgumentException_BecauseOfWrongBooleanOperator() {

        $dataOk = array('values' => array(
            1, 2, 3, 4, 5
        ));

        $str = "";

        try {
            ArgumentCheck::check(array(
                'values' =>  array(
                    array(
                        'type'  => 'int'
                    ),
                    'AND',
                    array(
                        'type'  => 'string'
                    ),
                ),
            ), $dataOk);
        } catch (\Conjoon\Argument\InvalidArgumentException $e) {
            $str = $e->getMessage();
        }

        $this->assertTrue(strpos($str, "'OR' expected, got") === 0);

    }

    /**
     * @ticket CN-942
     * @expectedException \Conjoon\Argument\InvalidArgumentException
     */
    public function test_CN942_InvalidArgumentException_BecauseOfArgument() {

        $dataOk = array('values' => array(
            1, 2, 3, 4, 5
        ));

        ArgumentCheck::check(array(
            'values' =>  array(
                array(
                    'type'  => 'int'
                ),
                'OR',
                array(
                    'type'  => 'string'
                ),
            ),
        ), $dataOk);
    }


    /**
     * @ticket CN-942
     */
    public function test_CN942() {

        $dataOk = array('values' => array(
            1, 2, 3, 4, 5
        ));

        ArgumentCheck::check(array(
            'values' =>  array(
                array(
                    'type'  => 'array'
                ),
                'OR',
                array(
                    'type'  => 'string'
                )
            ),
        ), $dataOk);
    }

    /**
     * @ticket CN-926
     */
    public function test_CN926() {

        $dataOk = array('values' => array(
            1, 2, 3, 4, 5
        ));

        $emptyArray = array('values' => array());

        $emptyValue = array('values' => null);

        ArgumentCheck::check(array(
            'values' => array(
                'type'  => 'array'
            )
        ), $dataOk);

        ArgumentCheck::check(array(
            'values' => array(
                'type'  => 'array',
                'allowEmpty' => false
            )
        ), $emptyArray);

        ArgumentCheck::check(array(
            'values' => array(
                'type'  => 'array',
                'allowEmpty' => true,
            )
        ), $emptyValue);

        $ee = null;

        try {
            ArgumentCheck::check(array(
                'values' => array(
                    'type'  => 'array',
                    'allowEmpty' => false,
                )
            ), $emptyValue);
        } catch (\Exception $ee) {
            // ignore
        }
        $this->assertTrue($ee instanceof \Conjoon\Argument\InvalidArgumentException);

    }

    /**
     * @ticket CN-925
     */
    public function test_CN925() {

        $dataOk = array('values' => array(
            0 => new \stdClass(),
            110 => new \stdClass(),
            12 => new \stdClass(),
            2220 => new \stdClass(),
            '0' => new \stdClass()
        ));
        $ee = null;
        try {
            ArgumentCheck::check(array(
                'values' => array(
                    'type'  => 'arrayType',
                    'class' => '\stdClass'
                )
            ), $dataOk);
        } catch (\Exception $ee) {
            $this->fail("Unexpected exception");
        }


        $notOkayValues = array('values' => 'meh.');
        $ee = null;
        try {
            ArgumentCheck::check(array(
                'values' => array(
                    'type'  => 'arrayType',
                    'class' => '\stdClass'
                )
            ), $notOkayValues);
        } catch (\Exception $ee) {
            //
        }
        $this->assertTrue($ee instanceof \Conjoon\Argument\InvalidArgumentException);


        $dataNotOk = array('values' => array(
            0 => new \stdClass(),
            110 => new \stdClass(),
            12 => new \stdClass(),
            2220 => 'new \stdClass()',
            '0' => new \stdClass()
        ));
        $ee = null;
        try {
            ArgumentCheck::check(array(
                'values' => array(
                    'type'  => 'arrayType',
                    'class' => '\stdClass'
                )
            ), $dataNotOk);
        } catch (\Exception $ee) {
            //
        }
        $this->assertTrue($ee instanceof \Conjoon\Argument\InvalidArgumentException);

    }

    /**
     * @ticket CN-910
     */
    public function testNotForceEmptyString() {

        $data = array('str' => " \n");

        $ee = null;

        try {
            ArgumentCheck::check(array(
                'str' => array(
                    'type' => 'string',
                    'allowEmpty' => false
                )
            ), $data);
        } catch (\Exception $ee) {
            // ignore
        }

        $this->assertTrue($ee instanceof \Conjoon\Argument\InvalidArgumentException);

        $cmpStr = "\ntest\n";
        $data = array('str' => $cmpStr);
        ArgumentCheck::check(array(
            'str' => array(
                'type' => 'string',
                'allowEmpty' => true
            )
        ), $data);

        $this->assertEquals($cmpStr, $data['str']);

        $cmpStr = " \n";
        $data = array('str' => $cmpStr);
        ArgumentCheck::check(array(
            'str' => array(
                'type' => 'string',
                'allowEmpty' => true
            )
        ), $data);

        $this->assertEquals($cmpStr, $data['str']);

    }

    /**
     * @ticket CN-651
     */
    public function testInstanceOfClassAndString() {

        $cl = new \stdClass();
        $data = array('cl' => $cl);

        try {
            ArgumentCheck::check(array(
                'cl' => array(
                    'type' => 'instanceof',
                    'class' => '\stdClass'
                )
            ), $data);
        } catch (\Exception $e) {
            $this->fail("Unexpected exception.");
        }

        $oE = null;
        try {
            ArgumentCheck::check(array(
                'cl' => array(
                    'type' => 'instanceof',
                    'class' => 'noneExistingClass'
                )
            ), $data);
        } catch (\Exception $e) {
           $oE = $e;
        }

        $this->assertTrue($oE instanceof \Exception);
    }

    /**
     * @ticket CN-810
     */
    public function testStrict() {
        $data = array('str' => 2);
        $exc = null;
        try {
            ArgumentCheck::check(array(
                'str' => array(
                    'type'       => 'string',
                    'allowEmpty' => false,
                    'strict'  => true
                )
            ), $data);
        } catch (\Exception $e) {
            $exc = $e;
        }

        $this->assertTrue($exc instanceof \Conjoon\Argument\InvalidArgumentException);

        $data = array('intval' => "2");
        $exc = null;
        try {
            ArgumentCheck::check(array(
                'intval' => array(
                    'type'       => 'int',
                    'allowEmpty' => false,
                    'strict'  => true
                )
            ), $data);
        } catch (\Exception $e) {
            $exc = $e;
        }

        $this->assertTrue($exc instanceof \Conjoon\Argument\InvalidArgumentException);

        $data = array('str' => "2", 'intval' => 2);

        ArgumentCheck::check(array(
            'intval' => array(
                'type'       => 'int',
                'allowEmpty' => false,
                'strict'  => true
            ),
            'str' => array(
                'type'       => 'string',
                'allowEmpty' => false,
                'strict'  => true
            )
        ), $data);

        $this->assertSame("2", $data['str']);
        $this->assertSame(2, $data['intval']);

    }

    /**
     * @ticket CN-791
     */
    public function testMandatoryFalse_Default() {
        $data = array();

        ArgumentCheck::check(array(
            'test' => array(
                'type'       => 'string',
                'allowEmpty' => false,
                'mandatory'  => false,
                'default' => 'YO'
            )
        ), $data);

        $this->assertTrue(array_key_exists('test', $data));
        $this->assertSame($data['test'], 'YO');
    }

    /**
     * @expectedException \Conjoon\Argument\InvalidArgumentException
     *
     * @ticket CN-794
     */
    public function testNotMandatoryAndDefaultWithNullValueAndAllowEmptyFalse()
    {
        $data = array('somekey' => 'somevalue');

        ArgumentCheck::check(array(
            'test' => array(
                'type'       => 'string',
                'allowEmpty' => false,
                'mandatory'  => false,
                'default' => null
            )
        ), $data);

    }

    /**
     *
     *
     * @ticket CN-793
     */
    public function testNotMandatoryAndDefaultWithNullValue()
    {
        $data = array('somekey' => 'somevalue');

        ArgumentCheck::check(array(
            'test' => array(
                'type'       => 'string',
                'allowEmpty' => true,
                'mandatory'  => false,
                'default' => null
            )
        ), $data);

        $this->assertTrue(array_key_exists('test', $data));
        $this->assertNull($data['test']);
    }

    /**
     * @expectedException \Conjoon\Argument\InvalidArgumentException
     *
     * @ticket CN-694
     */
    public function testMandatory_True()
    {
        $data = array('somekey' => 'somevalue');

        ArgumentCheck::check(array(
            'test' => array(
                'type'       => 'string',
                'allowEmpty' => false,
                'mandatory'  => true
            )
        ), $data);
    }

    /**
     * Ensures everything works as expected.
     *
     * @ticket CN-694
     */
    public function testMandatory_False()
    {
        $data = array('somekey' => 'somevalue');

        ArgumentCheck::check(array(
            'test' => array(
                'type'       => 'string',
                'allowEmpty' => false,
                'mandatory'  => false
            )
        ), $data);
    }

    /**
     * Ensures everything works as expected
     *
     * @ticket CN-704
     */
    public function testBooleanException()
    {
        $tests = $this->_checks['testBool']['testFor']['fail'];

        for ($i = 0, $len = count($tests); $i < $len; $i++) {
            $rule   = $tests[$i][0];
            $inputs = $tests[$i][1];

            for ($a = 0, $lena = count($inputs); $a < $lena; $a++) {
                $in = array('input' => $inputs[$a]);

                try {
                    ArgumentCheck::check($rule, $in);
                } catch (\Exception $e) {
                    $this->assertTrue($e instanceof InvalidArgumentException);
                    continue;
                }

                $this->fail(
                    "No InvalidArgumentException thrown for "
                        . "test $i and input $a"
                );
            }
        }
    }

    /**
     * Ensures everything works as expected
     *
     * @ticket CN-704
     */
    public function testBooleanSuccess()
    {
        $tests = $this->_checks['testBool']['testFor']['success'];

        for ($i = 0, $len = count($tests); $i < $len; $i++) {
            $rule   = $tests[$i][0];
            $inputs = $tests[$i][1];

            for ($a = 0, $lena = count($inputs); $a < $lena; $a++) {
                $in = array('input' => $inputs[$a]);

                ArgumentCheck::check($rule, $in);

                /**
                 * @ticket CN-979
                 */
                if (isset($rule['input']['strict']) &&
                    $rule['input']['strict'] === true &&
                    isset($rule['input']['allowEmpty']) &&
                    $rule['input']['allowEmpty'] === true &&
                    $inputs[$a] === null) {
                    $this->assertNull($in['input']);
                } else {
                    $this->assertSame($in['input'], (bool)$inputs[$a]);
                }


            }
        }
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
                    ArgumentCheck::check($rule, $in);
                } catch (InvalidArgumentException $e) {
                    continue;
                }

                $this->fail(
                    "No InvalidArgumentException thrown for "
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

                ArgumentCheck::check($rule, $in);

                /**
                 * @ticket CN-979
                 */
                if (isset($rule['input']['strict']) &&
                    $rule['input']['strict'] === true &&
                    isset($rule['input']['allowEmpty']) &&
                    $rule['input']['allowEmpty'] === true &&
                    $inputs[$a] === null) {
                    $this->assertNull($in['input']);
                } else {
                    $this->assertSame($in['input'], (string)$inputs[$a]);
                }
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
                    ArgumentCheck::check($rule, $in);
                } catch (InvalidArgumentException $e) {
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

                ArgumentCheck::check($rule, $in);

                $this->assertSame($in['input'], (int)$inputs[$a]);
            }
        }
    }

}
