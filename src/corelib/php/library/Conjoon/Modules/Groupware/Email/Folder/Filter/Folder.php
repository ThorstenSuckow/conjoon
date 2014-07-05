<?php
/**
 * conjoon
 * (c) 2007-2014 conjoon.org
 * licensing@conjoon.org
 *
 * conjoon
 * Copyright (C) 2014 Thorsten Suckow-Homberg/conjoon.org
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
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
            'StringTrim'
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