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

Ext.namespace('com.conjoon.cudgets.localCache');

/**
 *
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 *
 * @class com.conjoon.cudgets.localCache.Api
 * @singleton
 */
com.conjoon.cudgets.localCache.Api = function() {

    var adapter = null;

    var Api = com.conjoon.cudgets.localCache.Api;

    var listeners= {
        beforeclear  : [],
        clearsuccess : [],
        clearfailure : [],
        beforebuild  : [],
        buildsuccess : [],
        buildfailure : [],
        buildcancel  : [],
        build        : []
    };

    var checkForAdapter = function() {
        if (!adapter) {
            throw("com.conjoon.cudgets.localCache.Api - no adapter specified");
        }
    };

    var getListenersForType = function(type) {

        switch (type) {
            case 'beforebuild':
                return listeners.beforebuild;

            case 'buildcancel':
                return listeners.buildcancel;

            case 'build':
                return listeners.build;

            case 'beforeclear':
                return listeners.beforeclear;

            case 'buildsuccess':
                return listeners.buildsuccess;

            case 'clearsuccess':
                return listeners.clearsuccess;

            case 'buildfailure':
                return listeners.buildfailure;

            case 'clearfailure':
                return listeners.clearfailure;

            default:
                throw(
                    "com.conjoon.cudgets.localCache.Api: unknown event "
                    + "\""+type+"\" for private method \"getListenersForType()\""
                );
        }

    };

    var removeListener = function(type, fn, scope) {

        var listeners = getListenersForType(type);
        var index     = -1;
        var listener  = null;
        for (var i = 0, len = listeners.length; i < len; i++) {
            listener = listeners[i];
            if (listener[0] == fn && listener[1] == scope) {
                index = i;
                break;
            }
        }

        if (index > -1) {
            listeners.splice(index, 1);
        }
    };

    var addListener = function(type, fn, scope) {
        var cb = getListenersForType(type);
        cb.push([fn, scope ? scope : window]);
    };

    var callListener = function(type, adapter) {

        var cb             = getListenersForType(type),
            callbackConfig = null,
            ret            = true;

        for (var i = 0, len = cb.length; i < len; i++) {
            callbackConfig = cb[i];

            if (type == 'beforebuild') {
                ret = callbackConfig[0].call(callbackConfig[1], adapter);

                if (ret === false) {
                    break;
                }
            } else {
                callbackConfig[0].call(callbackConfig[1], adapter);
            }
        }

        return ret;
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
            addListener('beforeclear', fn, scope);
        },

        onClearSuccess : function(fn, scope)
        {
            addListener('clearsuccess', fn, scope);
        },

        onClearFailure : function(fn, scope)
        {
            addListener('clearfailure', fn, scope);
        },

        onBeforeBuild : function(fn, scope)
        {
            addListener('beforebuild', fn, scope);
        },

        onBuild : function(fn, scope)
        {
            addListener('build', fn, scope);
        },

        onBuildCancel : function(fn, scope)
        {
            addListener('buildcancel', fn, scope);
        },

        onBuildSuccess : function(fn, scope)
        {
            addListener('buildsuccess', fn, scope);
        },

        onBuildFailure : function(fn, scope)
        {
            addListener('buildfailure', fn, scope);
        },

        unBeforeClear : function(fn, scope)
        {
            removeListener('beforeclear', fn, scope);
        },

        unClearSuccess : function(fn, scope)
        {
            removeListener('clearsuccess', fn, scope);
        },

        unClearFailure : function(fn, scope)
        {
            removeListener('clearfailure', fn, scope);
        },

        unBeforeBuild : function(fn, scope)
        {
            removeListener('beforebuild', fn, scope);
        },

        unBuildCancel : function(fn, scope)
        {
            removeListener('buildcancel', fn, scope);
        },

        unBuild : function(fn, scope)
        {
            removeListener('build', fn, scope);
        },

        unBuildSuccess : function(fn, scope)
        {
            removeListener('buildsuccess', fn, scope);
        },

        unBuildFailure : function(fn, scope)
        {
            removeListener('buildfailure', fn, scope);
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
                function(adapter){return callListener('beforebuild', adapter);},
                Api
            );
            appAdapter.on(
                'build',
                function(adapter){return callListener('build', adapter);},
                Api
            );
            appAdapter.on(
                'buildsuccess',
                function(adapter){callListener('buildsuccess', adapter);},
                Api
            );
            appAdapter.on(
                'buildcancel',
                function(adapter){callListener('buildcancel', adapter);},
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