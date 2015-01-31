<?php
/**
 * conjoon
 * (c) 2007-2015 conjoon.org
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
 * processing input data for mutating or creating data in the read-table.
 *
 * @uses Conjoon_Filter_Input
 * @package    Conjoon_Filter_Input
 * @category   Filter
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Modules_Groupware_Email_Item_Filter_Flag extends Conjoon_Filter_Input {

    const CONTEXT_READ = 'read';
    const CONTEXT_SPAM = 'spam';

    protected $_presence = array(
         self::CONTEXT_CREATE => array(
            'groupwareEmailItemsId',
            'userId',
            'isRead',
            'isSpam',
        ),
        self::CONTEXT_READ => array(
            // user id will be set in the action based on the logged in user
            'id',
            'isRead'
        ),
        self::CONTEXT_SPAM => array(
            // user id will be set in the action based on the logged in user
            'id',
            'isSpam'
        ),
    );

    protected $_filters = array(
        'id' => array(
            'Int'
         ),
        'groupwareEmailItemsId' => array(
            'Int'
         ),
         'userId' => array(
            'Int'
         ),
         'isRead' => array(
            'Int'
         ),
         'isSpam' => array(
            'Int'
         )
    );

    protected $_validators = array(
         'id' => array(
            'allowEmpty' => false
         ),
        'groupwareEmailItemsId' => array(
            'allowEmpty' => false
         ),
         'userId' => array(
            'allowEmpty' => false
         ),
         'isRead' => array(
            'allowEmpty' => true,
            'default' => 0
         ),
         'isSpam' => array(
            'allowEmpty' => true,
            'default' => 0
         )
    );


}