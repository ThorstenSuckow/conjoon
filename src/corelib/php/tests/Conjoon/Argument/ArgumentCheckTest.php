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
