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

Ext.namespace('de.intrabuild.util.PreLoader');

de.intrabuild.util.PreLoader = function() {

    var _kernel = function(){
        this.addEvents(
            /**
             * Fired when all objects have been sucessfully loaded
             */
            'load'
        );
    };

    Ext.extend(_kernel, Ext.util.Observable, {

    });

    var kernel = new _kernel();

    var stores = {};

    var storeCount = 0;

    var storeLoaded = function(store)
    {
        store.un('load',          storeLoaded, de.intrabuild.util.PreLoader);
        store.un('loadexception', storeLoaded, de.intrabuild.util.PreLoader);

        storeCount--;
        if (storeCount == 0) {
            kernel.fireEvent('load');
        } else if (storeCount < 0 ) {
            throw('de.intrabuild.util.PreLoader: storeCount is negative, but most not be.');
        }
    };

    var storeDestroyed = function(store)
    {
        store.un('load', storeLoaded, de.intrabuild.util.PreLoader);
        storeCount--;
        delete stores[Ext.StoreMgr.getKey(store)];
    };

    return {

        on : function(eventName, fn, scope, parameters)
        {
            kernel.on(eventName, fn, scope, parameters);
        },

        /**
         * Adds a store to the preloader.
         *
         * @param {Ext.data.Store} store The store to add to the preloader
         * @param {Boolean|function} true to treat a loadexception from the store as
         * a usual load event, or a callback to be executed when loading fails
         * @param {Object} scope The scope to use for the callback passed as the
         * second argument. If ommited, the "window" scope will be used
         */
        addStore : function(store, continueOnLoadException, scope)
        {
            var id = Ext.StoreMgr.getKey(store);

            if (!id) {
                throw('de.intrabuild.util.PreLoader: store must have a property storeId or id.');
            }

            if (stores[id]) {
                throw('de.intrabuild.util.PreLoader: store with id '+id+' was already added.');
            }

            store.on('load', storeLoaded, de.intrabuild.util.PreLoader);

            if (continueOnLoadException === true) {
                store.on('loadexception', storeLoaded, de.intrabuild.util.PreLoader, {single : true});
            } else if (typeof continueOnLoadException == "function") {
                store.on('loadexception', continueOnLoadException, (scope ? scope : window), {single : true});
            }

            store.on('destroy', storeDestroyed, de.intrabuild.util.PreLoader);
            stores[id] = store;
            storeCount++;
        },

        load : function()
        {
            for (var i in stores) {
                stores[i].load();
            }
        }

    };

}();