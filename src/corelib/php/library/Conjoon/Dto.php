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
 * @see Conjoon_Util_Array
 */
require_once 'Conjoon/Util/Array.php';

/**
 * Base class for all Data Transfer Objects (DTOs) in the
 * conjoon project
 *
 * DataTransferObjects are also used for the rowClass property
 * of Zend_Db_Table_Abstract (@see Zend_Db_Table_Abstract::setRowClass)
 *
 * @uses ArrayAccess
 * @category   Dto
 * @package    Conjoon_Dto
 *
 * @author Thorsten-Suckow-Homberg <ts@siteartwork.de>
 */
abstract class Conjoon_Dto implements ArrayAccess {


// -------- interface ArrayAccess

    public function offsetExists($offset)
    {
        if (property_exists(get_class($this), $offset)) {
            return true;
        }
    }

    public function offsetGet($offset)
    {
        if (property_exists(get_class($this), $offset)) {
            return $this->{$offset};
        }
        return null;
    }

    public function offsetSet($offset, $value)
    {
        if (property_exists(get_class($this), $offset)) {
            $this->{$offset} = $value;
        }

    }

    public function offsetUnset($offset)
    {
        if (property_exists(get_class($this), $offset)) {
            unset($this->{$offset});
        }
    }

}