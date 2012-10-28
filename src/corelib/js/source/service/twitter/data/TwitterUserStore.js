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

Ext.namespace('com.conjoon.service.twitter.data');

/**
 * A store for querying twitter users (for example, friendslist of a user).
 *
 * @class com.conjoon.service.twitter.data.TwitterUserStore
 * @extends Ext.data.Store
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
com.conjoon.service.twitter.data.TwitterUserStore = function(c){

    com.conjoon.service.twitter.data.TwitterUserStore.superclass.constructor.call(
        this, Ext.apply(c, {
            autoLoad : false,
            reader : new Ext.data.JsonReader({
                root : 'users',
                id   : 'id'
            }, com.conjoon.service.twitter.data.TwitterUserRecord)
    }));

    this.on('beforeload', this._onBeforeLoad, this);

};
Ext.extend(com.conjoon.service.twitter.data.TwitterUserStore, Ext.data.Store, {

    /**
     * Listener for the beforeload event - cancels any ongoing server request.
     *
     * @param {Ext.data.Store} store
     * @param {Object} options
     */
    _onBeforeLoad : function()
    {
        var proxy = this.proxy;
        if (proxy.activeRequest[Ext.data.Api.actions.read]) {
            proxy.getConnection().abort(proxy.activeRequest[Ext.data.Api.actions.read]);
        }
    }

});