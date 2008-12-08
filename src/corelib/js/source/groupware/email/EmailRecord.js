/**
 * conjoon
 * (c) 2002-2009 siteartwork.de/conjoon.org
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
    {name : 'id', type : 'int'},
    {name : 'attachments'},
    {name : 'subject', type : 'string'},
    {name : 'from'},
    {name : 'date', type : 'date', dateFormat : 'Y-m-d H:i:s'},
    {name : 'isSpam', type : 'boolean'},
    {name : 'isPlainText', type : 'boolean'},
    {name : 'body', type : 'string'},
    {name : 'cc'},
    {name : 'to'},
    {name : 'replyTo'},
    {name : 'bcc'},
    {name : 'groupwareEmailFoldersId', type : 'int'}
]);