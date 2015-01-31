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

Ext.namespace('com.conjoon.groupware.email.data');

/**
 *
 * @type {Ext.data.Record}
 */
com.conjoon.groupware.email.data.Draft = Ext.data.Record.create([

    /**
     * The id of the draft, if available
     */
    {name : 'id', type : 'int' },

    /**
     * If this draft references any other email when replying to it, this will store the id.
     * It will default to 0 or -1 if the draft does not reference any existing email item.
     */
    {name : 'referencesId', type : 'int' },

    /**
     * @type string
     */
    {name : 'inReplyTo', type : 'inReplyTo' },

    /**
     * @type string
     */
    {name : 'references', type : 'string' },


    /**
     * The format of the draft. can be 'text/plain', 'text/html' or 'multipart'
     */
    {name : 'format', type : 'string' },

    /**
     * Context of the draft. Can be reply, reply_all, forward or left empty (empty string)
     */
    {name : 'type', type : 'string' },

    /**
     * Unix timestamp  - the time the client composed the draft
     */
    {name : 'date', type : 'int' },

    /**
     * Subject for the draft
     */
    {name : 'subject', type : 'string' },

    /**
     * Message for this draft
     */
    {name : 'message', type : 'string' },

    /**
     * Json encoded string of recipients (comma separated)
     */
    {name : 'to', type : 'string' },

    /**
     * Json encoded string of recipients (comma separated)
     */
    {name : 'cc', type : 'string' },

    /**
     * Json encoded string of recipients (comma separated)
     */
    {name : 'bcc', type : 'string' },

    /**
     * int
     */
    {name : 'groupwareEmailFoldersId', type : 'int'},

    /**
     * int
     */
    {name : 'groupwareEmailAccountsId', type : 'int'},

    /**
     * int
     */
    {name : 'attachments'},

    /**
     * object
     */
    {name : 'referencesData'},
    /**
     * object
     */
    {name : 'path'}
]);
