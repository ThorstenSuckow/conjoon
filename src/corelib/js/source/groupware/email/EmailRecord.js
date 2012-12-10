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

Ext.namespace('com.conjoon.groupware.email');


com.conjoon.groupware.email.EmailRecord = Ext.data.Record.create([
    {name : 'id', type : 'string'},
    {name : 'messageId', type : 'string'},
    {name : 'attachments'},
    {name : 'subject', type : 'string'},
    {name : 'from'},
    {name : 'date', type : 'date', dateFormat : 'Y-m-d H:i:s'},
    {name : 'isSpam', type : 'bool'},
    {name : 'isPlainText', type : 'bool'},
    {name : 'body', type : 'string'},
    {name : 'cc'},
    {name : 'to'},
    {name : 'replyTo'},
    {name : 'bcc'},
    {name : 'groupwareEmailFoldersId', type : 'int'}
]);