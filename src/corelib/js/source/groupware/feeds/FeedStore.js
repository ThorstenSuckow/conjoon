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
                timeout   : com.conjoon.groupware.feeds.FeedStore.getDefaultTimeOut()
            },
            proxy : new Ext.data.HttpProxy({
                url      : '/groupware/feeds/get.feed.items/format/json',
                timeout  : com.conjoon.groupware.feeds.FeedStore.getDefaultTimeOut()
            })
        });
    };

    return {

        /**
         * Returns the default timeout for any connection for getting the
         * latest feed items in miliseconds.
         *
         * @return {Number}
         */
        getDefaultTimeOut : function()
        {
            return 30*1000;
        },

        /**
         *
         * @return {Ext.data.GroupingStore}
         */
        getInstance : function()
        {
            if (_store === null) {
                _store = _getStore();
            }

            return _store;
        }

    };

}();