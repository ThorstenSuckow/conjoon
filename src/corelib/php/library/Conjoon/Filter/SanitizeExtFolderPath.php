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
 * Strips unneeded information from a node path as build and submitted
 * by the Ext framework. The remaining fragment should only contain
 * database-ids and/or global names defined by an IMAP server, separated
 * by a "slash".
 * Note:
 * This filter should not be used anymore. Instead, the client should submit
 * paths as json encoded arrays to the backend, so no path separator pollution
 * occurs.
 *
 * @category   Filter
 * @package    Conjoon_Filter
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 *
 * @deprecated use Conjoon_Text_Parser_Mail_MailboxFolderPathJsonParser
 */
class Conjoon_Filter_SanitizeExtFolderPath implements Zend_Filter_Interface
{

    /**
     * Defined by Zend_Filter_Interface
     *
     * @param  string $value
     * @return string
     */
    public function filter($value)
    {
        $value = trim((string)$value);

        return rtrim(ltrim($value, '/root'), '/');
    }
}
