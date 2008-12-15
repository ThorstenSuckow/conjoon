<?php
/**
 * conjoon
 * (c) 2002-2009 siteartwork.de/conjoon.org
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
 * @see Zend_Config_Ini
 */
require_once 'Zend/Config/Ini.php';

/**
 * Allows for passing an array which keys uses a nest separator.
 * The according keys will be split so that the separator is taken into
 * account. This basically follows the logic of Zend_Config_Ini with the
 * difference that you pass the constructor a config array instead of
 * a filename.
 *
 * @uses Zend_Config
 * @package Conjoon_Config
 *
 * @author Zend
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Conjoon_Config_Array extends Zend_Config_Ini {

    /**
     * Constructor.
     * Works exactly like Zend_Config_Ini, with the only difference that instead
     * of a filename, a config array is oassed as the argument.
     *
     * Unfortunately, lots  of the logic for processing the array has been put
     * into the constructor of Zend_Config_Ini, this is why the implementation
     * is taken 1:1 from the parent's implementation.
     * Anyway, all kudos for this code to the Zend Framework COntributors!
     *
     * @param  array         $iniArray
     * @param  string|null   $section
     * @param  boolean|array $options
     * @throws Zend_Config_Exception
     * @return void
     */
    public function __construct(Array $iniArray, $section = null, $options = false)
    {
        $allowModifications = false;
        if (is_bool($options)) {
            $allowModifications = $options;
        } elseif (is_array($options)) {
            if (isset($options['allowModifications'])) {
                $allowModifications = (bool) $options['allowModifications'];
            }
            if (isset($options['nestSeparator'])) {
                $this->_nestSeparator = (string) $options['nestSeparator'];
            }
        }

        $preProcessedArray = array();
        foreach ($iniArray as $key => $data) {
            $bits = explode(':', $key);
            $thisSection = trim($bits[0]);
            switch (count($bits)) {
                case 1:
                    $preProcessedArray[$thisSection] = $data;
                    break;

                case 2:
                    $extendedSection = trim($bits[1]);
                    $preProcessedArray[$thisSection] = array_merge(
                        array(';extends'=>$extendedSection), $data
                    );
                    break;

                default:
                    /**
                     * @see Conjoon_Config_Exception
                     */
                    require_once 'Conjoon/Config/Exception.php';
                    throw new Conjoon_Config_Exception(
                        "Section '$thisSection' may not extend ".
                        "multiple sections in $filename"
                    );
            }
        }

        if (null === $section) {
            $dataArray = array();
            foreach ($preProcessedArray as $sectionName => $sectionData) {
                if(!is_array($sectionData)) {
                    $dataArray = array_merge_recursive(
                        $dataArray,
                        $this->_processKey(array(), $sectionName, $sectionData)
                    );
                } else {
                    $dataArray[$sectionName] = $this->_processExtends(
                        $preProcessedArray, $sectionName
                    );
                }
            }
            Zend_Config::__construct($dataArray, $allowModifications);
        } elseif (is_array($section)) {
            $dataArray = array();
            foreach ($section as $sectionName) {
                if (!isset($preProcessedArray[$sectionName])) {
                    /**
                     * @see Conjoon_Config_Exception
                     */
                    require_once 'Conjoon/Config/Exception.php';
                    throw new Conjoon_Config_Exception(
                        "Section '$sectionName' cannot be found in $filename"
                    );
                }
                $dataArray = array_merge(
                    $this->_processExtends($preProcessedArray, $sectionName), $dataArray
                );

            }
            Zend_Config::__construct($dataArray, $allowModifications);
        } else {
            if (!isset($preProcessedArray[$section])) {
                /**
                 * @see Conjoon_Config_Exception
                 */
                require_once 'Conjoon/Config/Exception.php';
                throw new Conjoon_Config_Exception(
                    "Section '$section' cannot be found in $filename"
                );
            }
            Zend_Config::__construct(
                $this->_processExtends($preProcessedArray, $section), $allowModifications
            );
        }

        $this->_loadedSection = $section;
    }

}