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
            reader      : new Ext.data.JsonReader({
                              root: 'items',
                              id : 'id'
                          }, com.conjoon.groupware.feeds.ItemRecord),
            sortInfo    : {field: 'pubDate', direction: "DESC"},
            groupField  : 'name',
            baseParams  : {
                removeold : true,
                timeout   : com.conjoon.groupware.feeds.AccountStore.getTimeoutSum()
            },
            proxy : new Ext.data.HttpProxy({
                url      : './groupware/feeds/get.feed.items/format/json',
                timeout  : com.conjoon.groupware.feeds.AccountStore.getTimeoutSum()
            })
        });
    };

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
         * Returns the singleton instance of the store.
         *
         * @return {Ext.data.GroupingStore}
         */
        getInstance : function()
        {
            if (_store === null) {
                _store = _getStore();
                _store.on('beforeload', function() {
                    var timeout = com.conjoon.groupware.feeds.AccountStore.getTimeoutSum();
                    this.baseParams.timeout = timeout;
                    this.proxy.conn.timeout = timeout;
                }, _store);
            }

            return _store;
        }

    };

}();