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
 * @see Intrabuild_Filter_Input
 */
require_once 'Intrabuild/Filter/Input.php';

/**
 * @see Intrabuild_Filter_Raw
 */
require_once 'Intrabuild/Filter/Raw.php';

/**
 * An input-filter class defining all validators and filters needed when
 * processing input data for mutating or creating Email-Folders.
 *
 * @uses Intrabuild_Filter_Input
 * @package    Intrabuild_Filter_Input
 * @category   Filter
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Intrabuild_Modules_Groupware_Email_Folder_Filter_Folder extends Intrabuild_Filter_Input {

    const CONTEXT_RENAME = 'rename';
    const CONTEXT_MOVE   = 'move';
    
    protected $_presence = array(
        self::CONTEXT_RENAME => array(
            'id',
            'name'
        ), 
        self::CONTEXT_CREATE => array(
            'parentId',
            'name'
        ), 
        self::CONTEXT_MOVE => array(
            'parentId',
            'id'
        )
    );
    
    
    protected $_filters = array(
        'id' => array(
            'Int'
         ),
        'parentId' => array(
            'Int'
         ),
        'name' => array(
            'StringTrim'
         )
    );
    
    protected $_validators = array(
        'id' => array(
            'allowEmpty' => false,
            array('GreaterThan', 0)
         ),
        'parentId' => array(
            'allowEmpty' => false,
            array('GreaterThan', 0)
         ),
        'name' => array(
            'allowEmpty' => false
         )
    );
    
    protected function _init()
    {
        $this->_defaultEscapeFilter = new Intrabuild_Filter_Raw();   
    }  
   

}