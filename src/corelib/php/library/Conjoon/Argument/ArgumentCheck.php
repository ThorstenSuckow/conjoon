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
 * @see Conjoon\Argument\InvalidArgumentException
 */
require_once 'Conjoon/Argument/InvalidArgumentException.php';

/**
 *
 *
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class ArgumentCheck {

    private function __construct(){}

    private function __clone(){}

    /**
     *
     *
     * @throws Conjoon_Argument_Exception
     */
    public static function check(Array $config, Array &$data)
    {

        foreach ($config as $argumentName => $entityConfig) {

            if (is_array($entityConfig) && array_key_exists(0, $entityConfig)) {
                $stack = array();
                $conditionMod = true;
                foreach ($entityConfig as $configEntry) {

                    if (is_array($configEntry)) {
                        if ($conditionMod === false) {
                            throw new InvalidArgumentException(
                                "Boolean Operator expected, got Configuration"
                            );
                        }
                        try {
                            self::check(
                                array($argumentName => $configEntry),
                                $data
                            );
                            $stack[] = true;
                            // we only consider "OR" for now,
                            // so break foreach and succeed
                            break;
                        } catch (\Exception $e) {
                            $stack[] = $e;
                        }
                        $conditionMod = false;
                    } else {
                        if ($conditionMod === true) {
                            throw new InvalidArgumentException(
                                "Configuration expected, got $configEntry"
                            );
                        }
                        // $configEntry == 'OR'
                        if ($configEntry !== 'OR') {
                            throw new InvalidArgumentException(
                                "'OR' expected, got $configEntry"
                            );
                        }

                        $conditionMod = true;
                    }
                }

                if (in_array(true, array_unique($stack), true)) {
                    continue;
                } else {
                    $excMessages = array();
                    foreach ($stack as $stackEntry) {
                        if ($stackEntry instanceof \Exception) {
                            $excMessages[] = $stackEntry->getMessage();
                        }
                    }
                    throw new InvalidArgumentException(
                        implode('; ', $excMessages)
                    );
                }

               return;
            }

            $isMandatory = isset($entityConfig['mandatory'])
                ? (bool) $entityConfig['mandatory']
                : true;

            $isSettingAvailable = array_key_exists($argumentName, $data);

            $isStrict = (isset($entityConfig['strict']) &&
                        $entityConfig['strict'] === true);

            $wasDefaultNull = false;

            if (!$isSettingAvailable && $isMandatory) {
                throw new InvalidArgumentException(
                    "\"$argumentName\" is mandatory, but does not exist in data"
                );
            } else if (!$isSettingAvailable && !$isMandatory) {
                if (array_key_exists('default', $entityConfig)) {
                    $data[$argumentName] = $entityConfig['default'];
                    $wasDefaultNull = $entityConfig['default'] === null;
                } else {
                    continue;
                }
            }

            $allowEmpty = isset($entityConfig['allowEmpty'])
                          ? $entityConfig['allowEmpty']
                          : false;

            if ($allowEmpty && $wasDefaultNull) {
                continue;
            }

            // strict = true, allowEmpty= true, value = null
            // -> allowEmpty succeeds, pass!
            if ($allowEmpty && $isStrict  && $isSettingAvailable &&
                $data[$argumentName] === null) {
                continue;
            }

            $minLength =  isset($entityConfig['minLength'])
                          ? (int)(string)$entityConfig['minLength']
                          : null;

            $greaterThan = isset($entityConfig['greaterThan'])
                           ? (int)(string)$entityConfig['greaterThan']
                           : false;

            switch ($entityConfig['type']) {

                case 'arrayType':

                    if (!$allowEmpty && !isset($data[$argumentName])) {
                        throw new InvalidArgumentException(
                            "\"$argumentName\" not set"
                        );
                    }

                    $className = $entityConfig['class'];

                    if (!is_array($data[$argumentName])) {
                        throw new InvalidArgumentException(
                            "\"$argumentName\" is not an array"
                        );
                    }

                    if ($minLength !== null) {
                        if (count($data[$argumentName]) < $minLength) {
                            throw new InvalidArgumentException(
                                "\"$argumentName\" minLength is $minLength"
                            );
                        }
                    }

                    foreach ($data[$argumentName] as $arrKey => $arrValue) {
                        if (!($arrValue instanceof $className)) {
                            throw new InvalidArgumentException(
                                "\"$argumentName\" not instanceof " .
                                $entityConfig['class'] ." at index " .
                                $arrKey
                            );
                        }
                    }

                    break;

                case 'instanceof':

                    if (!$allowEmpty && !isset($data[$argumentName])) {
                        throw new InvalidArgumentException(
                            "\"$argumentName\" not set"
                        );
                    }

                    $className = $entityConfig['class'];

                    if (!($data[$argumentName] instanceof $className)) {
                        throw new InvalidArgumentException(
                            "\"$argumentName\" not instanceof " .
                            $entityConfig['class']
                        );
                    }


                    break;

                case 'array':

                    if (!$allowEmpty && !isset($data[$argumentName])) {
                        throw new InvalidArgumentException(
                            "\"$argumentName\" not set"
                        );
                    }

                    if (isset($data[$argumentName]) &&
                        !is_array($data[$argumentName])) {
                        throw new InvalidArgumentException(
                            "Not an array passed for $argumentName"
                        );
                    }

                    if ($minLength !== null) {
                        $minLengthCount = 0;
                        foreach ($data[$argumentName] as $minLengthCheck) {
                            $minLengthCount++;
                        }
                        if ($minLengthCount < $minLength) {
                            throw new InvalidArgumentException(
                                "\"$argumentName\" minLength is $minLength"
                            );
                        }
                    }


                    break;


                case 'inArray':
                    $values = &$entityConfig['values'];

                    if (!isset($data[$argumentName])) {
                        throw new InvalidArgumentException(
                            "\"$argumentName\" not set"
                        );
                    }

                    if (!in_array($data[$argumentName], $values)) {
                        throw new InvalidArgumentException(
                            "\"".$data[$argumentName]."\" not in list of [".
                                implode(', ', $values)."]"
                        );
                    }

                    break;

                case 'isset':
                    if (!isset($data[$argumentName])) {
                        throw new InvalidArgumentException(
                            "\"$argumentName\" not set"
                        );
                    }
                    break;

                case 'boolean':
                case 'bool':

                     if (isset($entityConfig['strict']) &&
                        $entityConfig['strict'] === true) {
                        if (!is_bool($data[$argumentName])) {
                            throw new InvalidArgumentException(
                                "No boolean value passed for $argumentName"
                            );
                        }
                    }

                    if (isset($data[$argumentName])) {
                        $data[$argumentName] = (bool) $data[$argumentName];
                    } else if ($allowEmpty === false) {
                        throw new InvalidArgumentException(
                            "no argument provided for $argumentName"
                        );
                    }
                break;

                case 'string':

                    if (is_array($data[$argumentName])
                        || is_object($data[$argumentName])) {
                        throw new InvalidArgumentException(
                            "Array or object passed for $argumentName - "
                            . (is_array($data[$argumentName])
                            ? 'array'
                            :'object')
                        );
                    }

                    if (isset($entityConfig['strict']) &&
                        $entityConfig['strict'] === true) {
                        if (!is_string($data[$argumentName])) {
                            throw new InvalidArgumentException(
                                "not a string!"
                            );
                        }
                    }

                    $val = (string)$data[$argumentName];
                    $org = $data[$argumentName];

                    if ($allowEmpty === false && trim($val) === "") {
                        throw new InvalidArgumentException(
                            "empty value provided for $argumentName"
                        );
                    }

                    $data[$argumentName] = $val;

                break;

                case 'int':
                case 'integer':

                    if (is_array($data[$argumentName])
                        || is_object($data[$argumentName])) {
                        throw new InvalidArgumentException(
                            "Array or object passed for $argumentName - "
                                . (is_array($data[$argumentName])
                                ? 'array'
                                :'object')
                        );
                    }

                    if (isset($entityConfig['strict']) &&
                        $entityConfig['strict'] === true) {
                        if (!is_int($data[$argumentName])) {
                            throw new InvalidArgumentException(
                                "not an integer!"
                            );
                        }
                    }

                    $val = (int)trim((string)$data[$argumentName]);
                    $org = $data[$argumentName];

                    if ($allowEmpty === false
                        && ($org === null || $org === "")) {
                        throw new InvalidArgumentException(
                            "empty value provided for $argumentName"
                        );
                    }

                    // check first if $org is not empty, and then check for
                    // greaterThan.
                    // if $org was empty and allowEmpty was set to false, an
                    // exception should have been thrown already.
                    // we take any other value into account
                    if (($org !== null && $org !== "") &&
                        $greaterThan !== false && $val <= $greaterThan) {
                        throw new InvalidArgumentException(
                            "value \"$argumentName\" must be > "
                            . $greaterThan .", was $val"
                        );
                    }

                    $data[$argumentName] = $val;

                break;
            }
        }
    }

}
