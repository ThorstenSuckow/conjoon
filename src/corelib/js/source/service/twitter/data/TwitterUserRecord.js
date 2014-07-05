/**
 * conjoon
 * (c) 2007-2014 conjoon.org
 * licensing@conjoon.org
 *
 * conjoon
 * Copyright (C) 2014 Thorsten Suckow-Homberg/conjoon.org
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
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
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
com.conjoon.service.twitter.data.TwitterUserRecord = Ext.data.Record.create([

    /**
     * @type {Number} id The id of the twitter user as managed by the Twitter service.
     */
    {name : 'id',            type : 'string'},

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
    {name : 'followersCount',  type : 'int'},

    /**
     * @type {Boolean} isFollowing Whether the currenty logged in user follows
     * this user or not.
     */
    {name : 'isFollowing',  type : 'bool'}
]);