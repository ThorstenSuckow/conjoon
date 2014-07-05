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