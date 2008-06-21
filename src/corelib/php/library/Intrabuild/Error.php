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
class Intrabuild_Error implements Intrabuild_BeanContext, Serializable {
    
    const UNKNOWN = 'INTRABUILD_ERROR_UNKNOWN';
    const INPUT = 'INTRABUILD_ERROR_INPUT';
    
    const LEVEL_CRITICAL = 'critical';
    const LEVEL_WARNING  = 'warning';
    const LEVEL_ERROR    = 'error';
    
    protected $level;
    protected $code;
    protected $file;
    protected $line;
    protected $message;
    protected $type;
    
    
    /**
     * Constructor.
     * 
     */
    public function __construct()
    {
        $this->setLevel(self::LEVEL_CRITICAL);
    }
    
// -------- accessors        
    
    public function getCode(){return $this->code;}
    public function getFile(){return $this->file;}
    public function getLevel(){return $this->level;}
    public function getLine(){return $this->line;}
    public function getMessage(){return $this->message;}
    public function getType(){return $this->type;}
    
    public function setCode($code){$this->id = $code;}
    public function setLevel($level){$this->level = $level;}
    public function setFile($file){$this->file = $file;}
    public function setLine($line){$this->line = $line;}
    public function setMessage($message){$this->message = $message;}
    public function setType($type){$this->type = $type;}
    
// -------- helper
    /**
     * Returns an associative array, which key/value pairs represent
     * the properties stored by this object.
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            'code'    => $this->code,
            'level'   => $this->level,
            'file'    => $this->file,
            'line'    => $this->line,
            'message' => $this->message,
            'type'    => $this->type
        );
    }   
    
// -------- interface Intrabuild_BeanContext    
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
    
    /**
     * Returns a textual representation of the current object.
     *
     * @return string
     */
    public function __toString()
    {
        return 
            'code: '   .$this->code.', '.
            'level: '  .$this->level.', '.
            'file: '   .$this->file.', '.
            'line: '   .$this->line.', '.
            'message: '.$this->message.', '.
            'type: '   .$this->type.';';
    }
    
    /**
     * Returns a Dto for an instance of this class.
     * 
     * @return Intrabuild_Groupware_Email_AccountDto
     */    
    public function getDto()
    {
        require_once 'ErrorDto.php';
        
        $data = $this->toArray();
        
        $dto = new Intrabuild_ErrorDto();
        foreach ($data as $key => $value) {
            if (property_exists($dto, $key)) {
                $dto->$key = $value;
            }
        }    
        
        return $dto;
    }        
    
    /**
     * Creates a new instance of Intrabuild_Error based on the
     * passed exception.
     * 
     * @param Exception $e Any exception that should be casted
     * to an instance of Intrabuild_Error
     *
     * @return Intrabuild_Error
     */
    public static function fromException(Exception $e, $errorClass = 'Intrabuild_Error')
    {
        switch ($errorClass) {
            case 'Intrabuild_Error':
                $error = new Intrabuild_Error();
            break;    
            
            case 'Intrabuild_Error_Form':
                require_once 'Intrabuild/Error/Form.php';
                $error = new Intrabuild_Error_Form();
            break;    
        }
        
        $error->setCode($e->getCode());
        $error->setMessage($e->getMessage());
        $error->setFile($e->getFile());
        $error->setLine($e->getLine());
        $error->setType(get_class($e));
        
        return $error;
    }
    
    /**
     * Creates a Intrabuild_Error_Form based on the fields that where marked as
     * errorneous in the passed filter-instance.
     * 
     * @param Zend_Filter_Input $filter
     * @param Zend_Filter_Exception $e
     * 
     * @return Intrabuild_Error_Form
     */
    public static function fromFilter(Zend_Filter_Input $filter, Zend_Filter_Exception $e)
    {
        $error = self::fromException($e, 'Intrabuild_Error_Form');
        
        $fields = array();
        
        if ($filter->hasMissing()) {
            foreach ($filter->getMissing() as $f => $mes) {
               $fields[$f] = array_values($mes);   
            }
        } else if ($filter->hasInvalid()) {
            foreach ($filter->getInvalid() as $f => $mes) {
                $fields[$f] = array_values($mes);   
            }                    
        }       
        $error->setMessage(
            "Error in user input. The following fields ".
            "where missing or contained invalid data: ".
            implode('; ', array_keys($fields))
        );
        
        $error->setLevel(self::LEVEL_WARNING);
        
        $error->setFields($fields);
        
        return $error;
    }    
} 