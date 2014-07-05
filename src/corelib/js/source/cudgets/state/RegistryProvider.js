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

Ext.namespace('com.conjoon.cudgets.state');

/**
 * A state provider utilitzing the com.conjoon.cudgets.direct.Registry for
 * persisting the state.
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 *
 * @class com.conjoon.cudgets.state.RegistryProvider
 * @singleton
 */
com.conjoon.cudgets.state.RegistryProvider = Ext.extend(Ext.state.Provider, {

    /**
     * The registry used for this provider.
     * @param registry
     */
    registry : null,

    /**
     * The path to prepend each name.
     * @param path
     */
    path : null,

    /**
     *
     * @param registry concrete registry instance to use
     */
    constructor : function(config){
        com.conjoon.cudgets.state.RegistryProvider.superclass.constructor.call(this);

        if (!config || !config.registry || !config.path) {
            throw("no valid configuration object");
        }

        this.registry = config.registry;
        this.path     = config.path;

        delete this.state
    },

    /**
     * @inheritdoc
     */
    get : function(name, defaultValue){
        var v = this.registry.get(this.getPathForName(name));
        return v === null
               ? (defaultValue ? defaultValue : null) : Ext.util.JSON.decode(v);
    },

    /**
     * @inheritdoc
     */
    clear : function(name){
        this.registry.setValues({
            values : [{key : this.getPathForName(name), value : null}]
        });
        this.fireEvent("statechange", this, name, null);
    },

    /**
     * @inheritdoc
     */
    set : function(name, value){
        this.registry.setValues({
            values : [{key : this.getPathForName(name), value : Ext.util.JSON.encode(value)}],
            buffer : true
        });
        this.fireEvent("statechange", this, name, value);
    },

    /**
     * Returns the full path for the specified name
     *
     * @param {String} name
     *
     * @return {String}
     */
    getPathForName : function(name) {
        return (this.path + name).replace(/\/\//gi,'/');
    }

});
