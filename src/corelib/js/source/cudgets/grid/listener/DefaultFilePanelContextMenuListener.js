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

Ext.namespace('com.conjoon.cudgets.grid.listener');

/**
 * An  base class that provides the interface for listeners for
 * {com.conjoon.cudgets.grid.FilePanelContextMenu}
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 *
 * @class com.conjoon.cudgets.grid.listener.DefaultFilePanelContextMenuListener
 *
 * @constructor
 */
com.conjoon.cudgets.grid.listener.DefaultFilePanelContextMenuListener = function() {

};

com.conjoon.cudgets.grid.listener.DefaultFilePanelContextMenuListener.prototype = {

    /**
     * @type {com.conjoon.cudgets.grid.FilePanelContextMenu} menu The menu
     * this listener is bound to.
     */
    menu : null,

    /**
     * @type {String} clsId
     */
    clsId : '9b2b9b78-e30d-4635-9087-851b37c573e3',


// -------- api

    /**
     * Installs the listeners for the elements found in the menu.
     *
     * @param {com.conjoon.cudgets.grid.FilePanelContextMenu} menu The menu
     * this listener is bound to.
     *
     * @packageprotected
     */
    init : function(menu)
    {
        if (this.menu) {
            return;
        }

        this.menu = menu;


    }

// -------- helper

// ------- listeners


};