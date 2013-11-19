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

            $isMandatory = isset($entityConfig['mandatory'])
                ? (bool) $entityConfig['mandatory']
                : true;

            $isSettingAvailable = array_key_exists($argumentName, $data);

            if (!$isSettingAvailable && $isMandatory) {
                throw new InvalidArgumentException(
                    "\"$argumentName\" is mandatory, but does not exist in data"
                );
            } else if (!$isSettingAvailable && !$isMandatory) {
                if (isset($entityConfig['default'])) {
                    $data[$argumentName] = $entityConfig['default'];
                }
                return;
            }

            $allowEmpty = isset($entityConfig['allowEmpty'])
                          ? $entityConfig['allowEmpty']
                          : false;

            $greaterThan = isset($entityConfig['greaterThan'])
                           ? (int)(string)$entityConfig['greaterThan']
                           : false;

            switch ($entityConfig['type']) {

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

                    if (!is_bool($data[$argumentName])) {
                        throw new InvalidArgumentException(
                            "No boolean value passed for $argumentName"
                        );
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

                    $val = trim((string)$data[$argumentName]);
                    $org = $data[$argumentName];

                    if ($allowEmpty === false && $val === "") {
                        throw new InvalidArgumentException(
                            "empty value provided for $argumentName"
                        );
                    }

                    $data[$argumentName] = $val;

                break;

                case 'int':

                    if (is_array($data[$argumentName])
                        || is_object($data[$argumentName])) {
                        throw new InvalidArgumentException(
                            "Array or object passed for $argumentName - "
                                . (is_array($data[$argumentName])
                                ? 'array'
                                :'object')
                        );
                    }

                    $val = (int)trim((string)$data[$argumentName]);
                    $org = $data[$argumentName];

                    if ($allowEmpty === false
                        && ($org === null || $org === "")) {
                        throw new InvalidArgumentException(
                            "empty value provided for $argumentName"
                        );
                    }

                    if ($greaterThan !== false && $val <= $greaterThan) {
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
