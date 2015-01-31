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

Ext.namespace('com.conjoon.cudgets.grid.plugin.listener');

/**
 * The listener class for the com.conjoon.cudgets.grid.plugin namespace.
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 *
 * @class com.conjoon.cudgets.grid.plugin.listener.Listener
 *
 * @constructor
 */
com.conjoon.cudgets.grid.plugin.listener.Listener = function(config) {

    config = config || {};

    Ext.apply(this, config);

};

com.conjoon.cudgets.grid.plugin.listener.Listener.prototype = {

    /**
     * The plugin this listener-class is bound to.
     * @type {com.conjoon.cudgets.grid.plugin.Plugin} plugin
     * @protected
     */
    plugin : null,

// -------- api
    /**
     * Installs the listeners for the grid.
     *
     * @param {com.conjoon.cudgets.grid.plugin.PreviewWindow} plugin The plugin
     * this listener-class is bound to.
     *
     * @packageprotected
     */
    init : function(plugin)
    {
        if (this.plugin) {
            throw(
                "You may not call \"init()\" of a Listener-class "
                +"more than once."
            )
        }

        this.plugin  = plugin;
    }

};