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

Ext.namespace('com.conjoon.cudgets.grid');

/**
 * This class represents a panel which lists file as selected by one ore more
 * FileChooserButton(s).
 * The panel's purpose is to present a selection of files wherever a user needs
 * to specify files which are either already present on the server or not.
 * The panel itself is not capable of doing uploads. The logic has to be
 * implemented along with the FileChooserButton that controls selecting files.
 * A FilePanel can either represent files which are not yet uploaded to the
 * server, or files which are already present on the server.
 * The component allows for in-line editing of file names and does allow
 * for showing states regarding file-upload/download.
 *
 *
 * The component represents data of the type
 * com.conjoon.cudgets.data.FileRecord.
 *
 * @author <Thorsten Suckow-Homberg> tsuckow@conjoon.org
 *
 * @class com.conjoon.cudgets.grid.FilePanel
 * @extends Ext.grid.EditorGridPanel
 */
com.conjoon.cudgets.grid.FilePanel = Ext.extend(Ext.grid.EditorGridPanel, {

    /**
     * @cfg {Object} api The Ext.Direct-Api configuration for this panel's
     * store
     */
    api : null,

    /**
     * @cfg {com.conjoon.cudgets.grid.ui.DefaultFilePanelUi} ui
     * The ui for this panel. If not provided, defaults to
     * {com.conjoon.cudgets.grid.ui.DefaultFilePanelUi}
     */
    ui : null,

    /**
     * @cfg {Ext.grid.ColumnModel} colModel
     * The colModel for this panel. If not provided, defaults to
     * {com.conjoon.cudgets.grid.data.DefaultFilePanelColumnModel}
     */
    colModel : null,

    /**
     * @type {com.conjoon.cudgets.grid.FilepanelContextMenu} contextMenu
     */
    contextMenu : null,


// -------- Ext.grid.EditorGridPanel
    /**
     * Inits this component.
     *
     */
    initComponent : function()
    {
        this.addEvents(
            /**
             * @event uploadcancel
             * Fired when this panel's store record is cancelled during uploading.
             * @param {com.conjoon.cudgets.grid.FilePanel} filePanel
             * @param {Array} records
             */
            'uploadcancel',
            /**
             * @event downloadcancel
             * Fired when this panel's store record is cancelled during downloading.
             * @param {com.conjoon.cudgets.grid.FilePanel} filePanel
             * @param {Array} records
             */
            'downloadcancel',
            /**
             * @event recordremove
             * Fired when this panel's store record is removed
             * @param {com.conjoon.cudgets.grid.FilePanel} filePanel
             * @param {Array} records
             */
            'recordremove',
            /**
             * @event downloadrequest
             * Fired when a file represented by the record is needed to be
             * downloaded
             * @param {Ext.grid.GridPanel} this
             * @param {Ext.data.Record} record
             */
            'downloadrequest'
        );

        if (!this.ui) {
            this.ui = new com.conjoon.cudgets.grid.ui.DefaultFilePanelUi();
        }

        if (!this.colModel) {
            this.colModel = new com.conjoon.cudgets.grid.data.DefaultFilePanelColumnModel();
        }

        this.ui.init(this);

        this.store = new com.conjoon.cudgets.data.Store({
            autoDestroy          : true,
            pruneModifiedRecords : true,
            storeId              : Ext.id(),
            autoSave             : false,
            autoLoad             : false,
            proxy                : new com.conjoon.cudgets.data.DirectProxy({
                api : this.api
            }),
            writer : new Ext.data.JsonWriter({
                encode  : false,
                listful : false
            }),
            reader : new com.conjoon.cudgets.data.JsonReader({
                 id              : 'id',
                 root            : 'files',
                 successProperty : 'success'
            }, com.conjoon.cudgets.data.FileRecord)
        });

        com.conjoon.cudgets.grid.FilePanel.superclass.initComponent.call(this);
    },

// -------- API

    /**
     * Removes all the specified records of which their state is either invalid
     * or/and not downloading and uploading.
     *
     */
    removeRecords : function(records)
    {
        var state, location, rec;

        for (var i = 0, len = records.length; i < len; i++) {
            rec   = records[i];
            state = rec.get('state');
        }
    },

    /**
     * Adds the specified record to this grid.
     *
     * @param {com.conjoon.cudgets.data.FileRecord) fileRecord
     */
    addFile : function(fileRecord)
    {
        this.getStore().add(fileRecord);
    },

    /**
     * Returns the context menu for this grid.
     *
     * @return {com.conjoon.cudgets.grid.FielPanelContextMenu}
     */
    getContextMenu : function()
    {
        if (!this.contextMenu) {
            this.contextMenu = this.ui.buildContextMenu();
        }

        return this.contextMenu;
    },

    /**
     * Shows the context menu for this filePanel at the specified position
     * extracted from the EventObject and for the rowIndex.
     *
     * @param {com.conjoon.cudgets.data.FileRecord} record
     * @param {Array} xy
     */
    showContextMenuForRecordAt : function(record, xy)
    {
        var state      = record.get('state');
        var location   = record.get('location');
        var FileRecord = com.conjoon.cudgets.data.FileRecord;
        var cm         = this.getContextMenu();

        if (state == FileRecord.STATE_INVALID) {
            cm.getCancelItem().setDisabled(true);
            cm.getRemoveItem().setDisabled(false);
            cm.getDownloadItem().setDisabled(true);
            cm.getRenameItem().setDisabled(true);
            cm.showAt(xy);
            return;
        } else if (state == FileRecord.STATE_UPLOADING
                   || state == FileRecord.STATE_DOWNLOADING) {
            cm.getCancelItem().setDisabled(false);
            cm.getRemoveItem().setDisabled(true);
            cm.getDownloadItem().setDisabled(true);
            cm.getRenameItem().setDisabled(true);
            cm.showAt(xy);
            return;
        } else if (location == FileRecord.LOCATION_REMOTE) {
            cm.getCancelItem().setDisabled(true);
            cm.getRemoveItem().setDisabled(false);
            cm.getDownloadItem().setDisabled(false);
            cm.getRenameItem().setDisabled(false);
            cm.showAt(xy);
            return;
        } else {
            throw("Invalid state or location for fileRecord");
        }


    }

});