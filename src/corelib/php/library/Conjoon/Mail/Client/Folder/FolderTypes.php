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
 * $URL: http://svn.conjoon.org/trunk/src/corelib/php/library/Conjoon/Mail/Client/Folder/Folder
 */


namespace Conjoon\Mail\Client\Folder;

/**
 * @see Conjoon\Data\Meta\Mail\FolderTypes
 */
require_once 'Conjoon/Data/Meta/Mail/FolderTypes.php';

/**
 * Simple access to possible type values of a folder.
 *
 * @category   Conjoon_Mail
 * @package    Conjoon_Mail_Client
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
final class FolderTypes {

    /**
     * Returns all possible folder types for local folders representing the
     * first level of a complete folder hierarchy.
     *
     * @return array
     */
    public static function getFirstLevelFolderTypes() {

        return array(
            \Conjoon\Data\Meta\Mail\FolderTypes::INBOX,
            \Conjoon\Data\Meta\Mail\FolderTypes::DRAFT,
            \Conjoon\Data\Meta\Mail\FolderTypes::OUTBOX,
            \Conjoon\Data\Meta\Mail\FolderTypes::SPAM,
            \Conjoon\Data\Meta\Mail\FolderTypes::SENT,
            \Conjoon\Data\Meta\Mail\FolderTypes::TRASH
        );

    }

    /**
     * Returns all possible folder types in an array.
     *
     * @return array
     */
    public static function getFolderTypes() {
        return array(
            \Conjoon\Data\Meta\Mail\FolderTypes::INBOX,
            \Conjoon\Data\Meta\Mail\FolderTypes::DRAFT,
            \Conjoon\Data\Meta\Mail\FolderTypes::OUTBOX,
            \Conjoon\Data\Meta\Mail\FolderTypes::SPAM,
            \Conjoon\Data\Meta\Mail\FolderTypes::SENT,
            \Conjoon\Data\Meta\Mail\FolderTypes::TRASH,
            \Conjoon\Data\Meta\Mail\FolderTypes::FOLDER
        );
    }

    /**
     * Returns all possible folder types for local first level folders.
     *
     * @return array
     */
    public static function getFolderTypesForFirstLevelChildFolders() {

        return array(
            \Conjoon\Data\Meta\Mail\FolderTypes::FOLDER
        );

    }

}

