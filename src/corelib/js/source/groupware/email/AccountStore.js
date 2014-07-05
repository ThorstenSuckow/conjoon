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

/**
 * @class com.conjoon.groupware.email.AccountStore
 * @singleton
 */
com.conjoon.groupware.email.AccountStore = function() {

    var _store = null;

    var _getStore = function()
    {
        return new Ext.data.Store({
            storeId  : Ext.id(),
            proxy    : new com.conjoon.cudgets.data.DirectProxy({
                api : {
                    read : com.conjoon.groupware.provider.emailAccount.getEmailAccounts
                }
            }),
            autoLoad             : false,
            pruneModifiedRecords : true,
            reader               : new com.conjoon.cudgets.data.JsonReader({
                root : 'accounts',
                id   : 'id'
            }, com.conjoon.groupware.email.AccountRecord)
        });
    };

    return {

        /**
         *
         * @return {Ext.data.Store}
         */
        getInstance : function()
        {
            if (_store === null) {
                _store = _getStore();
            }

            return _store;
        },

        /**
         * Returns the record which is currently marked as "standard" account.
         * If "useFallback" is set to "true" and no record is found where
         * "isStandard == true", the first record in the store will be returned.
         *
         * @param {Boolean} useFallback Whether to return the first record found
         * in the store when no standard account was found.
         *
         * @return {com.conjoon.groupware.email.AccountRecord}
         */
        getStandardAccount : function(useFallback)
        {
            this.getInstance();

            var isStandardIndex = _store.find('isStandard', true);
            var standardAcc     = _store.getAt(isStandardIndex);

            if (!standardAcc && useFallback !== false) {
                standardAcc = _store.getAt(0);
            }

            return standardAcc;
        }

    };

}();