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

/**
 * @see Conjoon_Argument_Exception
 */
require_once 'Conjoon/Argument/Exception.php';

/**
 *
 *
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Argument_Check {

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

            if (!array_key_exists($argumentName, $data)) {
                throw new Conjoon_Argument_Exception(
                    "\"$argumentName\" does not exist in data"
                );
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
                        throw new Conjoon_Argument_Exception(
                            "\"$argumentName\" not set"
                        );
                    }

                    $className = $entityConfig['class'];

                    if (!($data[$argumentName] instanceof $className)) {
                        throw new Conjoon_Argument_Exception(
                            "\"$argumentName\" not instanceof " .
                            $entityConfig['class']
                        );
                    }


                    break;

                case 'inArray':
                    $values = &$entityConfig['values'];

                    if (!isset($data[$argumentName])) {
                        throw new Conjoon_Argument_Exception(
                            "\"$argumentName\" not set"
                        );
                    }

                    if (!in_array($data[$argumentName], $values)) {
                        throw new Conjoon_Argument_Exception(
                            "\"".$data[$argumentName]."\" not in list of [".
                                implode(', ', $values)."]"
                        );
                    }

                    break;

                case 'isset':
                    if (!isset($data[$argumentName])) {
                        throw new Conjoon_Argument_Exception(
                            "\"$argumentName\" not set"
                        );
                    }
                    break;


                case 'bool':
                    if (isset($data[$argumentName])) {
                        $data[$argumentName] = (bool)$data[$argumentName];
                    } else if ($allowEmpty === false) {
                        throw new Conjoon_Argument_Exception(
                            "no argument provided for $argumentName"
                        );
                    }
                break;

                case 'string':

                    if (is_array($data[$argumentName])
                        || is_object($data[$argumentName])) {
                        throw new Conjoon_Argument_Exception(
                            "Array or object passed for $argumentName - "
                            . (is_array($data[$argumentName])
                            ? 'array'
                            :'object')
                        );
                    }

                    $val = trim((string)$data[$argumentName]);
                    $org = $data[$argumentName];

                    if ($allowEmpty === false && $val === "") {
                        throw new Conjoon_Argument_Exception(
                            "empty value provided for $argumentName"
                        );
                    }

                    $data[$argumentName] = $val;

                break;

                case 'int':

                    if (is_array($data[$argumentName])
                        || is_object($data[$argumentName])) {
                        throw new Conjoon_Argument_Exception(
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
                        throw new Conjoon_Argument_Exception(
                            "empty value provided for $argumentName"
                        );
                    }

                    if ($greaterThan !== false && $val <= $greaterThan) {
                        throw new Conjoon_Argument_Exception(
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