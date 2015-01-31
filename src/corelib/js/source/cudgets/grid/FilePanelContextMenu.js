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

Ext.namespace('com.conjoon.cudgets.grid');

/**
 *
 * @author <Thorsten Suckow-Homberg> tsuckow@conjoon.org
 *
 * @class com.conjoon.cudgets.grid.FilePanelContextMenu
 * @extends Ext.menu.Menu
 */
com.conjoon.cudgets.grid.FilePanelContextMenu = Ext.extend(Ext.menu.Menu, {

    /**
     * @type {Ext.menu.MenuItem} downloadItem
     */
    downloadItem : null,

    /**
     * @type {Ext.menu.MenuItem} cancelItem
     */
    cancelItem : null,

    /**
     * @type {Ext.menu.MenuItem} renameItem
     */
    renameItem : null,

    /**
     * @type {Ext.menu.MenuItem} removeItem
     */
    removeItem : null,

// -------- Ext.menu.Menu

    initComponent: function()
    {

        if (!this.ui) {
            this.ui = new com.conjoon.cudgets.grid.ui.DefaultFilePanelContextMenuUi();
        }

        this.ui.init(this);

        com.conjoon.cudgets.grid.FilePanelContextMenu.superclass.initComponent.call(this);
    },

// -------- API

    /**
     *
     * @return {Ext.menu.Item}
     */
    getCancelItem : function()
    {
        if (!this.cancelItem) {
            this.cancelItem = this.ui.buildCancelItem();
        }

        return this.cancelItem;
    },

    /**
     *
     * @return {Ext.menu.Item}
     */
    getDownloadItem : function()
    {
        if (!this.downloadItem) {
            this.downloadItem = this.ui.buildDownloadItem();
        }

        return this.downloadItem;
    },

    /**
     *
     * @return {Ext.menu.Item}
     */
    getRenameItem : function()
    {
        if (!this.renameItem) {
            this.renameItem = this.ui.buildRenameItem();
        }

        return this.renameItem;
    },

    /**
     *
     * @return {Ext.menu.Item}
     */
    getRemoveItem : function()
    {
        if (!this.removeItem) {
            this.removeItem = this.ui.buildRemoveItem();
        }

        return this.removeItem;
    }


});