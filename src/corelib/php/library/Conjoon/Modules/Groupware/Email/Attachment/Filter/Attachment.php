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
 * processing input data for mutating or creating data in the attachment-table.
 *
 * @uses Conjoon_Filter_Input
 * @package    Conjoon_Filter_Input
 * @category   Filter
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Modules_Groupware_Email_Attachment_Filter_Attachment extends Conjoon_Filter_Input {

    protected $_presence = array(
         self::CONTEXT_CREATE => array(
            'groupwareEmailItemsId',
            'fileName',
            'mimeType',
            'encoding',
            'content',
            'contentId',
            'key'
        )
    );

    protected $_filters = array(
         'groupwareEmailItemsId' => array(
            'Int'
         ),
         'fileName' => array(
            'StringTrim'
         ),
         'key' => array(
            'StringTrim'
         ),
         'mimeType' => array(
            'StringTrim'
         ),
         'encoding' => array(
            'StringTrim'
         ),
         'content' => array(),
         'contentId' => array(
            'StringTrim'
         )
    );

    protected $_validators = array(
        'groupwareEmailItemsId' => array(
            'allowEmpty' => false
         ),
         'fileName' => array(
            'allowEmpty' => false
         ),
         'mimeType' => array(
            'allowEmpty' => true,
            'default' => ''
         ),
         'encoding' => array(
            'allowEmpty' => true,
            'default' => ''
         ),
         'content' => array(
            'allowEmpty' => true,
            'default' => ''
         ),
         'contentId' => array(
            'allowEmpty' => true,
            'default' => ''
         ),
         'key' => array(
            'allowEmpty' => false,
            array('StringLength', 32, 32)
         )
    );

    protected function _init()
    {
        $this->_defaultEscapeFilter = new Conjoon_Filter_Raw();
    }



}