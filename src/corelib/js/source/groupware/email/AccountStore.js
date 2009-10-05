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
            url      : './groupware/emailAccount/get.email.accounts/format/json',
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