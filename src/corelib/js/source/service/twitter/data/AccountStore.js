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
 * A store for querying Twitter accounts as stored in the database backend of the
 * server.
 *
 * @class com.conjoon.service.twitter.data.AccountStore
 * @singleton
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
com.conjoon.service.twitter.data.AccountStore = function() {

    var _store = null;

    var _getStore = function() {
        return new Ext.data.Store({
            autoLoad : false,
            storeId  : Ext.id(),
            url      : './service/twitterAccount/get.accounts/format/json',
            reader : new Ext.data.JsonReader({
                root : 'accounts',
                id   : 'id'
            }, com.conjoon.service.twitter.data.AccountRecord)
        });
    };


    return {

        getInstance : function()
        {
            if (!_store) {
                _store = _getStore();
            }

            return _store;
        }

    }

}();