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
 * processing input data for mutating or creating Emails which are about
 * to be stored in the inbox table.
 *
 * @uses Intrabuild_Filter_Input
 * @package    Intrabuild_Filter_Input
 * @category   Filter
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Intrabuild_Modules_Groupware_Email_Item_Filter_Inbox extends Intrabuild_Filter_Input {

    protected $_presence = array(
         self::CONTEXT_CREATE => array(
            'groupwareEmailItemsId',
            'rawHeader',
            'rawBody',
            'hash',        
            'messageId',    
            'replyTo',         
            'uid',      
            'fetchedTimestamp'
        )
    );
    
    protected $_filters = array(
        'groupwareEmailItemsId' => array(
            'Int'
         ), 
         'hash' => array(
            'StringTrim'
         ), 
         'messageId' => array(
            'StringTrim'
         ), 
         'replyTo' => array(
            'StringTrim'
         ), 
         'uid' => array(
            'StringTrim'
         ), 
         'fetchedTimestamp' => array(
            'Int'
         ), 
         'rawHeader'        => array(),
         'rawBody'          => array()
    );
    
    protected $_validators = array(
        'groupwareEmailItemsId' => array(
            'allowEmpty' => false,
            array('GreaterThan', 0)
         ),
         'hash' => array(
            'allowEmpty' => true,
            'default'    => null
         ),
         'messageId' => array(
            'allowEmpty' => true,
            'default' => null
         ),
         'replyTo' => array(
            'allowEmpty' => true,
            'default' => null
         ),
        'uid' => array(
            'allowEmpty' => true,
            'default' => null
         ),
        'fetchedTimestamp' => array(
            'allowEmpty' => true
         ),
         'rawHeader' => array(
            'allowEmpty' => false
         ),
        'rawBody' => array(
            'allowEmpty' => true,
            'default' => ''
         )
    );
   
    protected function _init()
    {
        $this->_defaultEscapeFilter = new Intrabuild_Filter_Raw();
        $this->_validators['fetchedTimestamp']['default'] = time();    
    }  
   
   

}