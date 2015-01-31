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
 * @see Zend_Filter_HtmlEntities
 */
require_once 'Zend/Filter/HtmlEntities.php';


/**
 * A filter used for preparing data fetched from the database for sending as
 * a response to the client.
 *
 * @uses Conjoon_Filter_Input
 * @package    Conjoon_Modules_Groupware_Email
 * @category   Filter
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Modules_Groupware_Email_Item_Filter_ItemResponse extends Conjoon_Filter_Input {

    protected $_presence = array(
         self::CONTEXT_RESPONSE => array(
            'id',
            'recipients',
            'sender',
            'subject',
            'date',
            'isRead',
            'isAttachment',
            'isSpam',
            'isDraft',
            'isOutboxPending',
            'referencedAsTypes',
            'groupwareEmailFoldersId',
            'path'
        )
    );

    protected $_filters = array(
         'date' => array(
            'DateUtcToLocal'
         )
    );



    protected function _init()
    {
        /**
         * @see Conjoon_Modules_Groupware_Email_Item_Filter_ReferenceTypes
         */
        require_once 'Conjoon/Modules/Groupware/Email/Item/Filter/ReferenceTypes.php';

        $this->_filters['referencedAsTypes'] = array(
            new Conjoon_Modules_Groupware_Email_Item_Filter_ReferenceTypes()
        );

        $this->_defaultEscapeFilter = new Zend_Filter_HtmlEntities(
            array(
                'quotestyle' => ENT_COMPAT/*,
                'charset'    => 'UTF-8'*/
            )
        );
    }


}