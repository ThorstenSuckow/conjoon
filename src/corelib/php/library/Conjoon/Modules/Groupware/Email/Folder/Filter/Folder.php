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
 * processing input data for mutating or creating Email-Folders.
 *
 * @uses Conjoon_Filter_Input
 * @package    Conjoon_Filter_Input
 * @category   Filter
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
class Conjoon_Modules_Groupware_Email_Folder_Filter_Folder extends Conjoon_Filter_Input {

    const CONTEXT_RENAME = 'rename';
    const CONTEXT_MOVE   = 'move';

    protected $_presence = array(
        self::CONTEXT_RENAME => array(
            'id',
            'name'
        ),
        self::CONTEXT_CREATE => array(
            'parentId',
            'name'
        ),
        self::CONTEXT_MOVE => array(
            'parentId',
            'id'
        ),
        self::CONTEXT_DELETE => array(
            'id'
        )
    );


    protected $_filters = array(
        'id' => array(
            'Int'
         ),
        'parentId' => array(
            'Int'
         ),
        'name' => array(
            'StringTrim'
         )
    );

    protected $_validators = array(
        'id' => array(
            'allowEmpty' => false,
            array('GreaterThan', 0)
         ),
        'parentId' => array(
            'allowEmpty' => false,
            array('GreaterThan', 0)
         ),
        'name' => array(
            'allowEmpty' => false
         )
    );

    protected function _init()
    {
        $this->_defaultEscapeFilter = new Conjoon_Filter_Raw();
    }


}