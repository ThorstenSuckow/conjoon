<?php
/**
 * conjoon
 * (c) 2002-2012 siteartwork.de/conjoon.org
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
 * processing input data for mutating or creating data in the read-table.
 *
 * @uses Conjoon_Filter_Input
 * @package    Conjoon_Filter_Input
 * @category   Filter
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Modules_Groupware_Email_Item_Filter_Flag extends Conjoon_Filter_Input {

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