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
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
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
    protected function _loadIniFile($config)
    {
        if (!is_array($config)) {
            throw new InvalidArgumentException("\"config\" must be an array");
        }

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