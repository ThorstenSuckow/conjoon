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
 * This class models a Twitter-account.
 *
 * @class com.conjoon.service.twitter.data.AccountRecord
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
com.conjoon.service.twitter.data.AccountRecord = Ext.data.Record.create([

    /**
     * @type {Number} id The id of the account as stored in the database.
     */
    {name : 'id',                     type : 'int'},

    /**
     * @type {Number} userId The id of the conjoon user this account belongs to.
     */
    {name : 'userId',                 type : 'int'},

    /**
     * @type {String} name The account name. Should equal to the account name as
     * managed by Twitter.
     */
    {name : 'name',                   type : 'string'},

    /**
     * @type {String} password The password for the account. When retrieved from the
     * server, the value will be masked.
     */
    {name : 'password',               type : 'string'},

    /**
     * @type {Number} updateInterval The interval in which the server queries for new
     * updates, in miliseconds.
     */
    {name : 'updateInterval',         type : 'int'},

    /**
     * @type {String} twitterId The id of the account as managed by Twitter. May be null.
     */
    {name : 'twitterId',              type : 'string'},

    /**
     * @type {String} twitterName The name of the user for this account, as managed
     * by Twitter. May be null.
     */
    {name : 'twitterName',            type : 'string'},

    /**
     * @type {String} twitterScreenName The screen-name of the user for this account
     * as managed by Twitter. Should equal to "name", but may be null.
     */
    {name : 'twitterScreenName',      type : 'string'},

    /**
     * @type {String} twitterLocation The location of the account's user as managed by
     * Twitter. May be null.
     */
    {name : 'twitterLocation',        type : 'string'},

    /**
     * @type {String} twitterProfileImageUrl The url to the profile image of the account's
     * user as managed by Twitter. May be null.
     */
    {name : 'twitterProfileImageUrl', type : 'string'},

    /**
     * @type {String} twitterUrl The url of the account's user, as managed by Twitter.
     * May be null.
     */
    {name : 'twitterUrl',             type : 'string'},

    /**
     * @type {Boolean} twitterProtected Whether the account is protected, as managed by Twitter.
     * May be null.
     */
    {name : 'twitterProtected',       type : 'bool'},

    /**
     * @type {String} twitterDescription The description of the account's user as managed
     * by Twitter. May be null.
     */
    {name : 'twitterDescription',     type : 'string'},

    /**
     * @type {Number} twitterFollowersCount The number of followers for this account, as
     * managed by Twitter. May be null.
     */
    {name : 'twitterFollowersCount',  type : 'int'}

]);