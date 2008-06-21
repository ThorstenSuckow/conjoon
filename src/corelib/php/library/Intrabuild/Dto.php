<?php
/**
 * intrabuild
 * (c) 2002-2008 siteartwork.de/MindPatterns
 * license@siteartwork.de
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
 * @see Intrabuild_Util_Array
 */ 
require_once 'Intrabuild/Util/Array.php';
 
/**
 * Base class for all Data Transfer Objects (DTOs) in the
 * intrabuild project
 * 
 * DataTransferObjects are also used for the rowClass property
 * of Zend_Db_Table_Abstract (@see Zend_Db_Table_Abstract::setRowClass)
 *
 * @uses ArrayAccess
 * @category   Dto
 * @package    Intrabuild_Dto
 *
 * @author Thorsten-Suckow-Homberg <ts@siteartwork.de>
 */ 
abstract class Intrabuild_Dto implements ArrayAccess {
 

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