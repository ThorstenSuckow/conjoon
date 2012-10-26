/**
 * conjoon
 * (c) 2002-2012 siteartwork.de/conjoon.org
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

Ext.namespace('com.conjoon.cudgets.grid.plugin.dataPreview.listener');

/**
 * An  base class that provides the interface for listeners for the grid the
 * {com.conjoon.cudgets.grid.plugin.PreviewWindow}-plugin is bound to.
 * This listener provides no action for the rowdeselect event. Instead, the
 * cellclick listener will determine if shift or ctrl key was pressed during
 * clicking a row and hide any preview window which was opened for the clicked
 * row.
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 *
 * @class com.conjoon.cudgets.grid.plugin.previewWindow.listener.DefaultGridListener
 *
 * @constructor
 */
com.conjoon.cudgets.grid.plugin.dataPreview.listener.DefaultGridListener =
    Ext.extend(com.conjoon.cudgets.grid.plugin.listener.Listener, {

    /**
     * A flag indicating if there are currently multiple selections available
     * in the grid's selection model.
     * @type {Boolean}
     * @protected
     */
    multiSelections : false,

    /**
     * A flag indicating whether there is a click active which helps in
     * distinguishing between showing the preview panel when a double click or a
     * single click/select occurs .
     * @type {Boolean}
     */
    cellClickActive : false,

    /**
     * A lfag indicating that the context menu was requested, thus no preview
     * window should be shown when row gets selected.
     * @type {Boolean}
     */
    isContextMenu : false,

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
        com.conjoon.cudgets.grid.plugin.dataPreview.listener
            .DefaultGridListener.superclass.init.call(this, plugin);

        var grid     = plugin.getGrid();
        var selModel = grid.getSelectionModel();

        grid.mon(
            selModel, 'rowselect', this.onRowSelect, this, {buffer : 200}
        );
        grid.mon(
            selModel, 'beforerowselect', this.onBeforeRowSelect, this,
            {buffer : 200}
        );
        grid.mon(
            grid, 'cellclick', this.onCellClick, this, {buffer : 200}
        );

        grid.mon(grid, 'celldblclick', this.onCellDblClick, this);

        grid.mon(grid, 'resize',
            plugin.hidePreview.createDelegate(plugin, [true]));
        grid.mon(grid, 'beforecollapse',
            plugin.hidePreview.createDelegate(plugin, [true]));
        grid.mon(grid, 'contextmenu', this.onContextMenu, this);

    },

// -------- listeners

    /**
     * Listener for the contextmenu event. Will set the isContextMenu flag to
     * true.
     */
    onContextMenu : function()
    {
        this.plugin.hidePreview(false);
        this.isContextMenu = true;
    },


    /**
     * Checks whether multiple selections are available in the grid's
     * selection model, and will tell the plugin to hide the preview window
     * when a selection is about to be made
     *
     * @param {Ext.grid.RowSelectionModel} selModel
     * @param {Number} rowIndex
     * @param {Boolean} keepExisting
     * @param {Ext.data.Record} record
     *
     * @return void
     */
    onBeforeRowSelect : function(selModel, rowIndex, keepExisting, record)
    {
        if (keepExisting)  {
            this.multiSelections = true;
            this.plugin.hidePreview(false);
            return;
        }

        this.multiSelections = false;
    },

    /**
     * Listener for the grid selection model's rowselect event. Will void if
     * either one of the flags multiSelections or cellClickActive equals to
     * true, otherwise advise the plugin to show the dataPreview for the
     * selected record.
     *
     * @param {Ext.grid.RowSelectionModel} selModel
     * @param {Number} rowIndex
     * @param {Ext.data.Record} record
     */
    onRowSelect : function(selModel, rowIndex, record)
    {
        if (this.multiSelections || this.isContextMenu) {
            this.isContextMenu = false;
            return;
        }

        if (this.cellClickActive) {
            this.cellClickActive = false;
            return;
        }

        this.plugin.showPreview(record);
    },

    /**
     *  Listener for a grid cell's  click event. Will void if the current cell
     *  is not selected or if the number of selected rows is greater than one.
     *  This listener's purpose is mainly to re-show the preview window if
     *  the row which is clicked on is already selected, but it's preview window
     *  is already hidden. This will also take care of hiding a preview window
     *  when a pressed ctrl or shift key was detected.
     *
     * @param {Ext.grid.GridPanel} grid
     * @param {Number} rowIndex
     * @param {Number} columnIndex
     * @param {EventObject} e
     */
    onCellClick : function(grid, rowIndex, columnIndex, e)
    {
        var selModel = grid.getSelectionModel();
        var record   = selModel.getSelected();
        var key      = (e.shiftKey || e.ctrlKey);

        if (this.multiSelections
            || !selModel.isSelected(rowIndex)
            || selModel.getCount() > 1
            || this.plugin.isPreviewShownForRecord(record)
            ) {
            // ignore showPreview if the eventObject tells us that
            // shift or ctrl was pressed
            if (key) {
                this.plugin.hidePreview(false);
            }

            return;
        }

        if (this.cellClickActive) {
            this.cellClickActive = false;
            return;
        }

        this.plugin.showPreview(record);
    },

    /**
     *  Listener for a grid cell's  coubleclick event.
     *  Will set cellClickActive to true and hide the preview window.
     *  Implementing classes are advised to add an action which provides a more
     *  detailed view on the data the double clicked row represents.
     *
     * @param {Ext.grid.GridPanel} grid
     * @param {Number} rowIndex
     * @param {Number} columnIndex
     * @param {EventObject} e
     */
    onCellDblClick : function(grid, rowIndex, columnIndex, eventObject)
    {
        this.cellClickActive = true;
        this.plugin.hidePreview(true);
        alert("impl oubleclick!");
        //com.conjoon.groupware.feeds.FeedViewBaton.showFeed(feedItem, true);
    }

});