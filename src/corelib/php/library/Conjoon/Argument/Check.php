<?php
/**
 * conjoon
 * (c) 2002-2010 siteartwork.de/conjoon.org
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
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Conjoon_Argument_Check {

    private function __construct(){}

    private function __clone(){}

    /**
     *
     *
     * @throws Exception
     */
    public static function check(Array $config, Array &$data)
    {

        foreach ($config as $argumentName => $config) {

            $allowEmpty = isset($config['allowEmpty'])
                          ? $config['allowEmpty']
                          : false;

            switch ($config['type']) {

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
                    $val = "";
                    if (isset($data[$argumentName])) {
                        $data[$argumentName] = trim((string)$data[$argumentName]);
                        $val = $data[$argumentName];
                    } else if ($allowEmpty === false) {
                        throw new Conjoon_Argument_Exception(
                            "no argument provided for $argumentName"
                        );
                    }

                    if ($val == "" && !$allowEmpty) {
                        throw new Conjoon_Argument_Exception(
                            "Invalid argument supplied for $argumentName - "
                            .$data[$argumentName]
                        );
                    }
                break;

                case 'int':
                    if (isset($data[$argumentName])) {
                        $data[$argumentName] = (int)$data[$argumentName];
                    } else if ($allowEmpty === false) {
                        throw new Conjoon_Argument_Exception(
                            "no argument provided for $argumentName"
                        );
                    }

                    if ($data[$argumentName] <= 0) {
                        throw new Conjoon_Argument_Exception(
                            "Invalid argument supplied for $argumentName - "
                            .$data[$argumentName]
                        );
                    }
                break;
            }
        }
    }

}