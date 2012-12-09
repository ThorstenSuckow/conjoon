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


com.conjoon.groupware.email.EmailItemRecord = Ext.data.Record.create([
    {name : 'id',                      type : 'string'},
    {name : 'isAttachment',            type : 'bool'},
    {name : 'isRead',                  type : 'bool'},
    {name : 'recipients',              type : 'string'},
    {name : 'subject',                 type : 'string'},
    {name : 'sender',                  type : 'string'},
    {name : 'date',                    type : 'date', dateFormat : 'Y-m-d H:i:s'},
    {name : 'isSpam',                  type : 'bool'},
    {name : 'isDraft',                 type : 'bool'},
    {name : 'isOutboxPending',         type : 'bool'},
    {name : 'groupwareEmailFoldersId', type : 'string'},
    {name : 'path',                    type : 'array'},
    {name : 'referencedAsTypes'}
]);