/**
 * intrabuild
 * (c) 2002-2008 siteartwork.de/MindPatterns
 * license@siteartwork.de
 *
 * $Author: T. Suckow $
 * $Id: AccountStore.js 50 2008-07-29 22:16:55Z T. Suckow $
 * $Date: 2008-07-30 00:16:55 +0200 (Mi, 30 Jul 2008) $
 * $Revision: 50 $
 * $LastChangedDate: 2008-07-30 00:16:55 +0200 (Mi, 30 Jul 2008) $
 * $LastChangedBy: T. Suckow $
 * $URL: file:///F:/svn_repository/intrabuild_rep/trunk/src/corelib/js/source/groupware/feeds/AccountStore.js $
 */

Ext.namespace('de.intrabuild.groupware');

/**
 * @class de.intrabuild.groupware.RegistryStore
 * @singleton
 */
de.intrabuild.groupware.Registry = function() {

    var _store = null;

    var _getStore = function()
    {
        return new Ext.data.Store({
            storeId     : Ext.id(),
            autoLoad    : false,
            reader      : new Ext.data.JsonReader({
                              root: 'entries',
                              id  : 'key'
                          }, ['key', 'value']),
            url         : '/default/registry/get.entries/format/json'
        });
    };

    return {

        /**
         *
         * @return {Ext.data.Store}
         */
        getStore : function()
        {
            if (_store === null) {
                _store = _getStore();
            }

            return _store;
        },

        /**
         * Get's the value for the specified key.
         * Returns null if the key was not found.
         *
         * @return {mixed}
         */
        get : function(key)
        {
            var rec = _store.getById(key);

            return (rec ? rec.get('value') : null);
        }
    };

}();