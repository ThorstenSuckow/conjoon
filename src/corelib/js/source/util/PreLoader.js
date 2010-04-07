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

Ext.namespace('com.conjoon.util.PreLoader');

com.conjoon.util.PreLoader = function() {

    var _loadsAfter = [];

    var _kernel = function(){
        this.addEvents(
            /**
             * @event load
             * Fired when all objects have been sucessfully loaded
             */
            'load',
            /**
             * @event storeload
             * Fired when a single store was loaded
             * @param {Ext.data.Store} store
             */
            'storeload',
            /**
             * @event storeloadexception
             * Fired when loading a single store failed
             *
             * @param {Ext.data.Store} store
             */
            'storeloadexception',
            /**
             * @event beforestoreload
             * Fired before a single store is about to load
             *
             * @param {Ext.data.Store} store
             */
            'beforestoreload'
        );

        _kernel.superclass.constructor.call(this);
    };

    Ext.extend(_kernel, Ext.util.Observable, {

    });

    var kernel = new _kernel();

    var stores = {};

    var storeCount = 0;

    var storeLoaded = function(store)
    {
        storeCount--;
        if (storeCount == 0) {
            kernel.fireEvent('load');
        } else if (storeCount < 0 ) {
            throw('com.conjoon.util.PreLoader: storeCount is negative, but most not be.');
        }

        for (var i = 0, len = _loadsAfter.length; i < len; i++) {
            if (_loadsAfter[i].after == Ext.StoreMgr.getKey(store)) {
                Ext.StoreMgr.lookup(_loadsAfter[i].id).load();
            }
        }
    };

    var storeDestroyed = function(store)
    {
        store.un('load', _internLoad, com.conjoon.util.PreLoader);
        storeCount--;
        delete stores[Ext.StoreMgr.getKey(store)];
    };

    var _internException = function(store) {
        store.un('load', _internLoad, com.conjoon.util.PreLoader);
        storeLoaded(store);
    };

    var _internLoad = function(store) {
         store.un('exception', _internException, com.conjoon.util.PreLoader);
         kernel.fireEvent('storeload', store);
         storeLoaded(store);
    };

    return {

        getStoreConfig : function(store)
        {
            var id = Ext.StoreMgr.getKey(store);

            if (!id || !(stores[id])) {
                return;
            }

            return stores[id]['config'];
        },

        on : function(eventName, fn, scope, parameters)
        {
            kernel.on(eventName, fn, scope, parameters);
        },

        un : function(eventName, fn, scope, parameters)
        {
            kernel.un(eventName, fn, scope);
        },

        /**
         * Adds a store to the preloader.
         *
         * @param {Ext.data.Store} store The store to add to the preloader
         * @param {Object} config A configuration object with the following properties:
         *  - ignoreLoadException {Boolean} Whether to treat a loadexception as
         *    a load event and continue with processing remaining stores in the Preloader
         *  - exceptionCallback {Function} a custom callback for a loadexception
         *  - scope {Object} The scope of the exceptionCallback
         *  - loadAfterStore {Ext.data.Store} A store which load event should trigger the
         *    load process of the passed store.
         *
         * Note: ignoreLoadExcpetion will be given presedence before exceptionCallback,
         * so if you configure ignoreLoadException with true, exceptionCallback will be
         * ignored
         */
        addStore : function(store, config)
        {
            var config = config || {};

            var id = Ext.StoreMgr.getKey(store);

            if (!id) {
                throw('com.conjoon.util.PreLoader: store must have a property storeId or id.');
            }

            if (stores[id]) {
                throw('com.conjoon.util.PreLoader: store with id '+id+' was already added.');
            }

            var preLoader = com.conjoon.util.PreLoader;

            // add internal listeners
            store.on('beforeload', function(store) {
                kernel.fireEvent('beforestoreload', store);
            }, preLoader,  {single : true});
            store.on('exception', function(proxy, type, action, options, response, arg){
                kernel.fireEvent('storeloadexception', store, response, options);
            }, preLoader,  {single : true});
            store.on('load', _internLoad, preLoader,  {single : true});

            if (config.loadAfterStore) {
                _loadsAfter.push({
                    id    : id,
                    after : Ext.StoreMgr.getKey(config.loadAfterStore)
                });
            }

            if (config.ignoreLoadException === true) {
                store.on(
                    'exception',
                    _internException.createDelegate(preLoader, [store]),
                    preLoader, {single : true}
                );
            } else if (typeof config.exceptionCallback == "function") {
                store.on(
                    'exception',
                    config.exceptionCallback,
                    (config.scope ? config.scope : window),
                    {single : true}
                );
            }

            store.on('destroy', storeDestroyed, preLoader);
            stores[id] = {
                store  : store,
                config : config
            };
            storeCount++;
        },

        load : function()
        {
            var skip = false;
            for (var i in stores) {
                skip = false;
                for (var a = 0, len = _loadsAfter.length; a < len; a++) {
                    if (_loadsAfter[a].id == Ext.StoreMgr.getKey(stores[i]['store'])) {
                        skip = true;
                        break;
                    }
                }
                if (!skip) {
                    stores[i]['store'].load();
                }
            }
        }

    };

}();