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

/**
 * Utility methods for working with arrays.
 *
 * @package Conjoon_Util
 * @subpackage Util
 * @category Util
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Util_Array  {

    /**
     * Enforce static behavior.
     */
    private function __construct()
    {

    }

    /**
     * Applies the key/value pairs from array 2 to array 1.
     * Overwrites existing keys.
     *
     * @param array $array1
     * @param array $array2
     *
     */
    public static function apply(Array &$array1, Array $array2)
    {
        self::_apply($array1, $array2, false);
    }

    /**
     * Applies the key/value pairs from array 2 to array 1, if, and only
     * if the key for the value exists in $array1.
     *
     * @param array $array1
     * @param array $array2
     *
     */
    public static function applyStrict(Array &$array1, Array $array2)
    {
        self::_apply($array1, $array2, true);
    }

    /**
     * Applies the key/value pairs from array 2 to array 1, if, and only
     * if this key is not already set in array 1.
     *
     * @param array $array1
     * @param array $array2
     *
     */
    public static function applyIf(Array &$array1, Array $array2)
    {
        self::_apply($array1, $array2, false, true);
    }

    private static function _apply(Array &$array1, Array $array2, $strict = false, $preserveExisting = false)
    {
        foreach ($array2 as $key => $value) {
            if ($strict && !array_key_exists($key, $array1)) {
                continue;
            }

            if ($preserveExisting && isset($array1[$key])) {
                continue;
            }

            $array1[$key] = $value;
        }
    }

    /**
     * Camelizes the keys of an assoziative array and returns
     * the array with the new keys, if the second parameter is set to "true".
     * Otherwise, the reference to the passed argument will be used and the array
     * will be changed directly.
     *
     * @param Array $array The associative array to camelize the keys.
     * @param boolean $return "true" for return a copy of the array with
     * the camelized keys, otherwise false
     *
     * @return Array The new array with the keys camelized or null, if the
     * reference is used
     *
     * @deprecated use camelizeKeys2 instead
     */
    public static function camelizeKeys(Array &$array, $return = false)
    {
        if ($return !== true) {
            $keys = array_keys($array);
            $values = array_values($array);
            foreach ($keys as $k => $v) {
                $camelKey = '_' . str_replace('_', ' ', strtolower($v));
                $camelKey = ltrim(str_replace(' ', '', ucwords($camelKey)), '_');
                $keys[$k] = $camelKey;
            }

            $array = array_combine($keys, $values);
            return null;
        } else {
            $data = array();
            foreach ($array as $key => $value) {
                $camelKey = '_' . str_replace('_', ' ', strtolower($key));
                $camelKey = ltrim(str_replace(' ', '', ucwords($camelKey)), '_');

                $data[$camelKey] = $value;
            }

            return $data;
        }
    }

    /**
     * Underscores the keys of an assoziative array (if camelized) and returns
     * the array with the new keys, if the second parameter is set to "true".
     * Otherwise, the reference to the passed argument will be used and the array
     * will be changed directly.
     *
     * @param Array $array The associative array to underscore the keys.
     * @param boolean $return "true" for return a copy of the array with
     * the underscored keys, otherwise false
     *
     * @return Array The new array with the keys underscored or null, if the
     * reference is used
     */
    public static function underscoreKeys(Array &$array, $return = false)
    {
        if ($return !== true) {

            $keys   = array_keys($array);
            $values = array_values($array);
            foreach ($keys as $k => $v) {
                $keys[$k] = strtolower(preg_replace('/([a-z])([A-Z])/', "$1_$2", $v));
            }

            $array = array_combine($keys, $values);
            return null;
        } else {
            $data = array();

            foreach ($array as $key => $value) {
                $data[strtolower(preg_replace('/([a-z])([A-Z])/', "$1_$2", $key))] = $value;
            }

            return $data;
        }
    }

    /**
     * Checks wether the passed argument is an associative array.
     *
     * @param array $array The array to check for associative keys
     *
     * @return bollean "true", if the array is associative, otherwise
     * "false"
     */
    public static function isAssociative(array $array)
    {
        return count($array) !== array_reduce(
            array_keys($array),
            array('Conjoon_Util_Array', '_isAssocCallback'),
            0
        );
    }

    /**
     * Callback for array_reduce-function used in isAssociative
     */
    private static function _isAssocCallback($a, $b)
    {
        return $a === $b ? $a + 1 : 0;
    }

    /**
     * Checks whether a given array provides the listed keys.
     * The method will eitehr return true if all keys are available or
     * the name of the first key that was missing.
     *
     * @param array $array The associative array to check
     * @param array $keys The list of keys to check for availability in
     * $array
     *
     * @return boolean|string either true if none of the keys in $list
     * was missing as a key in $array, or the name of the first key that
     * was found missing
     */
    public static function arrayKeysExist(Array $array, Array $keys)
    {
        foreach ($keys as $key) {
            if (!array_key_exists($key, $array)) {
                return $key;
            }
        }

        return true;
    }

    /**
     * Extracts the specified keys from the array and saves them into a new array
     * with their according values.
     *
     * @param array $array The associative array to extract data from
     * @param array $keys The list of keys which values should be extracted from
     * $array
     *
     * @return boolean|array either false if a key was missing in $array, or
     * a new array with the extracted values
     */
    public static function extractByKeys(Array $array, Array $keys)
    {
        $new = array();
        foreach ($keys as $key) {
            if (!array_key_exists($key, $array)) {
                return false;
            }
            $new[$key] = $array[$key];
        }

        return $new;
    }
}