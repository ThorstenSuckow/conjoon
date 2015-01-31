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
    {name : 'isCopyLeftOnServer',   type : 'bool'},
    {name : 'folderMappings'},
    {name : ''}
]);