/**
 * conjoon
 * (c) 2002-2012 siteartwork.de/conjoon.org
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

    var _defaultTimeout = 30*1000;

    var _commitId = Ext.data.Record.COMMIT;

    /**
     * Timeout for requests of this store as computed by the configured accounts,
     * in milliseconds.
     * @type {Number}
     */
    var _timeout = 0;


    var _getStore = function()
    {
        return new Ext.data.Store({
            storeId     : Ext.id(),
            autoLoad    : false,
            reader      : new com.conjoon.cudgets.data.JsonReader({
                              root: 'accounts',
                              id : 'id'
                          }, com.conjoon.groupware.feeds.AccountRecord),
            proxy   : new com.conjoon.cudgets.data.DirectProxy({
                api : {
                    read : com.conjoon.groupware.provider.feedsAccount.getFeedAccounts
                }
            }),

            url         : './groupware/feeds.account/get.feed.accounts/format/json',
            listeners   : com.conjoon.groupware.feeds.FeedRunner.getListener()
        });
    };

    /**
     * Listener for add/remove/update/load event of the store. Will compute the
     * timeout value from the configured accounts.
     * It is guaranteed that the method will only be called once the store has been
     * created.
     * The timeout will only be committed if changes have actually been commited
     * to the store.
     *
     *
     * @return {Number}
     */
    var _storeChanged = function(store, record, operation)
    {
        if (operation && (typeof(operation) == 'string') && operation != _commitId) {
            return;
        }

        var records = _store.getRange();
        var len     = records.length;

        var _oldTimeout = _timeout || _defaultTimeout;

        _timeout = 0;

        for (var i = 0; i < len; i++) {
            _timeout += (records[i].get('requestTimeout')*1000);
        }

        Ext.ux.util.MessageBus.publish('com.conjoon.groupware.feeds.AccountStore.update', {
            requestTimeout    : _timeout,
            oldRequestTimeout : _oldTimeout
        });
    };

    return {

        /**
         * Returns the timeout for the request accumulated over all available
         * feed accounts, in miliseconds.
         * If the store is not available yet, the function will return the
         * default value to guarantee that the timeout will never equal to 0.
         *
         * @return {Number}
         */
        getTimeoutSum : function()
        {
            if (_timeout == 0 || _store == null) {
                return _defaultTimeout;
            }

            return _timeout;
        },

        /**
         *
         * @return {Ext.data.Store}
         */
        getInstance : function()
        {
            if (_store === null) {
                _store = _getStore();
                _store.on('add',    _storeChanged, this);
                _store.on('remove', _storeChanged, this);
                _store.on('update', _storeChanged, this);
                _store.on('load',   _storeChanged, this);
            }

            return _store;
        }

    };

}();