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
 * processing input data for mutating or creating feed items.
 *
 * @uses Conjoon_Filter_Input
 * @package    Conjoon_Filter_Input
 * @category   Filter
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Conjoon_Modules_Groupware_Email_Item_Filter_Request extends Conjoon_Filter_Input {

    const CONTEXT_REQUEST        = 'get_items';
    const CONTEXT_REQUEST_LATEST = 'get_latest_items';


    protected $_defaultEscapeFilter = 'StringTrim';

    protected $_presence = array(

        self::CONTEXT_REQUEST => array(
            'start',
            'limit',
            'dir',
            'sort',
            'groupwareEmailFoldersId'
        ),
        self::CONTEXT_REQUEST_LATEST => array(
            'start',
            'limit',
            'dir',
            'sort',
            'minDate'
        )
    );

    protected $_filters = array(
        'start' => array(
            'Int'
         ),
         'limit' => array(
            'Int'
         ),
        'dir' => array(
            'StringTrim',
            'StringToUpper'
         ),
         'sort' => array(
            'StringTrim',
            'StringToLower'
         ),
         'groupwareEmailFoldersId' => array(
            'Int'
         ),
         'minDate' => array(
            'Int'
         )
    );

    protected $_validators = array(
        'start' => array(
            'allowEmpty' => true,
            'default' => 0
         ),
         'limit' => array(
            'allowEmpty' => false,
            array('GreaterThan', 0)
         ),
        'dir' => array(
            'allowEmpty' => false
         ),
        'sort' => array(
            'allowEmpty' => false
         ),
         'minDate' => array(
            'allowEmpty' => false,
            array('GreaterThan', 0)
         ),
        'groupwareEmailFoldersId' => array(
            'allowEmpty' => false,
            array('GreaterThan', 0)
         )
    );

    protected function _init()
    {
        switch ($this->_context) {
            case self::CONTEXT_REQUEST:
            case self::CONTEXT_REQUEST_LATEST:
                require_once 'Conjoon/Modules/Groupware/Email/Item/Filter/SortFieldToTableField.php';
                require_once 'Conjoon/Filter/SortDirection.php';
                $this->_filters['dir'][]  = new Conjoon_Filter_SortDirection();
                $this->_filters['sort'][] = new Conjoon_Modules_Groupware_Email_Item_Filter_SortFieldToTableField();
            break;
        }
    }


}