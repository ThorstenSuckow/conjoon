/**
 * conjoon
 * (c) 2002-2012 siteartwork.de/conjoon.org
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
 * A record for storing local/IMAP folder mappings.
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
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
     * @type {Number} groupwareEmailAccountsId The id of the related account
     */
    {name : 'groupwareEmailAccountsId',  type : 'int'},
    /**
     * @type {String} globalName The global name of the folder mapped to "type",
     * or the id of this folder in case the related account is a POP3 account.
     */
    {name : 'globalName',          type : 'string'},
    /**
     * @type {String} type The type of the mapping. Can be any of
     * INBOX, DRAFT, SENT, OUTBOX, TRASH
     */
    {name : 'type',                 type : 'string'}
]);