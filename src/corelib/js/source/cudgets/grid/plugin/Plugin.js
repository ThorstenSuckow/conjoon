/**
 * conjoon
 * (c) 2007-2015 conjoon.org
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

Ext.namespace('com.conjoon.cudgets.grid.plugin');

/**
 * The base class for all grid plugins.
 * A grid plugin has a listener that receives events from the grid and it's
 * components a plugin is bound to, and should call only methods from the
 * implementing plugin class upon receiving those events.
 *
 * @class com.conjoon.cudgets.grid.plugin.Plugin
 * @extends {Ext.util.Observable}
 *
 * @constructor
 * @param {Object} config The config object
 * @abstract
 */
com.conjoon.cudgets.grid.plugin.Plugin = function(config) {

    config = config || {};

    Ext.apply(this, config);

    com.conjoon.cudgets.grid.plugin.Plugin.superclass.constructor.call(this);
};

Ext.extend(com.conjoon.cudgets.grid.plugin.Plugin, Ext.util.Observable, {

    /**
     * @cfg {com.conjoon.cudgets.grid.plugin.listener.Listener} actionListener
     * The actionListener for the grid the plugin is bound to.
     * @protected
     */
    actionListener : null,

    /**
     * The {Ext.grid.GridPanel} this plugin is bound to.
     * @type {Ext.grid.GridPanel}
     * @protected
     */
    grid : null,

    /**
     * Inits this plugin.
     * Method is API-only. Will be called automatically from the grid this
     * plugin is bound to.
     *
     * @param {Ext.grid.GridPanel} grid
     */
    init : function(grid)
    {
        if (this.grid) {
            return;
        }

        this.grid = grid;

        this.actionListener.init(this);
    },

    /**
     * Returns the grid this plugin is bound to.
     *
     * @return {Ext.grid.GridPanel}
     */
    getGrid : function()
    {
        return this.grid;
    }



});