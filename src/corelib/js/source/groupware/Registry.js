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

Ext.namespace('com.conjoon.groupware');

/**
 * @class com.conjoon.groupware.Registry
 * @singleton
 */
com.conjoon.groupware.Registry = function() {

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
            url         : './default/registry/get.entries/format/json'
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