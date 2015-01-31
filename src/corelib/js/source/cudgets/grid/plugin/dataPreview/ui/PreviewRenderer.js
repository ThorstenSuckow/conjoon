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

Ext.namespace('com.conjoon.cudgets.grid.plugin.dataPreview.ui');

/**
 * Base class for DataPreview plugins.
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 *
 * @extends com.conjoon.cudgets.grid.plugin.ui.Ui
 *
 * @abstract
 *
 * @class com.conjoon.cudgets.grid.plugin.dataPreview.ui.PreviewRenderer
 */
com.conjoon.cudgets.grid.plugin.dataPreview.ui.PreviewRenderer = Ext.extend(
    com.conjoon.cudgets.grid.plugin.ui.Ui, {

    /**
     * The record from which a request to preview its data was initiated.
     */
    record : null,

    /**
     * The id of the data which is currently being previewed or in the progress
     * of being viewed, if any.
     * @type {Mixed}
     */
    activePreviewId : null,

    /**
     * The element where the preview gets rendered into.
     * @type {Ext.Component} previewComponent
     */
    previewComponent : null,


// -------- protected api

    /**
     * Returns true if the panel is currently busy rendering the data.
     * This could be for example if data has to be queried from the backend
     * which has not finished yet.
     * @return {Boolean}
     */
    isRenderingProcessBusy : Ext.emptyFn,

    /**
     * Aborts any process related with the preview, for example an AJAX request
     * querying data from a server.
     *
     */
    abortRenderingProcess : Ext.emptyFn,

    /**
     * Repaints the preview with new data. Implementing classes can decide
     * whether elements can be reused for painting the preview. Otherwise, a
     * call to paintPreview should be made.
     *
     */
    repaintPreview : Ext.emptyFn,

    /**
     * Paints the preview element which can be later reused by calling
     * repaintPreview.
     * @return {Ext.Component} The element responsible for showing the data.
     */
     paintPreview : Ext.emptyFn,

    /**
     * Hides the preview.
     * @param {Boolean} disappear true to hide at once without allowing
     * animation effects or similiar.
     */
    hidePreview : Ext.emptyFn,

    /**
     * Called when showPreviewForId is called, right before the methods for
     * painting the preview are invoked.
     *
     * @param {Ext.data.Record} record The record that is about to be previewed.
     */
    harvestRecordInformation : Ext.emptyFn,

// -------- public api

    /**
     * Aborts any rendering process if busy.
     *
     */
    abortRenderingProcessIfBusy : function()
    {
        if (this.isRenderingProcessBusy()) {
            this.abortRenderingProcess();
        }
    },

    /**
     * returns the id of the data currently represented by a preview, if any.
     * @return mixed
     */
    getActivePreviewId : function()
    {
        return this.activePreviewId;
    },

    /**
     * Shows the preview for the selected record. The preview will not get
     * refreshed if data with the requested id is already shown in the preview.
     *
     * @param {Mixed} id
     */
    showPreviewForId : function(id)
    {
        var grid = this.plugin.getGrid();

        // get the record information of the current selected cell
        var t = grid.getStore().getById(id);
        if (!t) {
            return;
        }

        var pId = t.id;
        if (this.activePreviewId == pId) {
            // previewing is already active for this record.
            return;
        }

        this.harvestRecordInformation(t);

        this.record = t.copy();

        // abort all pending operations
        if (this.previewComponent != null) {
            this.repaintPreview();
        } else {
            this.previewComponent = this.paintPreview();
        }

        this.activePreviewId = pId;
    },

    /**
     * Hides the preview, i.e. it's contents, not the container holding the
     * preview.
     * Calling objects might hint to how hiding should happen, whereas
     * disappear = true basically indicates that the preview should be hidden at
     * once, without using any effect or confirm dialogs..
     *
     * @param {boolean} <tt>true</tt> to close immediately
     */
    clearPreview : function(disappear)
    {
        if (this.previewComponent == null || this.activePreviewId == null) {
            return;
        }

        this.hidePreview(disappear);

        this.activePreviewId = null;
    }

});
