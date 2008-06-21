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
 * processing input data for mutating or creating feed-Accounts.
 *
 * @uses Intrabuild_Filter_Input
 * @package    Intrabuild_Filter_Input
 * @category   Filter
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Intrabuild_Modules_Groupware_Feeds_AccountFilter extends Intrabuild_Filter_Input {

    protected $_presence = array(
        'delete' => 
            array(
                'id'
            )
        ,
        'update' => 
            array(
                'id',
                'name',
                'updateInterval',
                'deleteInterval'
        ),
        'create' => 
            array(
                'uri',
                'title',     
                'name',
                'updateInterval',
                'link',
                'description',
                'deleteInterval'
        )
    );
    
    protected $_filters = array(
        'id' => array(
            'Int'
         ),
        'name' => array(
            'StringTrim'
         ),
        'title' => array(
            'StringTrim'
         ),
        'uri' => array(
            'StringTrim'
         ),
         'link' => array(
            'StringTrim'
         ),
         'description' => array(
            'StringTrim'
         ),
        'updateInterval' => array(
            'Int'
        ),
        'deleteInterval' => array(
            'Int'
         )
    );
    
    protected $_validators = array(
        'id' => array(
            'allowEmpty' => false,
            array('GreaterThan', 0)
         ),
        'name' => array(
            'allowEmpty' => false
         ),
        'title' => array(
            'allowEmpty' => true,
            'default' => ''
         ),
         'link' => array(
            'allowEmpty' => true,
            'default' => ''
         ),
         'description' => array(
            'allowEmpty' => true,
            'default' => ''
         ),
        'uri' => array(
            'allowEmpty' => false
         ),
        'updateInterval' => array(
            'allowEmpty' => true,
            'default'    => 3600,
            array('GreaterThan', 0)
        ),
        'deleteInterval' => array(
            'allowEmpty' => true,
            'default'    => 172800,
            array('GreaterThan', 0)
        )
    );
   
   
}