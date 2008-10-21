/**
 * intrabuild
 * (c) 2002-2008 siteartwork.de/MindPatterns
 * license@siteartwork.de
 *
 * $Author: T. Suckow $
 * $Id: EmailEditorManager.js 237 2008-10-20 12:24:12Z T. Suckow $
 * $Date: 2008-10-20 14:24:12 +0200 (Mo, 20 Okt 2008) $
 * $Revision: 237 $
 * $LastChangedDate: 2008-10-20 14:24:12 +0200 (Mo, 20 Okt 2008) $
 * $LastChangedBy: T. Suckow $
 * $URL: file:///F:/svn_repository/intrabuild_rep/trunk/src/corelib/js/source/groupware/email/EmailEditorManager.js $
 */

Ext.namespace('de.intrabuild.groupware.email.data');

/**
 *
 * @type {Ext.data.Record}
 */
de.intrabuild.groupware.email.data.Draft = Ext.data.Record.create([

    /**
     * The id of the draft, if available
     */
    {name : 'id', type : 'int' },

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
