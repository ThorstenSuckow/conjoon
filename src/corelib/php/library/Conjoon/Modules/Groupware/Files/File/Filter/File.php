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
 * processing files.
 *
 * @uses Conjoon_Filter_Input
 * @package    Conjoon_Filter_Input
 * @category   Filter
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Modules_Groupware_Files_File_Filter_File extends Conjoon_Filter_Input {

    const CONTEXT_DOWNLOAD_REQUEST = 'download_request';

    protected $_presence = array(
        self::CONTEXT_DOWNLOAD_REQUEST => array(
            'id',
            'key',
            'downloadCookieName',
            'type',
            'name'
        )
    );

    protected $_filters = array(
        'id' => array(
            'Int'
        ),
         'key' => array(
            'StringTrim'
         ),
         'downloadCookieName' => array(
            'StringTrim'
         ),
         'type' => array(
            'StringTrim'
         ),
         'name' => array(
            'StringTrim',
            'Urldecode'
         )
    );

    protected $_validators = array(
        'id' => array(
            'allowEmpty' => false,
            'default'    => 0
         ),
         'key' => array(
            'allowEmpty' => false,
            array('StringLength', 32, 32)
         ),
         'downloadCookieName' => array(
            'allowEmpty' => false
         ),
         'type' => array(
            'allowEmpty' => false,
            array('InArray', array('file', 'emailAttachment'))
         ),
         'name' => array(
            'allowEmpty' => true
         )

    );

    protected function _init()
    {
        $this->_defaultEscapeFilter = new Conjoon_Filter_Raw();
    }



}