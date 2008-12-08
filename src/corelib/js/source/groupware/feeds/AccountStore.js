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

Ext.namespace('de.intrabuild.groupware.feeds');

/**
 * @class de.intrabuild.groupware.feeds.AccountStore
 * @singleton
 */
de.intrabuild.groupware.feeds.AccountStore = function() {

    var _store = null;

    var _getStore = function()
    {
        return new Ext.data.Store({
            storeId     : Ext.id(),
            autoLoad    : false,
            reader      : new Ext.data.JsonReader({
                              root: 'accounts',
                              id : 'id'
                          }, de.intrabuild.groupware.feeds.AccountRecord),
            url         : '/groupware/feeds/get.feed.accounts/format/json',
            listeners   : de.intrabuild.groupware.feeds.FeedRunner.getListener()
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