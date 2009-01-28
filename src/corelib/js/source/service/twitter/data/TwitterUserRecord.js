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
 * This class models a Twitter user record. A TwitterUserRecord should not be
 * confused with a AccountRecord, as TwitterUserRecord only represents another
 * user/friend in the Twitterverse.
 *
 * @class com.conjoon.service.twitter.data.TwitterUserRecord
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
com.conjoon.service.twitter.data.TwitterUserRecord = Ext.data.Record.create([

    /**
     * @type {Number} id The id of the twitter user as managed by the Twitter service.
     */
    {name : 'id',            type : 'int'},

    /**
     * @type {String} name The name of the user as managed by the
     * Twitter service. Note: this property holds the real name of the user. The name
     * used to identify the user's account can be found in "screenName".
     */
    {name : 'name',            type : 'string'},

    /**
     * @type {String} screenName The name used to identify a Twitter account.
     */
    {name : 'screenName',      type : 'string'},

    /**
     * @type {String} location The location of the user as managed by
     * the twitter service.
     */
    {name : 'location',        type : 'string'},

    /**
     * @type {String} profileImageUrl The url to the user's image, as
     * managed by the twitter service.
     */
    {name : 'profileImageUrl', type : 'string'},

    /**
     * @type {String} url The url of the user as managed by the Twitter
     * service.
     */
    {name : 'url',             type : 'string'},

    /**
     * @type {Boolean} protected Whether status updates of this user are protected, as managed
     * by the Twitter service.
     */
    {name : 'protected',       type : 'bool'},

    /**
     * @type {String} description The description/bio of the user, as
     * managed by the twitter service.
     */
    {name : 'description',     type : 'string'},

    /**
     * @type {Number} followersCount The number of followers for the user
     * as managed by the Twitter service.
     */
    {name : 'followersCount',  type : 'int'}
]);