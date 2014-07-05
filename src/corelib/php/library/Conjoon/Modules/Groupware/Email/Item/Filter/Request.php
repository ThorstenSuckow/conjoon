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
 * An input-filter class defining all validators and filters needed when
 * processing input data for mutating or creating feed items.
 *
 * @uses Conjoon_Filter_Input
 * @package    Conjoon_Filter_Input
 * @category   Filter
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
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
            'groupwareEmailFoldersId',
            'path'
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
         ),
        'path' => array(
            'StringTrim'
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
         ),
        'path' => array(
            'allowEmpty' => false
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