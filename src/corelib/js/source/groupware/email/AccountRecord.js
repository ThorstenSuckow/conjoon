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

Ext.namespace('com.conjoon.groupware.email');

com.conjoon.groupware.email.AccountRecord = Ext.data.Record.create([
    {name : 'id',                   type : 'int'},
    {name : 'name',                 type : 'string'},
    {name : 'address',              type : 'string'},
    {name : 'replyAddress',         type : 'string'},
    {name : 'isStandard',           type : 'bool'},
    {name : 'protocol',             type : 'string'},
    {name : 'serverInbox',          type : 'string'},
    {name : 'serverOutbox',         type : 'string'},
    {name : 'usernameInbox',        type : 'string'},
    {name : 'usernameOutbox',       type : 'string'},
    {name : 'userName',             type : 'string'},
    {name : 'isOutboxAuth',         type : 'bool'},
    {name : 'passwordInbox',        type : 'string'},
    {name : 'passwordOutbox',       type : 'string'},
    {name : 'signature',            type : 'string'},
    {name : 'isSignatureUsed',      type : 'bool'},
    {name : 'portInbox',            type : 'int'},
    {name : 'portOutbox',           type : 'int'},
    {name : 'inboxConnectionType',  type : 'string'},
    {name : 'outboxConnectionType', type : 'string'},
    {name : 'isCopyLeftOnServer',   type : 'bool'}
]);