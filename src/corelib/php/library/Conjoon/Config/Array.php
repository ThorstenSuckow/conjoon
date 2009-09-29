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
     * Loads the section $section from the array $config for
     * access facilitated by nested object properties.
     *
     *
     * @param  array         $config
     * @param  string|null   $section
     * @param  boolean|array $options
     * @throws Zend_Config_Exception
     * @return void
     */
    public function __construct(Array $config, $section = null, $options = false)
    {
        parent::__construct($config, $section, $options);
    }

    /**
     * Overrides parent implementation by passing an already parsed ini array to this
     * method.
     *
     * @param Array $config
     * @throws Zend_Config_Exception
     * @return array
     */
    protected function _loadIniFile(Array $config)
    {
        $loaded = $config;

        $iniArray = array();
        foreach ($loaded as $key => $data)
        {
            $pieces = explode($this->_sectionSeparator, $key);
            $thisSection = trim($pieces[0]);
            switch (count($pieces)) {
                case 1:
                    $iniArray[$thisSection] = $data;
                    break;

                case 2:
                    $extendedSection = trim($pieces[1]);
                    $iniArray[$thisSection] = array_merge(array(';extends'=>$extendedSection), $data);
                    break;

                default:
                    /**
                     * @see Zend_Config_Exception
                     */
                    require_once 'Zend/Config/Exception.php';
                    throw new Zend_Config_Exception("Section '$thisSection' may not extend multiple sections in $filename");
            }
        }

        return $iniArray;
    }

}