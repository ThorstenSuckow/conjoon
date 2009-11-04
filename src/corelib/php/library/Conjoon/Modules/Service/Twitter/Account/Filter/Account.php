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
 * @see Conjoon_Filter_Raw
 */
require_once 'Conjoon/Filter/Raw.php';

/**
 * An input-filter class defining all validators and filters needed when
 * processing input data for mutating or creating Email-Accounts.
 *
 * @uses Conjoon_Filter_Input
 * @package    Conjoon_Filter_Input
 * @category   Filter
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Conjoon_Modules_Service_Twitter_Account_Filter_Account extends Conjoon_Filter_Input {

    protected $_presence = array(
        'create' => array(
            'name',
            'password',
            'updateInterval'
        ),
        'delete' => array(
            'data'
        )
    );

    protected $_filters = array(
        'name' => array(
            'StringTrim'
         ),
        'password' => array(
            'StringTrim'
         ),
         'updateInterval' => array(
            'Int'
         ),
         'data' => array(
            'ExtDirectWriterFilter',
            'PositiveArrayValues'
         )
    );

    protected $_validators = array(
        'name' => array(
            'allowEmpty' => false
         ),
        'password' => array(
            'allowEmpty' => false
         ),
        'updateInterval' => array(
            'allowEmpty' => true,
            'default'    => 60
         ),
         'data' => array(
            'allowEmpty' => false
         )
    );

    protected $_dontRecurseFilter = array(
        'data'
    );

    protected function _init()
    {
        $this->_defaultEscapeFilter = new Conjoon_Filter_Raw();
    }

}