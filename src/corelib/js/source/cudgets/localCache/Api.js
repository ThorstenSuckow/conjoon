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

Ext.namespace('com.conjoon.cudgets.localCache');

/**
 *
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 *
 * @class com.conjoon.cudgets.localCache.Api
 * @singleton
 */
com.conjoon.cudgets.localCache.Api = function() {

    var adapter = null;

    var listeners= {
        beforeclear  : [],
        clearsuccess : [],
        clearfailure : [],
        beforebuild  : [],
        buildsuccess : [],
        buildfailure : []
    };

    var checkForAdapter = function() {
        if (!adapter) {
            throw("com.conjoon.cudgets.localCache.Api - no adapter specified");
        }
    };

    var callListener = function(type, adapter) {

        var cb             = null;
        var callbackConfig = null;
        switch (type) {
            case 'beforebuild':
                cb = listeners.beforebuild;
            break;

            case 'beforeclear':
                cb = listeners.beforeclear;
            break;

            case 'buildsuccess':
                cb = listeners.buildsuccess;
            break;

            case 'clearsuccess':
                cb = listeners.clearsuccess;
            break;

            case 'buildfailure':
                cb = listeners.buildfailure;
            break;

            case 'clearfailure':
                cb = listeners.clearfailure;
            break;

            default:
                throw(
                    "com.conjoon.cudgets.localCache.Api: unknown event "
                    + "\""+type+"\" for private method \"callListener()\""
                );
            break;
        }

        for (var i = 0, len = cb.length; i < len; i++) {
            callbackConfig = cb[i];
            callbackConfig[0].call(callbackConfig[1], adapter);
        }

    };

    return {

        type : {
            NONE : 'none'
        },

        UNAVAILABLE : -1,
        UNCACHED    : 0,
        IDLE        : 1,
        CHECKING    : 2,
        DOWNLOADING : 3,
        UPDATEREADY : 4,
        OBSOLETE    : 5,

        onBeforeClear : function(fn, scope)
        {
            listeners.beforeclear.push([fn, scope ? scope : window]);
        },

        onClearSuccess : function(fn, scope)
        {
            listeners.clearsuccess.push([fn, scope ? scope : window]);
        },

        onClearFailure : function(fn, scope)
        {
            listeners.clearfailure.push([fn, scope ? scope : window]);
        },

        onBeforeBuild : function(fn, scope)
        {
            listeners.beforebuild.push([fn, scope ? scope : window]);
        },

        onBuildSuccess : function(fn, scope)
        {
            listeners.buildsuccess.push([fn, scope ? scope : window]);
        },

        onBuildFailure : function(fn, scope)
        {
            listeners.buildfailure.push([fn, scope ? scope : window]);
        },

        /**
         * Returns the cache type from the used adapter.
         */
        getCacheType : function()
        {
            checkForAdapter();

            if (!this.isCacheAvailable()) {
                return this.type.NONE;
            }

            return adapter.getCacheType();
        },

        /**
         * Checks whether the cache is available by the adapter.
         */
        isCacheAvailable : function()
        {
            checkForAdapter();

            return adapter.isCacheAvailable();
        },

        /**
         * Returns the adapter used by this class.
         */
        getAdapter : function()
        {
            return adapter;
        },

        /**
         * Sets the adapter to be used by the Api.
         */
        setAdapter : function(appAdapter)
        {
            if (adapter) {
                throw("com.conjoon.cudgets.localCache.Api already set");
            }

            var Api = com.conjoon.cudgets.localCache.Api;
            appAdapter.on(
                'beforeclear',
                function(adapter){callListener('beforeclear', adapter);},
                Api
            );
            appAdapter.on(
                'clearsuccess',
                function(adapter){callListener('clearsuccess', adapter);},
                Api
            );
            appAdapter.on(
                'clearfailure',
                function(adapter){callListener('clearfailure', adapter);},
                Api
            );
            appAdapter.on(
                'beforebuild',
                function(adapter){callListener('beforebuild', adapter);},
                Api
            );
            appAdapter.on(
                'buildsuccess',
                function(adapter){callListener('buildsuccess', adapter);},
                Api
            );
            appAdapter.on(
                'buildfailure',
                function(adapter){callListener('buildfailure', adapter);},
                Api
            );

            adapter = appAdapter;
        },

        /**
         * Clears the cache.
         */
        clearCache : function()
        {
            checkForAdapter();

            if (!this.isCacheAvailable()) {
                return false;
            }

            adapter.clearCache();
        },

        /**
         * Rebuilds the local cache.
         */
        buildCache : function()
        {
            checkForAdapter();

            if (!this.isCacheAvailable()) {
                return false;
            }

            adapter.buildCache();
        },

        /**
         * Returns the current status of the cache. Possible
         * values are:
         *
         *  UNAVAILABLE
         *  UNCACHED
         *  IDLE
         *  CHECKING
         *  DOWNLOADING
         *  UPDATEREADY
         *  OBSOLETE
         *
         * @return {Number}
         */
        getStatus : function()
        {
            checkForAdapter();

            if (!this.isCacheAvailable()) {
                return this.UNAVAILABLE;
            }

            var states = com.conjoon.cudgets.localCache.Adapter.status;

            var state = adapter.getStatus();

            switch (state) {
                case states.UNCACHED:
                    return this.UNCACHED;

                case states.IDLE:
                    return this.IDLE;

                case states.CHECKING:
                    return this.CHECKING;

                case states.DOWNLOADING:
                    return this.DOWNLOADING;

                case states.UPDATEREADY:
                    return this.UPDATEREADY;

                case states.OBSOLETE:
                    return this.OBSOLETE;

                default:
                    return this.UNAVAILABLE;
            }
        }

    };

}();