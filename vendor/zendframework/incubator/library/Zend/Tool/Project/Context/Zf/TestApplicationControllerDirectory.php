<?php

class Zend_Tool_Project_Context_Zf_TestApplicationControllerDirectory extends Zend_Tool_Project_Context_Filesystem_Directory 
{
    
    protected $_filesystemName = 'controllers';
    
    public function getName()
    {
        return 'TestApplicationControllerDirectory';
    }
    
}