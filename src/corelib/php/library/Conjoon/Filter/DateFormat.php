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
 * @see Zend_Filter_Interface
 */
require_once 'Zend/Filter/Interface.php';

/**
 * @see Zend_Date
 */
require_once 'Zend/Date.php';

/**
 * @category   Filter
 * @package    Conjoon_Filter
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Conjoon_Filter_DateFormat implements Zend_Filter_Interface
{

    /**
     * @var string
     */
    private $_format = "F j, Y, g:i a";

    public function __construct($format = null)
    {
        if ($format != null) {
            $this->_format = $format;
        }
    }

    /**
     * Defined by Zend_Filter_Interface
     *
     * Returns the date formatted for the format defined with the constructor.
     * Will return the date for the 1970-1-1 if the passed argument
     * was not in a valid date-format.
     *
     * @param  mixed $value
     * @return integer
     */
    public function filter($value)
    {
        Zend_Date::setOptions(array('format_type' => 'php'));
        $d = strtotime($value);
        if ($d === false) {
            try {
                $date = new Zend_Date($value);
            } catch (Zend_Date_Exception $e) {
                $date = new Zend_Date("1970-01-01 00:00:00");
            }
        } else {
            try {
                $date = new Zend_Date($d);
            } catch (Zend_Date_Exception $e) {
                $date = new Zend_Date("1970-01-01 00:00:00");
            }
        }

        $d = $date->get($this->_format);
        Zend_Date::setOptions(array('format_type' => 'iso'));
        return $d;
    }
}