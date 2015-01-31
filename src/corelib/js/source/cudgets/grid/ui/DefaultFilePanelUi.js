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
 * Builds and layouts the FilePanel's layout and its components.
 *
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 *
 * @class com.conjoon.cudgets.grid.ui.DefaultFilePanelUi
 */
com.conjoon.cudgets.grid.ui.DefaultFilePanelUi = function(config) {

    config = config || {};

    Ext.apply(this, config);
};

com.conjoon.cudgets.grid.ui.DefaultFilePanelUi.prototype = {


    /**
     * @cfg {com.conjoon.cudgets.grid.listener.DefaultFilePanelListener} actionListener
     * The actionListener for the panel this ui class manages. If not provided,
     * defaults to {com.conjoon.cudgets.grid.listener.DefaultFilePanelListener}
     */
    actionListener : null,

    /**
     * @type {Ext.grid.EditorGridPanel} panel The panel this ui class manages. Gets assigned in the init()
     * method.
     */
    panel : null,

    /**
     * Inits the layout of the panel.
     * gets called from the initComponent's "initComponent()" method.
     *
     * @param {Ext.grid.EditoGridPanel} panel The panel this ui will manage.
     */
    init : function(panel)
    {
        if (this.panel) {
            return;
        }

        this.panel = panel;

        this.buildPanel();
        this.installListeners();
    },

    /**
     *
     * @protected
     */
    installListeners : function()
    {
        if (!this.actionListener) {
            this.actionListener = new com.conjoon.cudgets.grid.listener.DefaultFilePanelListener();
        }

        this.actionListener.init(this.panel);
    },

    /**
     *
     * @protected
     */
    buildPanel : function()
    {
        Ext.apply(this.panel, {
            cls : 'com-conjoon-cudgets-grid-FilePanel',
            sm  : new Ext.grid.RowSelectionModel({
                singleSelect : true
            }),
            clicksToEdit : 0,
            /**
             * Overwrite so default impl does not start editing the row when
             * double clicked
             */
            onCellDblClick : function(g, row, col) {
                return;
                //this.startEditing(row, col);
            }
        });
    },

    /**
     *
     * @return {com.conjoon.cudgets.grid.FilePanelContextMenu}
     *
     * @protected
     */
    buildContextMenu : function()
    {
        return new com.conjoon.cudgets.grid.FilePanelContextMenu();
    }
};