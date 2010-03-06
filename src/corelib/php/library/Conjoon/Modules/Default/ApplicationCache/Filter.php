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
 * processing input data for working with ApplicationCache.
 *
 * @uses Conjoon_Filter_Input
 * @package    Conjoon_Filter_Input
 * @category   Filter
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Conjoon_Modules_Default_ApplicationCache_Filter extends Conjoon_Filter_Input {

    /**
     * @const string CONTEXT_CLEARFLAG_REQUEST
     */
    const CONTEXT_CLEARFLAG_REQUEST = 'update_clearflag';

    protected $_presence = array(
        self::CONTEXT_CLEARFLAG_REQUEST => array(
            'clear'
        )
    );

    protected $_filters = array(
        'clear' => array(
            array('Boolean')
         )
    );

    protected $_validators = array(
         'clear' => array(
            'allowEmpty' => true,
            'default'    => false
         )
    );

    protected function _init()
    {
        $this->_defaultEscapeFilter = new Conjoon_Filter_Raw();
    }
}