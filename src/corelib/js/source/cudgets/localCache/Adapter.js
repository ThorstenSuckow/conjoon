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

Ext.namespace('com.conjoon.cudgets.localCache');

/**
 * An abstract class to provide concrete Application Cache implementations for use with
 * com.conjoon.cudgets.localCache.Api
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 *
 * @class com.conjoon.cudgets.localCache.Adapter
 * @extends Ext.util.Observable
 */

com.conjoon.cudgets.localCache.Adapter = function() {

    this.addEvents(
        /**
         * @event beforeclear
         * Gets fired before an attempt is made to clear the local cache.
         * @param {com.conjoon.cudgets.localCache.Adapter}
         */
        'beforeclear',
        /**
         * @event clearsuccess
         * gets fired when clearing the cache was successfull.
         * @param {com.conjoon.cudgets.localCache.Adapter}
         */
        'clearsuccess',
        /**
         * @event clearfailure
         * Gets fired when clearing the cache was not successfull.
         * @param {com.conjoon.cudgets.localCache.Adapter}
         */
        'clearfailure',
        /**
         * @event beforebuild
         * Gets fired before an attempt is made to build the local cache.
         * Listeners should return false to cancel invoking a build.
         * @param {com.conjoon.cudgets.localCache.Adapter}
         */
        'beforebuild',
        /**
         * @event build
         * Gets fired after the beforebuild event if no buildcancel was
         * triggered and tells the observers that the request for building is
         * now happening.
         * @param {com.conjoon.cudgets.localCache.Adapter}
         */
        'build',
        /**
         * @event buildcancel
         * Gets fired when any listener for the beforebuild-event returns false.
         * @param {com.conjoon.cudgets.localCache.Adapter}
         */
        'buildcancel',
        /**
         * @event buildsuccess
         * gets fired when building the cache was successfull.
         * @param {com.conjoon.cudgets.localCache.Adapter}
         */
        'buildsuccess',
        /**
         * @event buildfailure
         * Gets fired when building the cache was not successfull.
         * @param {com.conjoon.cudgets.localCache.Adapter}
         */
        'buildfailure'
    );

    com.conjoon.cudgets.localCache.Adapter.superclass.constructor.call(this);
};

Ext.extend(com.conjoon.cudgets.localCache.Adapter, Ext.util.Observable, {

    /**
     * Returns true if the cache the concrete implementation of this class is
     * available, otherwise false
     *
     * @return {Boolean}
     *
     * @abstract
     */
    isCacheAvailable : Ext.emptyFn,

    /**
     * Returns the textual representation for the local cache the concrete
     * implementation of this class represents.
     *
     * @return {String}
     *
     * @abstract
     */
    getCacheType : Ext.emptyFn,

    /**
     * Clears the local cache. Concrete implementations of this class are
     * advised to properly fire the "beforeclear" and "clearsuccess" or
     * "clearfailure" event.
     *
     * @abstract
     */
    clearCache : Ext.emptyFn,

    /**
     * Builds the local cache. Concrete implementations of this class are
     * advised to properly fire the "beforebuild" and "buildsuccess" or
     * "buildfailure" event.
     *
     * @abstract
     */
    buildCache : Ext.emptyFn,

    /**
     * Returns the current state of the cache. Possible values are
     *
     *  UNCACHED
     *  IDLE
     *  CHECKING
     *  DOWNLOADING
     *  UPDATEREADY
     *  OBSOLETE
     *
     */
    getStatus : Ext.EmptyFn
});

/**
 * A set of constants representing possibel states of this Adapter.
 *
 */
com.conjoon.cudgets.localCache.Adapter.status = {
    UNCACHED    : 0,
    IDLE        : 1,
    CHECKING    : 2,
    DOWNLOADING : 3,
    UPDATEREADY : 4,
    OBSOLETE    : 5
};