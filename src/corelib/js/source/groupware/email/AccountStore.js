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
            url      : './groupware/email/get.email.accounts/format/json',
            autoLoad : false,
            pruneModifiedRecords : true,
            reader   : new Ext.data.JsonReader({
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
        }

    };

}();