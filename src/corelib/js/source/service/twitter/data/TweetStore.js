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
 * A store for querying Twitter status updates.
 *
 * @class com.conjoon.service.twitter.data.TweetStore
 * @extends Ext.data.Store
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
com.conjoon.service.twitter.data.TweetStore = function(c){

    com.conjoon.service.twitter.data.TweetStore.superclass.constructor.call(
        this, Ext.apply(c, {
            autoLoad : false,
            reader : new Ext.data.JsonReader({
                root : 'tweets',
                id   : 'id'
            }, com.conjoon.service.twitter.data.TweetRecord),
            remoteSort : false,
            sortInfo   : {
                field     : 'createdAt',
                direction : 'DESC'
            }
    }));
};
Ext.extend(com.conjoon.service.twitter.data.TweetStore, Ext.data.Store);