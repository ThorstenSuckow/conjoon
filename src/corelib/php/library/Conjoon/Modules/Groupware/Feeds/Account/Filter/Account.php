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
 * processing input data for mutating or creating feed-Accounts.
 *
 * @uses Conjoon_Filter_Input
 * @package    Conjoon_Filter_Input
 * @category   Filter
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Modules_Groupware_Feeds_Account_Filter_Account extends Conjoon_Filter_Input {

    const CONTEXT_EXTRACT_UPDATE = 'extract_update';

    protected $_presence = array(
        'extract_update' => array(
            'deleted',
            'updated'
        ),
        'delete' =>
            array(
                'id'
            )
        ,
        'update' =>
            array(
                'id',
                'name',
                'updateInterval',
                'requestTimeout',
                'deleteInterval',
                'isImageEnabled'
        ),
        'create' =>
            array(
                'uri',
                'title',
                'name',
                'updateInterval',
                'link',
                'requestTimeout',
                'description',
                'deleteInterval',
                'isImageEnabled',
                'lastUpdated'
        )
    );

    protected $_filters = array(
        'deleted' => array(
            'JsonDecode',
            'PositiveArrayValues'
        ),
        'updated' => array(
            'JsonDecode'
        ),
        'id' => array(
            'Int'
         ),
        'name' => array(
            'StringTrim'
         ),
        'title' => array(
            'StringTrim'
         ),
        'uri' => array(
            'StringTrim'
         ),
         'link' => array(
            'StringTrim'
         ),
         'description' => array(
            'StringTrim'
         ),
        'updateInterval' => array(
            'Int'
        ),
        'requestTimeout' => array(
            'Int'
        ),
        'deleteInterval' => array(
            'Int'
         ),
        'isImageEnabled' => array(
            'FormBoolToInt'
         ),
         'lastUpdated' => array(
            'Int'
         )
    );

    protected $_validators = array(
        'id' => array(
            'allowEmpty' => false,
            array('GreaterThan', 0)
         ),
        'name' => array(
            'allowEmpty' => false
         ),
        'title' => array(
            'allowEmpty' => true,
            'default' => ''
         ),
         'link' => array(
            'allowEmpty' => true,
            'default' => ''
         ),
         'description' => array(
            'allowEmpty' => true,
            'default' => ''
         ),
        'uri' => array(
            'allowEmpty' => false
         ),
        'updateInterval' => array(
            'allowEmpty' => true,
            'default'    => 3600,
            array('GreaterThan', 0)
        ),
        'requestTimeout' => array(
            'allowEmpty' => true,
            'default'    => 10,
            array('GreaterThan', 0)
        ),
        'deleteInterval' => array(
            'allowEmpty' => true,
            'default'    => 172800,
            array('GreaterThan', 0)
        ),
        'isImageEnabled' => array(
            'allowEmpty' => true,
            'default'    => 0
        ),
        'lastUpdated' => array(
            'presence'   => 'optional',
            'allowEmpty' => true,
            'default'    => 0
        )

    );


    protected function _init()
    {
        $this->_defaultEscapeFilter = new Conjoon_Filter_Raw();
    }

}