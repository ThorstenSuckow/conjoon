/**
 * conjoon
 * (c) 2007-2014 conjoon.org
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
 * @class com.conjoon.groupware.feeds.FeedStore
 * @singleton
 */
com.conjoon.groupware.feeds.FeedStore = function() {

    var _store = null;

    var _getStore = function()
    {
        return new Ext.data.GroupingStore({
            storeId     : Ext.id(),
            autoLoad    : false,
            reader      : new com.conjoon.cudgets.data.JsonReader({
                              root: 'items',
                              id : 'id'
                          }, com.conjoon.groupware.feeds.ItemRecord),
            sortInfo    : {field: 'pubDate', direction: "DESC"},
            groupField  : 'name',
            baseParams  : {
                removeold : true,
                timeout   : com.conjoon.groupware.feeds.AccountStore.getTimeoutSum()
            },
            proxy   : new com.conjoon.cudgets.data.DirectProxy({
                api : {
                    read : com.conjoon.groupware.provider.feedsItem.getFeedItems
                },
                timeout  : com.conjoon.groupware.feeds.AccountStore.getTimeoutSum()
            })
        });
    };

    var _onFeedLoadFailure = function(subject, message)
    {
        var store = com.conjoon.groupware.feeds.FeedStore.getInstance();
        var rec   = store.getById(message.id);

        if (rec) {
            store.remove(rec);
        }
    };

    Ext.ux.util.MessageBus.subscribe(
        'com.conjoon.groupware.feeds.FeedViewBaton.onFeedLoadFailure',
        _onFeedLoadFailure
    );

    Ext.ux.util.MessageBus.subscribe(
        'com.conjoon.groupware.feeds.FeedPreview.onLoadFailure',
        _onFeedLoadFailure
    );

    return {

        /**
         * Returns the default timeout for any connection for getting the
         * latest feed items in miliseconds.
         *
         * @return {Number}
         *
         * @deprecated use com.conjoon.groupware.feeds.AccountStore.getTimeoutSum instead
         */
        getDefaultTimeOut : function()
        {
            return 30*1000;
        },

        /**
         * Updates the provider used for fetching feed items to re-assign the
         * new timeout.
         *
         * @pram {Number} timeout
         */
        setProviderTimeout : function(timeout)
        {
            var prov = Ext.Direct.getProvider('com.conjoon.groupware.provider');
            prov.clearTimeoutCache();
            var actions = prov.actions.feedsItem;
            for (var i = 0, len = actions.length; i < len; i++) {
                if (actions[i].name === 'getFeedItems') {
                    actions[i].timeout = timeout;
                    break;
                }
            }
        },

        /**
         * Returns the singleton instance of the store.
         *
         * @return {Ext.data.GroupingStore}
         */
        getInstance : function()
        {
            if (_store === null) {
                _store = _getStore();
                _store.on('beforeload', function(store, options) {
                    var timeout = com.conjoon.groupware.feeds.AccountStore.getTimeoutSum();

                    options.params.timeout = timeout;

                    if (this.baseParams.timeout == timeout) {
                        return;
                    }

                    this.baseParams.timeout = timeout;
                    com.conjoon.groupware.feeds.FeedStore.setProviderTimeout(timeout);

                }, _store);
            }

            return _store;
        }

    };

}();