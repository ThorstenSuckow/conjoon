<?php

require_once 'Zend/Tool/Project/Context/Filesystem/File.php';
require_once 'Zend/Filter/Word/DashToCamelCase.php';
require_once 'Zend/Tool/CodeGenerator/Php/File.php';



class Zend_Tool_Project_Context_Zf_ControllerFile extends Zend_Tool_Project_Context_Filesystem_File 
{
    
    protected $_controllerName = 'index';
    protected $_filesystemName = 'controllerName';
    
    public function init()
    {
        $this->_controllerName = $this->_resource->getAttribute('controllerName');
        $this->_filesystemName = ucfirst($this->_controllerName) . 'Controller.php';
        parent::init();
    }
    
    public function getPersistentParameters()
    {
        return array(
            'controllerName' => $this->getControllerName()
            );
    }
    
    public function getName()
    {
        return 'ControllerFile';
    }
    
    
    public function getControllerName()
    {
        return $this->_controllerName;
    }
    
    public function getContents()
    {
        
        $filter = new Zend_Filter_Word_DashToCamelCase();
        
        $className = $filter->filter($this->_controllerName) . 'Controller';
        
        $codeGenFile = new Zend_Tool_CodeGenerator_Php_File(array(
            'classes' => array(
                new Zend_Tool_CodeGenerator_Php_Class(array(
                    'name' => $className,
                    'extendedClass' => 'Zend_Controller_Action',
                    'methods' => array(
                        new Zend_Tool_CodeGenerator_Php_Method(array(
                            'name' => 'init',
                            'body' => '        /* Initialize action controller here */'
                            )),
                        new Zend_Tool_CodeGenerator_Php_Method(array(
                            'name' => 'indexAction',
                            'body' => '        /* Default action for action controller */'
                            ))
                        )
                    ))
                )
            ));
        

        if ($className == 'ErrorController') {
            
            $codeGenFile = new Zend_Tool_CodeGenerator_Php_File(array(
                'classes' => array(
                    new Zend_Tool_CodeGenerator_Php_Class(array(
                        'name' => $className,
                        'extendedClass' => 'Zend_Controller_Action',
                        'methods' => array(
                            new Zend_Tool_CodeGenerator_Php_Method(array(
                                'name' => 'init',
                                'body' => <<<EOS
        \$errors = \$this->_getParam('error_handler'); 

        switch (\$errors->type) { 
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER: 
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION: 

                // 404 error -- controller or action not found 
                \$this->getResponse()->setHttpResponseCode(404); 
                \$this->view->message = 'Page not found'; 
                break; 
            default: 
                // application error 
                \$this->getResponse()->setHttpResponseCode(500); 
                \$this->view->message = 'Application error'; 
                break; 
        } 

        \$this->view->env       = \$this->getInvokeArg('env'); 
        \$this->view->exception = \$errors->exception; 
        \$this->view->request   = \$errors->request; 
                            
EOS
                                ))
                            )
                        ))
                    )
                ));

        }
        
        return $codeGenFile->generate();
    }
    
    public function addAction($actionName)
    {
        require_once $this->getPath();
        $codeGenFile = Zend_Tool_CodeGenerator_Php_File::fromReflection(new Zend_Reflection_File($this->getPath()));
        $codeGenFileClasses = $codeGenFile->getClasses();
        $class = array_shift($codeGenFileClasses);
        $class->setMethod(array('name' => $actionName . 'Action', 'body' => '        // action body here'));
        
        file_put_contents($this->getPath(), $codeGenFile->generate());
    }
    
}