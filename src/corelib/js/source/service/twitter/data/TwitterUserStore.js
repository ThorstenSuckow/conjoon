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
 * A store for querying twitter users (for example, friendslist of a user).
 *
 * @class com.conjoon.service.twitter.data.TwitterUserStore
 * @extends Ext.data.Store
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
com.conjoon.service.twitter.data.TwitterUserStore = function(c){

    com.conjoon.service.twitter.data.TwitterUserStore.superclass.constructor.call(
        this, Ext.apply(c, {
            autoLoad : false,
            reader : new Ext.data.JsonReader({
                root : 'users',
                id   : 'id'
            }, com.conjoon.service.twitter.data.TwitterUserRecord),
    }));

};
Ext.extend(com.conjoon.service.twitter.data.TwitterUserStore, Ext.data.Store);