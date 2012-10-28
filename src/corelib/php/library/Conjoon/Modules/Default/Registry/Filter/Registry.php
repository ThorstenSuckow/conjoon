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
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Modules_Default_Registry_Filter_Registry extends Conjoon_Filter_Input {

    /**
     * @const string CONTEXT_UPDATE_REQUEST
     */
    const CONTEXT_UPDATE_REQUEST = 'update_request';

    protected $_presence = array(
        'update_request' => array(
            'data'
        ),
        self::CONTEXT_UPDATE => array(
            'key',
            'value'
        )
    );

    protected $_filters = array(
        'data' => array(
            array('ExtDirectWriterFilter')
            // additional filters actually set in _init depending on the context
         ),
         'key'   => array('StringTrim'),
         'value' => array('Raw')
    );

    protected $_validators = array(
         'data' => array(
            'allowEmpty' => false
         ),
         'key' => array(
            'allowEmpty' => false
         ),
         'value' => array(
            'allowEmpty' => true
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