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
 * processing input data for mutating or creating Email-Folders.
 *
 * @uses Conjoon_Filter_Input
 * @package    Conjoon_Filter_Input
 * @category   Filter
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Modules_Groupware_Email_Folder_Filter_Folder extends Conjoon_Filter_Input {

    const CONTEXT_RENAME = 'rename';
    const CONTEXT_MOVE   = 'move';

    protected $_presence = array(
        self::CONTEXT_RESPONSE => array(
            'id',
            'path'
        ),
        self::CONTEXT_RENAME => array(
            'id',
            'name',
            'parentId',
            'path'
        ),
        self::CONTEXT_CREATE => array(
            'parentId',
            'name',
            'path'
        ),
        self::CONTEXT_MOVE => array(
            'parentId',
            'id',
            'path',
            'parentPath'
        ),
        self::CONTEXT_DELETE => array(
            'id'
        )
    );


    protected $_filters = array(
        'id' => array(
            'StringTrim'
         ),
        'parentId' => array(
            'StringTrim'
        ),
        'name' => array(
            'StringTrim'
        ),
        'path' => array(
            'SanitizeExtFolderPath'
        ),
        'parentPath' => array(
            'SanitizeExtFolderPath'
        )
    );

    protected $_validators = array(
        'id' => array(
            'allowEmpty' => false
        ),
        'parentId' => array(
            'allowEmpty' => false
        ),
        'name' => array(
            'allowEmpty' => false
        ),
        'path' => array(
            'allowEmpty' => false
        ),
        'parentPath' => array(
            'allowEmpty' => false
        )
    );

    protected function _init()
    {
        if ($this->_context == self::CONTEXT_RESPONSE) {
            // allow path to be empty to indicate all root folders
            // shall be returned
            $this->_validators['path'] = array(
                'allowEmpty' => true
            );
        }

        $this->_defaultEscapeFilter = new Conjoon_Filter_Raw();
    }


}