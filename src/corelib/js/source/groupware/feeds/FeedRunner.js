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

Ext.namespace('com.conjoon.groupware.feeds');

/**
 * @class com.conjoon.groupware.feeds.FeedRunner
 *
 */
com.conjoon.groupware.feeds.FeedRunner = function(){

    var commitId = Ext.data.Record.COMMIT;

    var store = new Ext.data.Store({
        autoLoad   : false,
        reader     : new com.conjoon.cudgets.data.JsonReader({
                          root: 'items',
                          id : 'id'
                      }, com.conjoon.groupware.feeds.ItemRecord),
        baseParams  : {
            removeold : false,
            timeout   : com.conjoon.groupware.feeds.AccountStore.getTimeoutSum()
        },
        proxy   : new com.conjoon.cudgets.data.DirectProxy({
            api : {
                read : com.conjoon.groupware.provider.feedsItem.getFeedItems
            },
            timeout  : com.conjoon.groupware.feeds.AccountStore.getTimeoutSum()
        })
    });

    var firstTimeLoaded = false;

    var task = null;

    var runnable = false;

    var defaultUpdateInterval = 3600;

    var updateInterval = 3600;

    var onStoreLoadException = function(proxy, type, action, options, response, arg)
    {
        com.conjoon.groupware.ResponseInspector.handleFailure(response);
    };

    var onStoreLoad = function(store, records, options)
    {
        if (!records || (records && !records.length)) {
            return;
        }
        var added = [];
        var cp    = null;
        for (var i = 0, len = records.length; i < len; i++) {
            cp = records[i].copy();
            feedStore.addSorted(cp);
            added.push(cp);
        }

        store.removeAll();
        if (len > 0) {
            Ext.ux.util.MessageBus.publish('com.conjoon.groupware.feeds.FeedRunner.newFeeds', {
                feeds : added
            });
            notifyUser(len);
        }
    };

    var onStoreChange = function(store, record, operation)
    {
        if (operation && (typeof(operation) == 'string') && operation != commitId) {
            return;
        }

        stopRunning();

        var recs = store.getRange();

        updateInterval = Number.MAX_VALUE;
        for (var i = 0, len = recs.length; i < len; i++) {
            updateInterval = Math.min(recs[i].get('updateInterval'), updateInterval);
        }
        if (len == 0) {
            updateInterval = -1;
        }

        run();
    };

    var stopRunning = function()
    {
        runnable = false;
        if (task) {
            Ext.TaskMgr.stop(task);
            task = null;
        }
    };

    var run = function()
    {
        if (task || updateInterval < 0) {
            return;
        }
        task = {
            run      : updateFeeds,
            interval : updateInterval * 1000
        }

        Ext.TaskMgr.start(task);
    };

    var updateFeeds = function()
    {
        if (!runnable) {
            runnable = true;
            return;
        }

        if (_reception.isLocked()) {
            return;
        }

        var timeout = com.conjoon.groupware.feeds.AccountStore.getTimeoutSum();

        if (store.baseParams.timeout != timeout) {
            store.baseParams.timeout = timeout;
            com.conjoon.groupware.feeds.FeedStore.setProviderTimeout(timeout);
        }

        if (!firstTimeLoaded) {
            store.load({params : {timeout : timeout, removeold : false}});
            firstTimeLoaded = true;
        } else {
            store.reload({params : {timeout : timeout, removeold : false}});
        }

    };

    /**
     *
     * @param {Number} feedCount any value > 0
     */
    var notifyUser = function(feedCount)
    {
        var text = String.format(
            com.conjoon.Gettext.ngettext("There is one new feed entry available", "There are {0} new feed entries available", feedCount),
            feedCount
        );

        new Ext.ux.ToastWindow({
            title   : com.conjoon.Gettext.ngettext("New feed entry available", "New feed entries available", feedCount),
            html    : text
        }).show(document);

    };

    // leave this here since listener only works if observer function gets defined before
    // (stopRunning, onStoreLoad)
    var feedStore = com.conjoon.groupware.feeds.FeedStore.getInstance();
    feedStore.on('load', function() {
        this.on('beforeload', stopRunning, com.conjoon.groupware.feeds.FeedRunner);
    }, feedStore, {single : true});

    store.on('load',      onStoreLoad, com.conjoon.groupware.feeds.FeedRunner);
    store.on('exception', onStoreLoadException, com.conjoon.groupware.feeds.FeedRunner);

    var _reception = com.conjoon.groupware.Reception;

    return {


        getListener : function()
        {
            return {
                add    : onStoreChange,
                remove : onStoreChange,
                update : onStoreChange,
                load   : onStoreChange
            };
        }

    };

}();