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

namespace Conjoon\Util;

/**
 * @see Conjoon\Util\ArrayUtil
 */
require_once 'Conjoon/Util/ArrayUtil.php';

/**
 * @see Conjoon\Util\Array
 */
require_once 'Conjoon/Util/Array.php';


/**
 * @package Conjoon/Tests
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class ArrayUtilTest extends \PHPUnit_Framework_TestCase {

    /**
     *
     * @return void
     */
    public function setUp()
    {

    }

    /**
     *
     * @return void
     */
    public function tearDown()
    {

    }

    /**
     * Returns config for apply tests.
     *
     * @return array
     */
    protected function getApplyTests() {
        return array(
            // test #1
            array(
                'source' => array(
                    'a'     => 0,
                    '1'     => 8,
                    'hallo' => 'welt'
                ),
                'apply' => array(
                    '1'   => '9',
                    'xyz' => 'lore ipsum'
                ),
                'result' => array(
                    'a'     => 0,
                    '1'   => '9',
                    'hallo' => 'welt',
                    'xyz' => 'lore ipsum'
                )
            )
        );
    }

// +---------------------------------------------------------------------------
// | Tests
// +---------------------------------------------------------------------------


    /**
     * Ensure everything works as expected.
     */
    public function testApply() {

        $applyTests = $this->getApplyTests();

        foreach ($applyTests as $test) {

            $source = $test['source'];
            $apply = $test['apply'];
            $result = $test['result'];

            ArrayUtil::apply($source, $apply);

            foreach ($result as $key => $value) {
                $this->assertArrayHasKey($key, $source);
                $this->assertSame($value, $source[$key]);
            }
        }
    }

// -------- legacy tests

    /**
     * Ensure everything works as expected.
     */
    public function testApply_legacy() {

        $applyTests = $this->getApplyTests();

        foreach ($applyTests as $test) {

            $source = $test['source'];
            $apply = $test['apply'];
            $result = $test['result'];

            \Conjoon_Util_Array::apply($source, $apply);

            foreach ($result as $key => $value) {
                $this->assertArrayHasKey($key, $source);
                $this->assertSame($value, $source[$key]);
            }
        }
    }
}
