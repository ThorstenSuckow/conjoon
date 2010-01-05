<?php
/**
 * conjoon
 * (c) 2002-2010 siteartwork.de/conjoon.org
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
 * @see Conjoon_Filter_Raw
 */
require_once 'Conjoon/Filter/Raw.php';

/**
 * An input-filter class defining all validators and filters needed when
 * processing input data for mutating or creating Emails which are about
 * to be stored in the inbox table.
 *
 * @uses Conjoon_Filter_Input
 * @package    Conjoon_Filter_Input
 * @category   Filter
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Conjoon_Modules_Groupware_Email_Item_Filter_Inbox extends Conjoon_Filter_Input {

    protected $_presence = array(
         self::CONTEXT_CREATE => array(
            'groupwareEmailItemsId',
            'rawHeader',
            'rawBody',
            'hash',
            'messageId',
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
            'presence'   => 'optional',
            'allowEmpty' => true,
            'default'    => null
         ),
         'messageId' => array(
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
        $this->_defaultEscapeFilter = new Conjoon_Filter_Raw();
        $this->_validators['fetchedTimestamp']['default'] = time();
    }



}