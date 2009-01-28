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
 * @extends Ext.data.Store
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
com.conjoon.service.twitter.data.AccountStore = function(c){

    com.conjoon.service.twitter.data.AccountStore.superclass.constructor.call(
        this, Ext.apply(c || {}, {
            autoLoad : false,
            url      : '/service/twitter/get.accounts/format/json',
            reader : new Ext.data.JsonReader({
                root : 'accounts',
                id   : 'id'
            }, com.conjoon.service.twitter.data.AccountRecord),
    }));

};
Ext.extend(com.conjoon.service.twitter.data.AccountStore, Ext.data.Store);