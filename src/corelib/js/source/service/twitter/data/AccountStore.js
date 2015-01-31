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

Ext.namespace('com.conjoon.service.twitter.data');

/**
 * A store for querying Twitter accounts as stored in the database backend of the
 * server.
 *
 * @class com.conjoon.service.twitter.data.AccountStore
 * @singleton
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
com.conjoon.service.twitter.data.AccountStore = function() {

    var _store = null;

    var _getStore = function() {
        return new Ext.data.Store({
            storeId : Ext.id(),
            proxy   : new com.conjoon.cudgets.data.DirectProxy({
                api : {
                    read : com.conjoon.service.provider.twitterAccount.getAccounts
                }
            }),
            autoLoad : false,
            reader   : new com.conjoon.cudgets.data.JsonReader({
                    root            : 'accounts',
                    id              : 'id',
                    successProperty : 'success'
                }, com.conjoon.service.twitter.data.AccountRecord)
        });
    };


    return {

        getInstance : function()
        {
            if (!_store) {
                _store = _getStore();
            }

            return _store;
        }

    }

}();