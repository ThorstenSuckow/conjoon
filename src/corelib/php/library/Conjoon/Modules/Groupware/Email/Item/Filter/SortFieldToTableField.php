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
 * @category   Filter
 * @package    Conjoon_Filter
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
class Conjoon_Modules_Groupware_Email_Item_Filter_SortFieldToTableField implements Zend_Filter_Interface
{
    /**
     * Defined by Zend_Filter_Interface
     *
     * Returns the appropriate table's field name based on the passed sort field's
     * name.
     *
     * @param  mixed $value
     * @return integer
     */
    public function filter($value)
    {
        switch (trim(strtolower($value))) {
            case 'id':
                return 'id';
            case 'isattachment':
                return 'is_attachment';
            case 'recipients':
                return 'recipients';
            case 'sender':
                return 'sender';
            case 'isread':
                return 'is_read';
            case 'cc':
                return 'cc';
            case 'date':
                return 'date';
            case 'to':
                return 'to';
            case 'to':
                return 'to';
            case 'subject':
                return 'subject';
            case 'from':
                return 'from';
            case 'isspam':
                return 'is_spam';
            default:
                return 'subject';
        }
    }
}
