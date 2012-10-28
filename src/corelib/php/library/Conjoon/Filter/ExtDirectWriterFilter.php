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
 * @see Zend_Filter_Interface
 */
require_once 'Zend/Filter/Interface.php';


/**
 * Filters data send by an Ext.Direct-Writer - it looks up the passed value
 * and checks whether the value is a numeric array that contains only one index,
 * i.e. "0" - if that is the case, the value of this index is returned if, and
 * only if the value is an array itself.
 *
 * @category   Filter
 * @package    Conjoon_Filter
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Filter_ExtDirectWriterFilter implements Zend_Filter_Interface
{
    protected $_root = null;

    public function __construct($root = null)
    {
        $this->_root = $root;
    }

    /**
     * Defined by Zend_Filter_Interface
     *
     *
     * @param  mixed $value
     * @return integer
     */
    public function filter($value)
    {
        if ($this->_root != null) {
            if (!array_key_exists(0, $value)) {
                return $value;
            }

            if (!array_key_exists($this->_root, $value[0])) {
                return $value;
            }

            $value = $value[0][$this->_root];
        }

        if (!is_array($value)) {
            return $value;
        }

        /**
         * @see Conjoon_Util_Array
         */
        require_once 'Conjoon/Util/Array.php';

        if (isset($value[0]) && is_array($value[0]) && count($value) == 1
            && !Conjoon_Util_Array::isAssociative($value[0])) {
            return $value[0];
        }

        if (Conjoon_Util_Array::isAssociative($value)) {
            return array($value);
        }

        return $value;
    }
}
