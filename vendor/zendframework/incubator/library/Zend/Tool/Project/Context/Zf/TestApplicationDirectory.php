<?php

class Zend_Tool_Project_Context_Zf_TestApplicationDirectory extends Zend_Tool_Project_Context_Filesystem_Directory 
{
    
    protected $_filesystemName = 'application';
    
    public function getName()
    {
        return 'TestApplicationDirectory';
    }
    
}