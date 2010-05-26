/**
 * conjoon
 * (c) 2002-2010 siteartwork.de/conjoon.org
 * licensing@conjoon.org
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
 * @author <Thorsten Suckow-Homberg> ts@siteartwork.de
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