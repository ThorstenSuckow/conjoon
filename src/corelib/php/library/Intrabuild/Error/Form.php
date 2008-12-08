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
 * Intrabuild_BeanContext
 */
require_once 'Intrabuild/Error.php';


/**
 * A class representing an error in the intrabuild application.
 *
 * @uses       Intrabuild_BeanContext
 * @uses       Serializable
 * @package    Intrabuild
 * @subpackage Error
 * @category   Error
 *
 * @author Thorsten-Suckow-Homberg <ts@siteartwork.de>
 */
class Intrabuild_Error_Form extends Intrabuild_Error {

    private $fields;

// -------- accessors

    public function getFields(){return $this->fields;}
    public function setFields(Array $fields){$this->fields = $fields;}


// -------- helper
    /**
     * Returns an associative array, which key/value pairs represent
     * the properties stored by this object.
     *
     * @return array
     */
    public function toArray()
    {
        $array = parent::toArray();
        $array['fields'] = $this->fields;
        return $array;
    }

// -------- interface Intrabuild_BeanContext
    /**
     * Returns a textual representation of the current object.
     *
     * @return string
     */
    public function __toString()
    {
        $parts = array();

        foreach($this->fields as $field => $messages) {
            $parts[] = '"'.$field.'" - ['.implode(';', $messages).']';
        }

        return  'fields: '.implode(' - ', $parts).', '.
                 parent::__toString();
    }

    /**
     * Returns a Dto for an instance of this class.
     *
     * @return Intrabuild_Groupware_Email_AccountDto
     */
    public function getDto()
    {
        require_once 'FormDto.php';

        $data = $this->toArray();

        $dto = new Intrabuild_Error_FormDto();
        foreach ($data as $key => $value) {
            if (property_exists($dto, $key)) {
                $dto->$key = $value;
            }
        }

        return $dto;
    }
}