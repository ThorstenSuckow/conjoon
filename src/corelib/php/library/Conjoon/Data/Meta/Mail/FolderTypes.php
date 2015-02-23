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


namespace Conjoon\Data\Meta\Mail;


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
     * @type string
     */
    const INBOX = 'inbox';

    /**
     * @type string
     */
    const DRAFT = 'draft';

    /**
     * @type string
     */
    const OUTBOX = 'outbox';

    /**
     * @type string
     */
    const SPAM = 'spam';

    /**
     * @type string
     */
    const SENT = 'sent';

    /**
     * @type string
     */
    const TRASH = 'trash';

    /**
     * @type string
     */
    const FOLDER = 'folder';

    /**
     * @type string
     */
    const ROOT = 'root';

    /**
     * @type string
     */
    const ACCOUNTS_ROOT = 'accounts_root';

    /**
     * @type string
     */
    const ROOT_REMOTE = 'root_remote';

}