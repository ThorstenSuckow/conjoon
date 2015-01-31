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
 * @see Zend_Filter_Interface
 */
require_once 'Zend/Filter/Interface.php';

/**
 * @see Conjoon_Modules_Groupware_Email_Keys
 */
require_once 'Conjoon/Modules/Groupware/Email/Keys.php';

/**
 * @category   Filter
 * @package    Conjoon_Filter
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Modules_Groupware_Email_Draft_Filter_ReferenceType implements Zend_Filter_Interface
{
    /**
     * Defined by Zend_Filter_Interface
     *
     * Returns text/plain, text/html or multipart based on the passed parameter.
     *
     * @param  mixed $value
     * @return string
     */
    public function filter($value)
    {
        $str = trim((string)$value);

        switch ($str) {

            case Conjoon_Modules_Groupware_Email_Keys::REFERENCE_TYPE_NEW:
            case Conjoon_Modules_Groupware_Email_Keys::REFERENCE_TYPE_EDIT:
            case Conjoon_Modules_Groupware_Email_Keys::REFERENCE_TYPE_REPLY:
            case Conjoon_Modules_Groupware_Email_Keys::REFERENCE_TYPE_REPLY_ALL:
            case Conjoon_Modules_Groupware_Email_Keys::REFERENCE_TYPE_FORWARD:
                return $str;
            default:
                return Conjoon_Modules_Groupware_Email_Keys::REFERENCE_TYPE_NEW;
        }
    }
}
