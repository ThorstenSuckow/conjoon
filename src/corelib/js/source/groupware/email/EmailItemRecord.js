/**
 * conjoon
 * (c) 2007-2014 conjoon.org
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