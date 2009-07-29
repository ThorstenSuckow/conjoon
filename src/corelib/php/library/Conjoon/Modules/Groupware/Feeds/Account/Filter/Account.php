<?php
/**
 * conjoon
 * (c) 2002-2009 siteartwork.de/conjoon.org
 * licensing@conjoon.org
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
 * @see Conjoon_Filter_Input
 */
require_once 'Conjoon/Filter/Input.php';


/**
 * An input-filter class defining all validators and filters needed when
 * processing input data for mutating or creating feed-Accounts.
 *
 * @uses Conjoon_Filter_Input
 * @package    Conjoon_Filter_Input
 * @category   Filter
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Conjoon_Modules_Groupware_Feeds_Account_Filter_Account extends Conjoon_Filter_Input {

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
                'requestTimeout',
                'deleteInterval',
                'isImageEnabled'
        ),
        'create' =>
            array(
                'uri',
                'title',
                'name',
                'updateInterval',
                'link',
                'requestTimeout',
                'description',
                'deleteInterval',
                'isImageEnabled'
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
        'requestTimeout' => array(
            'Int'
        ),
        'deleteInterval' => array(
            'Int'
         ),
        'isImageEnabled' => array(
            'FormBoolToInt'
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
        'requestTimeout' => array(
            'allowEmpty' => true,
            'default'    => 10,
            array('GreaterThan', 0)
        ),
        'deleteInterval' => array(
            'allowEmpty' => true,
            'default'    => 172800,
            array('GreaterThan', 0)
        ),
        'isImageEnabled' => array(
            'allowEmpty' => true,
            'default'    => 0
        )

    );


}