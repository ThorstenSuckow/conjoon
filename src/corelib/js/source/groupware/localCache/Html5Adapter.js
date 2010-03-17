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
 * This implementation does also take care of rendering ProgressBar-dialogs
 * where applicable.
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
 * Those events get translated to Ext-events. The event names are written
 * without the leading "on"
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
            this.fireEvent('error', this, e);
        } , this);
        EventManager.on(appCache, 'noupdate', function(e) {
            this.fireEvent('noupdate', this, e);
        } , this);
        EventManager.on(appCache, 'downloading', function(e) {
            this.fireEvent('downloading', this, e);
        } , this);
        EventManager.on(appCache, 'progress', function(e) {
            this.fireEvent('progress', this, e);
        } , this);
        EventManager.on(appCache, 'updateready', function(e) {
            this.fireEvent('updateready', this, e);
        } , this);
        EventManager.on(appCache, 'cached', function(e) {
            this.fireEvent('cached', this, e);
        } , this);
        EventManager.on(appCache, 'obsolete', function(e) {
            this.fireEvent('obsolete', this, e);
        } , this);
    }

    com.conjoon.groupware.localCache.Html5Adapter.superclass.constructor.call(this);
};

Ext.extend(com.conjoon.groupware.localCache.Html5Adapter, com.conjoon.cudgets.localCache.Adapter, {

    /**
     * @type {Number} cacheEntryCount The total number of cache entries. Will
     * be available after a request to clear the cache has been made.
     */
    cacheEntryCount : 0,

    /**
     * @type {Number} progressIndex The current count of processed files, if any.
     */
    progressIndex : 0,

    /**
     * @type {String} progressText The text to show in the progress bar while
     * files are being processed for caching
     */
    progressText : com.conjoon.Gettext.gettext("Processing file {0} of {1}"),

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
     * Clears the local cache.
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
                    this.cacheEntryCount = succ.cacheEntryCount;
                    this.on(
                        'updateready', this._removeClearFlag, this,
                        {single : true}
                    );

                    window.applicationCache.update();
                }

        }, this);
    },

    /**
     * Builds the local cache.
     */
    buildCache : function()
    {
        this.fireEvent('beforebuild', this);

        com.conjoon.defaultProvider.applicationCache.setClearFlag(
            {clear : true},
            function(provider, response) {
                var succ = com.conjoon.groupware.ResponseInspector.isSuccess(
                    response
                );
                if (succ === null || succ === false) {
                    this.fireEvent('buildfailure', this);
                } else {
                    this.cacheEntryCount = succ.cacheEntryCount;
                    this.on(
                        'updateready', this._removeClearFlag.createDelegate(this, ['build']), this,
                        {single : true}
                    );

                    window.applicationCache.update();
                }

        }, this);
    },

    getStatus : function()
    {
        var states = com.conjoon.cudgets.localCache.Adapter.status;
        var status = window.applicationCache.status;

        switch (status) {
            case 0:
                return states.UNCACHED;
            case 1:
                return states.IDLE;
            case 2:
                return states.CHECKING;
            case 3:
                return states.DOWNLOADING;
            case 4:
                return states.UPDATEREADY;
            case 5:
                return states.OBSOLETE;
        }
    },

// -------- helpers

    /**
     *
     * error
     * noupdate
     * downloading
     * progress
     * updateready
     * cached
     * obsolete
     *
     * @private
     */
    _build : function()
    {
        this.on('downloading', this._onDownloading, this, {single : true});
        this.on('progress',    this._onBuildProgress, this);
        this.on('updateready', this._onBuildUpdateReady, this, {single : true});

        //try {
            window.applicationCache.update();
        //} catch (e) {
        //    this.un('progress', this._onBuildProgress, this);
        //    this.progressIndex = 0;
        //    this.fireEvent('buildfailure', this);
        //}
    },

    /**
     *
     * @private
     */
    _onBuildUpdateReady : function()
    {
        window.applicationCache.swapCache();
        this.un('progress', this._onBuildProgress, this);
        this.progressIndex = 0;

        com.conjoon.SystemMessageManager.updateProgress(
            1, "", com.conjoon.Gettext.gettext("Finished!")
        );

        (function() {
            com.conjoon.SystemMessageManager.hide();
            this.fireEvent('buildsuccess', this);
        }).defer(1000, this);
    },

    /**
     *
     * @private
     */
    _onBuildProgress : function()
    {
        // a little workaround for FF/Webkit adding the manifest
        // as additional file to cache to the progress list
        if (this.progressIndex == 0) {
            this.progressIndex = -1;
            return;
        } else if (this.progressIndex == -1) {
            this.progressIndex = 1;
        } else {
            this.progressIndex++;
        }

        com.conjoon.SystemMessageManager.updateProgress(
            this.progressIndex/this.cacheEntryCount,
            String.format(
                this.progressText,
                this.progressIndex, this.cacheEntryCount
            )
        );
    },

    /**
     *
     * @private
     */
    _onDownloading : function()
    {
        com.conjoon.SystemMessageManager.progress(
            new com.conjoon.SystemMessage({
                text : com.conjoon.Gettext.gettext("Please wait, processing files..."),
                type : com.conjoon.SystemMessage.TYPE_PROGRESS
            }), {
                progressText : String.format(
                    this.progressText,
                    0, this.cacheEntryCount
                )
        });
    },

    /**
     *
     * @private
     */
    _buildPrepare : function()
    {
        com.conjoon.groupware.Registry.setValues({
            values      : [{
                key   : 'client/applicationCache/last-changed',
                value : (new Date()).getTime()
            }],
            success : function(provider, response, updated, failed) {
                this._build();
            },
            failure : function(provider, response, updated, failed) {
                this.fireEvent('buildfailure', this);
            },
            scope : this
        });

    },

    /**
     *
     * @private
     */
    _removeClearFlag : function(type)
    {
        com.conjoon.defaultProvider.applicationCache.setClearFlag(
            {clear : false},
            function(provider, response) {
                var succ = com.conjoon.groupware.ResponseInspector.isSuccess(response);
                if (succ === null || succ === false) {
                    if (type === 'build') {
                        this.fireEvent('buildfailure', this);
                    }else {
                        this.fireEvent('clearfailure', this);
                    }
                } else {
                    this.cacheEntryCount = succ.cacheEntryCount;
                    window.applicationCache.swapCache();
                    if (type === 'build') {
                        this._buildPrepare();
                    } else {
                        this.fireEvent('clearsuccess', this);
                    }
                }
            },
            this
        );
    }

});