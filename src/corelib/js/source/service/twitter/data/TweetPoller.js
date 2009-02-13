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

Ext.namespace('com.conjoon.service.twitter.data');

/**
 * A store for querying Twitter status updates in a frequent interval.
 * Polled tweets will be compared with existing tweets of the specified store.
 * If not found, the polled tweets will be added to the store specified.
 *
 * @class com.conjoon.service.twitter.data.TweetPoller
 * @extends Ext.data.Store
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
com.conjoon.service.twitter.data.TweetPoller = function(config){

    config = config || {};

    this.addEvents(
        /**
         * @event updateempty
         * @param {com.conjoon.service.twitter.data.TweetPoller} tweetPoller
         */
        'updateempty'
    )

    com.conjoon.service.twitter.data.TweetPoller.superclass.constructor.call(this, Ext.apply(config, {
        reader : new Ext.data.JsonReader({
            root : 'tweets',
            id   : 'id'
        }, com.conjoon.service.twitter.data.TweetRecord),
        autoLoad   : false
    }));

    this.on('load', this._onLoad, this);
};

Ext.extend(com.conjoon.service.twitter.data.TweetPoller, Ext.data.Store, {

    /**
     * @cfg {com.conjoon.service.twitter.data.TweetStore} updateStore
     * The store which contents are to be compared when this store loads
     * new tweets.
     */

    /**
     * @cfg {Number} updateInterval The update interval of this store, in milliseconds.
     * Defaults to 60000 (1 minute). It serves only as a fallback if the updateInterval
     * argument was not specified during a call to startPolling
     */
    updateInterval : 60 * 1000,

    /**
     * @type {Ext.util.TaskRunner} _task The task which triggers the polling of
     * new data in a frequent interval. May be null if there is currently no task
     * running.
     * @protected
     */
    _task : null,

    /**
     * @type {Object} _taskConfig The configuration for the TaskRunner
     * @protected
     */
    _taskConfig : null,

    /**
     * @type {Boolean} _taskCalled A helper property to determine whether
     * the task has processed at least once since polling was started.
     * @protected
     */
    _taskCalled : false,

// -------- public API

    /**
     * returns true if the task is currently running, oterwise false.
     */
    isPolling : function()
    {
        return this._task != null;
    },

    /**
     * Starts polling the server for new tweets.
     *
     * @param {Number} id The id of the account as configured by the server
     * to poll the tweets for,
     * @param {Number} updateInterval The interval in which the task polls for
     * new tweets, in miliseconds
     */
    startPolling : function(accountId, updateInterval)
    {
        this.stopPolling();

        if (!updateInterval) {
            updateInterval = this.updateInterval;
        }

        this._taskConfig = {
            args     : [accountId, true],
            run      : this.pollTweets,
            scope    : this,
            interval : updateInterval
        };

        this._task = new Ext.util.TaskRunner();
        this._task.start(this._taskConfig);
    },

    /**
     * Stops polling the server for new tweets.
     */
    stopPolling : function()
    {
        this.removeAll();
        this._taskCalled = false;

        if (this._task) {
            this._task.stop(this._taskConfig);
        }

        this._task = null;
    },

    /**
     * Sends a request to the server to poll for new tweets.
     *
     * @param {Number}  accountId The id of the account to poll new tweets for
     * @param {Boolean} useCheck if true, the method will check if the method has
     * been called since the polling started. If this is not true, the first call to
     * this method will do nothing.
     */
    pollTweets : function(accountId, useCheck)
    {
        if (useCheck === true) {
            if (!this._taskCalled) {
                this._taskCalled = true;
                return;
            }
        }

        this._taskCalled = true;

        this.load({
            params : {
                id : accountId
            }
        });
    },

// -------- listeners

    /**
     * Callback for this store's load event.
     * Will compare the fetched records with the exsiting in updateStore
     * and add those records which were not already in updateStore.
     * Files the updateempty event if, and only if no records have been
     * added to updateStore
     *
     * @param {Ext.data.Store} store
     * @param {Array} records
     * @param {Object} options
     */
    _onLoad : function(store, records, options)
    {
        var updStore = this.updateStore;
        var rec      = null;

        var updated = false;

        for (var i = 0, len = records.length; i < len; i++) {
            rec = records[i];
            if (!updStore.getById(rec.id)) {
                updStore.addSorted(rec.copy());
                updated = true;
            }
        }

        if (!updated) {
            this.fireEvent('updateempty', this);
        }

        this.removeAll();
    }
});