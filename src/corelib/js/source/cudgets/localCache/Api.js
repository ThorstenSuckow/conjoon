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
        clearfailure : []
    };

    var checkForAdapter = function() {
        if (!adapter) {
            throw("com.conjoon.cudgets.localCache.Api - no adapter specified");
        }
    };

    var onBeforeClearListener = function() {
        var beforeclear = listeners.beforeclear;
        var bc = null;
        for (var i = 0, len = beforeclear.length; i < len; i++) {
            bc = beforeclear[i];
            bc[0].call(bc[1]);
        }
    };

    var onClearSuccessListener = function() {
        var clearsuccess = listeners.clearsuccess;
        var cs = null;
        for (var i = 0, len = clearsuccess.length; i < len; i++) {
            cs = clearsuccess[i];
            cs[0].call(cs[1]);
        }
    };

    var onClearFailureListener = function() {
        var clearfailure = listeners.clearfailure;
        var cf = null;
        for (var i = 0, len = clearfailure.length; i < len; i++) {
            cf = clearfailure[i];
            cf[0].call(cf[1]);
        }
    };

    return {

        type : {
            NONE : 'none'
        },

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
            appAdapter.on('beforeclear',  onBeforeClearListener,  Api);
            appAdapter.on('clearsuccess', onClearSuccessListener, Api);
            appAdapter.on('clearfailure', onClearFailureListener, Api);

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
        }

    };

}();