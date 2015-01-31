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

Ext.namespace('com.conjoon.cudgets.grid.ui');

/**
 * Builds and layouts the FilePanel's ContextMenu layout and its components.
 *
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 *
 * @class com.conjoon.cudgets.grid.ui.DefaultFilePanelContextMenuUi
 */
com.conjoon.cudgets.grid.ui.DefaultFilePanelContextMenuUi = function(config) {

    config = config || {};

    Ext.apply(this, config);
};

com.conjoon.cudgets.grid.ui.DefaultFilePanelContextMenuUi.prototype = {

    /**
     * @cfg {String} cancelText
     */
    cancelText : 'cancel',

    /**
     * @cfg {String} removeText
     */
    removeText : 'remove',

    /**
     * @cfg {String} renameText
     */
    renameText : 'rename',

    /**
     * @cfg {String} downloadText
     */
    downloadText : 'download',

    /**
     * @cfg {com.conjoon.cudgets.grid.listener.DefaultFilePanelContextMenuListener}
     * actionListener
     * The actionListener for the menu this ui class manages. If not provided,
     * defaults to
     * {com.conjoon.cudgets.grid.listener.DefaultFilePanelContextMenuListener}
     */
    actionListener : null,

    /**
     * @type {Ext.menu.Menu} menu The menu this ui class manages. Gets assigned in the init()
     * method.
     */
    menu : null,

    /**
     * Inits the layout of the menu.
     * Gets called from the initComponent's "initComponent()" method.
     *
     * @param {Ext.menu.Menu} menu The menu this ui will manage.
     */
    init : function(menu)
    {
        if (this.menu) {
            return;
        }

        this.menu = menu;

        this.buildMenu();
        this.installListeners();
    },

    /**
     *
     * @protected
     */
    installListeners : function()
    {
        if (!this.actionListener) {
            this.actionListener = new com.conjoon.cudgets.grid.listener
                .DefaultFilePanelContextMenuListener();
        }

        this.actionListener.init(this.menu);
    },

    /**
     *
     * @protected
     */
    buildMenu : function()
    {
        Ext.apply(this.menu, {
            items : [
                this.menu.getDownloadItem(),
                this.menu.getRenameItem(),
                '-',
                this.menu.getCancelItem(),
                '-',
                this.menu.getRemoveItem()
            ]
        });
    },

    /**
     *
     * @return {Ext.menu.Item}
     *
     * @protected
     */
    buildCancelItem : function()
    {
        return new Ext.menu.Item({
            text : this.cancelText
        });
    },

    /**
     *
     * @return {Ext.menu.Item}
     *
     * @protected
     */
    buildRenameItem : function()
    {
        return new Ext.menu.Item({
            text : this.renameText
        });
    },

    /**
     *
     * @return {Ext.menu.Item}
     *
     * @protected
     */
    buildRemoveItem : function()
    {
        return new Ext.menu.Item({
            text : this.removeText
        });
    },

    /**
     *
     * @return {Ext.menu.Item}
     *
     * @protected
     */
    buildDownloadItem : function()
    {
        return new Ext.menu.Item({
            text : this.downloadText
        });
    }
};