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
 * @see Zend_Validate_Abstract
 */
require_once 'Zend/Validate/Abstract.php';

/**
 * @see Zend_Date
 */
require_once 'Zend/Date.php';

class Intrabuild_Validate_Date extends Zend_Validate_Abstract
{
    const NOT_DATE = 'notDate';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_DATE => "'%value%' does not appear to be a date"
    );

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if $value is a valid date
     *
     * @param  string $value
     * @return boolean
     */
    public function isValid($value)
    {
        try {
            new Zend_Date($value);
        } catch (Zend_Date_Exception $e) {
            return false;
        }

        return true;
    }

}
