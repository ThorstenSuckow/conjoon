<?php

require_once 'Zend/Tool/Project/Context/Filesystem/File.php';

class Zend_Tool_Project_Context_Zf_FormFile extends Zend_Tool_Project_Context_Filesystem_File
{
    
    public function getName()
    {
        return 'FormFile';
    }
    
}