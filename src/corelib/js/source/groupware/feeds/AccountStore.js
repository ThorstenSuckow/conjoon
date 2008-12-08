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
 * @class com.conjoon.groupware.feeds.AccountStore
 * @singleton
 */
com.conjoon.groupware.feeds.AccountStore = function() {

    var _store = null;

    var _getStore = function()
    {
        return new Ext.data.Store({
            storeId     : Ext.id(),
            autoLoad    : false,
            reader      : new Ext.data.JsonReader({
                              root: 'accounts',
                              id : 'id'
                          }, com.conjoon.groupware.feeds.AccountRecord),
            url         : '/groupware/feeds/get.feed.accounts/format/json',
            listeners   : com.conjoon.groupware.feeds.FeedRunner.getListener()
        });
    };

    return {

        /**
         *
         * @return {Ext.data.Store}
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