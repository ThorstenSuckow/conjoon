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
 * @see Conjoon_Filter_Raw
 */
require_once 'Conjoon/Filter/Raw.php';

/**
 * An input-filter class defining all validators and filters needed when
 * processing input data for mutating or creating Emails which are about
 * to be stored in the inbox table.
 *
 * @uses Conjoon_Filter_Input
 * @package    Conjoon_Filter_Input
 * @category   Filter
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Modules_Groupware_Email_Item_Filter_Inbox extends Conjoon_Filter_Input {

    protected $_presence = array(
         self::CONTEXT_CREATE => array(
            'groupwareEmailItemsId',
            'rawHeader',
            'rawBody',
            'hash',
            'messageId',
            'uid',
            'fetchedTimestamp'
        )
    );

    protected $_filters = array(
        'groupwareEmailItemsId' => array(
            'Int'
         ),
         'hash' => array(
            'StringTrim'
         ),
         'messageId' => array(
            'StringTrim'
         ),

         'uid' => array(
            'StringTrim'
         ),
         'fetchedTimestamp' => array(
            'Int'
         ),
         'rawHeader'        => array(),
         'rawBody'          => array()
    );

    protected $_validators = array(
        'groupwareEmailItemsId' => array(
            'allowEmpty' => false,
            array('GreaterThan', 0)
         ),
         'hash' => array(
            'presence'   => 'optional',
            'allowEmpty' => true,
            'default'    => null
         ),
         'messageId' => array(
            'allowEmpty' => true,
            'default' => null
         ),
         'uid' => array(
            'allowEmpty' => true,
            'default' => null
         ),
        'fetchedTimestamp' => array(
            'allowEmpty' => true
         ),
         'rawHeader' => array(
            'allowEmpty' => false
         ),
        'rawBody' => array(
            'allowEmpty' => true,
            'default' => ''
         )
    );

    protected function _init()
    {
        $this->_defaultEscapeFilter = new Conjoon_Filter_Raw();
        $this->_validators['fetchedTimestamp']['default'] = time();
    }



}