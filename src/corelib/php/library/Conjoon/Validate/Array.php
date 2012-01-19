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
 * @see Zend_Validate_Abstract
 */
require_once 'Zend/Validate/Abstract.php';


class Conjoon_Validate_Array extends Zend_Validate_Abstract
{
    const NOT_ARRAY = 'notArray';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_ARRAY => "'%value%' does not appear to be an array"
    );

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if $value is a valid array
     *
     * @param  string $value
     * @return boolean
     */
    public function isValid($value)
    {
        return is_array($value);
    }

}