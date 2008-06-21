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
 * Intrabuild_BeanContext 
 */ 
require_once 'Intrabuild/BeanContext.php'; 


/**
 * 
 * @uses       Intrabuild_BeanContext
 * @category   Intrabuild_Groupware
 * @package    Intrabuild_Groupware
 * @subpackage Email
 *
 * @author Thorsten-Suckow-Homberg <ts@siteartwork.de>
 */  
 
class Intrabuild_Modules_Groupware_Email_Attachment implements Intrabuild_BeanContext, Serializable {
    
    private $id;
    private $fileName;
    private $mimeType;
    
    
    /**
     * Constructor.
     */
    public function __construct()
    {
    
    }
    
// -------- accessors        
    
    public function getId(){return $this->id;}
    public function getFileName(){return $this->fileName;}
    public function getMimeType(){return $this->mimeType;}
    
    public function setId($id){$this->id = $id;}
    public function setFileName($fileName){$this->fileName = $fileName;}
    public function setMimeType($mimeType){$this->mimeType = $mimeType;}
   
    
// -------- interface Serializable
    /**
     * Serializes properties and returns them as a string which can later on
     * be unserialized.
     * 
     * @return string
     */
    public function serialize()
    {
        $data = $this->toArray();
        
        return serialize($data);
    }
    
    /**
     * Unserializes <tt>$serialized</tt> and assigns the specific 
     * values found to the members in this class.
     *
     * @param string $serialized The serialized representation of a former
     * instance of this class.
     */
    public function unserialize($serialized)
    {
        $str = unserialize($serialized);
        
  	     foreach ($str as $member => $value) {
  	        $this->$member = $value; 	    
        }
    }

// -------- interface Intrabuild_BeanContext    
    
    /**
     * Returns a Dto for an instance of this class.
     * 
     * @return Intrabuild_Groupware_Email_AccountDto
     */    
    public function getDto()
    {
        require_once 'Attachment/Dto.php';
        
        $data = $this->toArray();
        
        $dto = new Intrabuild_Modules_Groupware_Email_Attachment_Dto();
        foreach ($data as $key => $value) {
            if (property_exists($dto, $key)) {
                    $dto->$key = $value;
            }
        }    
        
        return $dto;
    }

    /**
     * Returns an associative array, which key/value pairs represent
     * the properties stored by this object.
     *
     * @return array
     */
    public function toArray()
    {
        
        return array(
            'id'           => $this->id,
            'fileName'     => $this->fileName,
            'mimeType'     => $this->mimeType
        );
    }   

    /**
     * Returns a textual representation of the current object.
     *
     * @return string
     */
    public function __toString()
    {
        $data = $this->toArray();
        
        $strs = array();
        foreach ($data as $key => $value) {
            $strs[] = $key.': '.$value;
        }
        return get_class($this).'['.implode('; ', $strs).']';
    }
} 