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
 * An input-filter class defining all validators and filters needed when
 * processing input data for mutating or creating data in the read-table.
 *
 * @uses Intrabuild_Filter_Input
 * @package    Intrabuild_Filter_Input
 * @category   Filter
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Intrabuild_Modules_Groupware_Email_Item_Filter_Flag extends Intrabuild_Filter_Input {

    const CONTEXT_READ = 'read';
    const CONTEXT_SPAM = 'spam';

    protected $_presence = array(
         self::CONTEXT_CREATE => array(
            'groupwareEmailItemsId',
            'userId',
            'isRead',
            'isSpam',
        ),
        self::CONTEXT_READ => array(
            // user id will be set in the action based on the logged in user
            'id',
            'isRead'
        ),
        self::CONTEXT_SPAM => array(
            // user id will be set in the action based on the logged in user
            'id',
            'isSpam'
        ),
    );
    
    protected $_filters = array(
        'id' => array(
            'Int'
         ), 
        'groupwareEmailItemsId' => array(
            'Int'
         ), 
         'userId' => array(
            'Int'
         ), 
         'isRead' => array(
            'Int'
         ), 
         'isSpam' => array(
            'Int'
         )
    );
    
    protected $_validators = array(
         'id' => array(
            'allowEmpty' => false
         ), 
        'groupwareEmailItemsId' => array(
            'allowEmpty' => false
         ), 
         'userId' => array(
            'allowEmpty' => false
         ), 
         'isRead' => array(
            'allowEmpty' => true,
            'default' => 0
         ), 
         'isSpam' => array(
            'allowEmpty' => true,
            'default' => 0
         )
    );
   

}