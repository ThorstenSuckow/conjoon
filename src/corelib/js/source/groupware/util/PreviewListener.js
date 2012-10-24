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

Ext.namespace('com.conjoon.groupware.util');

/**
 * An  base class that provides the interface for listeners for the preview
 * window.
 * This listener provides no action for the rowdeselect event. Instead, the
 * cellclick listener will determine if shift or ctrl key was pressed during
 * clicking a row and hide any preview window which was opened for the clicked
 * row. Additional flags will be set since delaying listeners make it necessary
 * to determine later on which which event was reacted first to.
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 *
 * @class com.conjoon.groupware.feeds.FeedGridPreviewListener
 *
 * @constructor
 */
com.conjoon.groupware.util.PreviewListener = function(){};
com.conjoon.groupware.util.PreviewListener.prototype = {

    /**
     * A flag indicating if there are currently multiple selections available
     * in the grid's selection model.
     * @type {Boolean}
     * @protected
     */
    multiSelections : false,

    /**
     * A flag indicating whether a double click was triggered.
     * @type {Boolean}
     */
    cellClickActive : false,

    /**
     * A flag indicating whether a row selection was triggered.
     * @type {Boolean}
     */
    rSel : false,

    /**
     * A flag indicating whether a single click was triggered.
     * @type {Boolean}
     */
    sClick : false,

    /**
     * A lfag indicating that the context menu was requested, thus no preview
     * window should be shown when row gets selected.
     * @type {Boolean}
     */
    isContextMenu : false,

    /**
     * @param {Object}
        */
    preview : null,

    /**
     * @param grid
     */
    grid : null,

// -------- api
    /**
     * Installs the listeners for the grid.
     *
     * @param {Ext.grid.GridPanel} grid The grid this listener-class is
     * bound to.
     *
     * @packageprotected
     */
    init : function(grid, preview)
    {
        this.grid = grid;

        var selModel = grid.getSelectionModel();

        grid.mon(
            selModel, 'rowselect', this.onRowSelect, this, {buffer : 250}
        );

        grid.mon(
            selModel, 'beforerowselect', this.onBeforeRowSelect, this
        );
        grid.mon(
            grid, 'cellclick', this.onCellClick, this
        );

        grid.mon(grid, 'celldblclick', this._onCellDblClick, this);

        this.preview = preview;

        grid.mon(grid, 'resize',
            this.preview.hide.createDelegate(this.preview, [true]));
        grid.mon(grid, 'beforecollapse',
            this.preview.hide.createDelegate(this.preview, [true]));
        grid.mon(grid, 'contextmenu', this.onContextMenu, this);

    },

// -------- listeners

    /**
     * Listener for the contextmenu event. Will set the isContextMenu flag to
     * true.
     */
    onContextMenu : function()
    {
        this.preview.hide(false);
        this.isContextMenu = true;
    },


    /**
     * Checks whether multiple selections are available in the grid's
     * selection model, and (un)set flags accordingly.
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
        if (keepExisting && selModel.getCount() >= 1)  {
            this.multiSelections = true;
            return;
        }

        this.multiSelections = false;
    },

    i : 0,
    /**
     * Listener for the grid selection model's rowselect event. Will void if
     * either one of the flags multiSelections or cellClickActive equals to
     * true, otherwise advise the plugin to show the dataPreview for the
     * selected record. hides the preview if multiple selections are detected.
     *
     * @param {Ext.grid.RowSelectionModel} selModel
     * @param {Number} rowIndex
     * @param {Ext.data.Record} record
     */
    onRowSelect : function(selModel, rowIndex, record)
    {
        this.rSel = true;

        if (this.sClick) {
            this.sClick = false;
            return;
        }

        if (this.multiSelections || this.isContextMenu) {
            this.isContextMenu = false;
            this.preview.hide(false);
            return;
        }

        if (this.cellClickActive) {
            this.cellClickActive = false;
            return;
        }

        this.preview.show(this.grid, record);
    },

    /**
     *  Listener for a grid cell's  click event. Will void if cellClickActive
     *  is set to true.
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
        if (this.rSel) {
            this.rSel = false;
            return;
        }

        if (this.cellClickActive) {
            this.cellClickActive = false;
            return;
        }

        this.sClick = true;

        var selModel = grid.getSelectionModel();
        var record   = selModel.getSelected();
        var key      = (e.ctrlKey || e.shiftKey);

        if (!record || (key && this.preview.isPreviewShownForRecord(record))) {
            // hide preview if deselect or no selections are available
            this.preview.hide(false);
        } else if (selModel.getCount() == 1
            && grid.getStore().indexOf(record) === rowIndex) {
            // show preview panel if click occurs on already selected row,
            // and this is the only selected row
            this.preview.show(grid, record);
        }


        return;
    },


    /**
     * Delegates to onCellDblClick and makes sure that any existing preview
     * window gets hidden.
     *
     */
    _onCellDblClick : function()
    {
        this.cellClickActive = true;
        this.preview.hide(true);

        this.onCellDblClick.apply(this, arguments);
    },

    /**
     *  Listener for a grid cell's  coubleclick event.
     *  Will set cellClickActive to true and hide the preview window.
     *  Implementing classes are advised to add an action which provides a more
     *  detailed view on the data the double clicked row represents.
     *
     * @abstract
     * @param {Ext.grid.GridPanel} grid
     * @param {Number} rowIndex
     * @param {Number} columnIndex
     * @param {EventObject} e
     */
    onCellDblClick : Ext.emptyFn
};