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

Ext.namespace('com.conjoon.groupware.localCache');

/**
 * An concrete Application Cache implementation for use with
 * com.conjoon.cudgets.localCache.Api based on HTML5 specifications.
 *
 * Possible events fired by the window.applicationCache object:
 * Function onerror;
 * Function onnoupdate;
 * Function ondownloading;
 * Function onprogress;
 * Function onupdateready;
 * Function oncached;
 * Function onobsolete;
 *
 *
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 *
 * @class com.conjoon.groupware.localCache.Html5Adapter
 * @extends com.conjoon.cudgets.localCache.Adapter
 */
com.conjoon.groupware.localCache.Html5Adapter = function() {

    this.addEvents(
        'error',
        'noupdate',
        'downloading',
        'progress',
        'updateready',
        'cached',
        'obsolete'
    );

    var appCache = window.applicationCache;

    if (appCache) {
        var EventManager = Ext.EventManager;

        EventManager.on(appCache, 'error', function(e) {
            this.fireEvent('error', this);
        } , this);
        EventManager.on(appCache, 'noupdate', function(e) {
            this.fireEvent('noupdate', this);
        } , this);
        EventManager.on(appCache, 'downloading', function(e) {
            this.fireEvent('downloading', this);
        } , this);
        EventManager.on(appCache, 'progress', function(e) {
            this.fireEvent('error', this);
        } , this);
        EventManager.on(appCache, 'updateready', function(e) {
            this.fireEvent('updateready', this);
        } , this);
        EventManager.on(appCache, 'progress', function(e) {
            this.fireEvent('progress', this);
        } , this);
        EventManager.on(appCache, 'cached', function(e) {
            this.fireEvent('cached', this);
        } , this);
        EventManager.on(appCache, 'obsolete', function(e) {
            this.fireEvent('obsolete', this);
        } , this);
    }

    com.conjoon.groupware.localCache.Html5Adapter.superclass.constructor.call(this);
};

Ext.extend(com.conjoon.groupware.localCache.Html5Adapter, com.conjoon.cudgets.localCache.Adapter, {




// -------- com.conjoon.cudgets.localCache.Adapter

    /**
     * @return {Boolean}
     */
    isCacheAvailable : function()
    {
        return window.applicationCache ? true : false;
    },

    /**
     * @return {String}
     */
    getCacheType : function()
    {
        return 'HTML5 Application Cache';
    },

    /**
     *
     */
    clearCache : function()
    {
        // fire the beforeclear event
        this.fireEvent('beforeclear', this);

        com.conjoon.defaultProvider.applicationCache.setClearFlag(
            {clear : true},
            function(provider, response) {
                var succ = com.conjoon.groupware.ResponseInspector.isSuccess(
                    response
                );
                if (succ === null || succ === false) {
                    this.fireEvent('clearfailure', this);
                } else {

                    this.on(
                        'updateready', this._removeClearFlag, this,
                        {single : true}
                    );

                    try {
                        window.applicationCache.swapCache();
                        window.applicationCache.update();
                    } catch (e) {
                        // updateready possibly noot fired, make sure clear
                        // flag gets removed
                        this._removeClearFlag();
                    }
                }

        }, this);
    },

// -------- helpers

    _removeClearFlag : function()
    {
        com.conjoon.defaultProvider.applicationCache.setClearFlag(
            {clear : false},
            function(provider, response) {
                var succ = com.conjoon.groupware.ResponseInspector.isSuccess(response);
                if (succ === null || succ === false) {
                    this.fireEvent('clearfailure', this);
                } else {
                    this.fireEvent('clearsuccess', this);
                }
            },
            this
        );
    }

});