/**
 * conjoon
 * (c) 2002-2010 siteartwork.de/conjoon.org
 * licensing@conjoon.org
 *
 * $Author$
 * $Id$
 * $Date$
 * $Revision$
 * $LastChangedDate$
 * $LastChangedBy$
 * $URL$
 */

Ext.namespace('com.conjoon.groupware.email.options.folderMapping.data');

/**
 * A record for storing IMAP folder mappings.
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
com.conjoon.groupware.email.options.folderMapping.data.Record = Ext.data.Record.create([
    /**
     * @type {Number} id The id of this record
     */
    {name : 'id',                   type : 'int'},
    /**
     * @type {Number} rootFolderId The id of the root folder representing the
     * IMAP mailboxes
     */
    {name : 'rootFolderId',         type : 'int'},
    /**
     * @type {Number} accountId The id of the related account
     */
    {name : 'accountId',            type : 'int'},
    /**
     * @type {String} globalName The global name of the folder mapped to "type".
     */
    {name : 'globalName',          type : 'string'},
    /**
     * @type {String} type The type of the mapping. Can be any of
     * INBOX, DRAFT, SENT, OUTBOX, TRASH
     */
    {name : 'type',                 type : 'string'}
]);