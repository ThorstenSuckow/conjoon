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

Ext.namespace('com.conjoon.cudgets.grid.plugin.ui');

/**
 * Base class for ui implementations in com.conjoon.cudgets.grid.plugin.
 *
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 *
 * @class com.conjoon.cudgets.grid.plugin.ui.Ui
 */
com.conjoon.cudgets.grid.plugin.ui.Ui = function(config) {

    config = config || {};

    Ext.apply(this, config);
};

com.conjoon.cudgets.grid.plugin.ui.Ui.prototype = {

    /**
     * @cfg {com.conjoon.cudgets.grid.plugin.listener.Listener} actionListener
     * The actionListener for the ui mmanaged by this class.
     */
    actionListener : null,

    /**
     * @type {com.conjoon.cudgets.grid.plugin.Plugin} plugin The plugin this
     * ui is bound to.
     */
    plugin : null,

    /**
     * Inits the layout of the dialog.
     * gets called from the initComponent's "initComponent()" method.
     *
     * @param {Ext.Dialog} dialog The dialog this ui will manage.
     */
    init : function(plugin)
    {
        if (this.plugin) {
            throw("You may not call \"init()\" of a Ui-class more than once.")
        }

        this.plugin = plugin;

        this.paintUi();
        this.installListeners();
    },

    /**
     * Installs the listeners for this ui.
     * @protected
     */
    installListeners : function()
    {
        this.actionListener.init(this.plugin);
    },

    /**
     * Override with necessary components needed to show up when ui gets
     * initialized.
     * @protected
     */
    paintUi : Ext.emptyFn


};