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
    {name : 'groupwareEmailAccountsId', type : 'int'}

]);
