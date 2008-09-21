/*
 * Ext.ux.BufferedGridToolbar V1.0
 * Copyright(c) 2007, http://www.siteartwork.de
 *
 * Licensed under the terms of the Open Source LGPL 3.0
 * http://www.gnu.org/licenses/lgpl.html
 *
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */

/**
 * @class Ext.ux.BufferedGridToolbar
 * @extends Ext.Toolbar
 * A specialized toolbar that is bound to a {@link Ext.ux.grid.BufferedGridView}
 * and provides information about the indexes of the requested data and the buffer
 * state.
 * @constructor
 * @param {Object} config
 */
Ext.namespace('Ext.ux');

Ext.ux.BufferedGridToolbar = Ext.extend(Ext.Toolbar, {
    /**
     * @cfg {Boolean} displayInfo
     * True to display the displayMsg (defaults to false)
     */

    /**
     * @cfg {String} displayMsg
     * The paging status message to display (defaults to "Displaying {start} - {end} of {total}")
     */
    displayMsg : 'Displaying {0} - {1} of {2}',

    /**
     * @cfg {String} emptyMsg
     * The message to display when no records are found (defaults to "No data to display")
     */
    emptyMsg : 'No data to display',

    /**
     * Value to display as the tooltip text for the refresh button. Defaults to
     * "Refresh"
     * @param {String}
     */
    refreshText : "Refresh",

    initComponent : function()
    {
        Ext.ux.BufferedGridToolbar.superclass.initComponent.call(this);
        this.bind(this.grid);
    },

    // private
    updateInfo : function(rowIndex, visibleRows, totalCount)
    {
        if(this.displayEl){
            var msg = totalCount == 0 ?
                this.emptyMsg :
                String.format(this.displayMsg, rowIndex+1,
                              rowIndex+visibleRows, totalCount);
            this.displayEl.update(msg);
        }
    },

    /**
     * Unbinds the toolbar from the specified {@link Ext.ux.grid.Livegrid}
     * @param {@link Ext.ux.grid.Livegrid} view The view to unbind
     */
    unbind : function(grid)
    {
        var st = grid.getStore();
        var vw = grid.view;

        st.un('loadexception', this.enableLoading,  this);
        st.un('beforeload',    this.disableLoading, this);
        st.un('load',          this.enableLoading,  this);
        vw.un('rowremoved',    this.onRowRemoved,   this);
        vw.un('rowsinserted',  this.onRowsInserted, this);
        vw.un('beforebuffer',  this.beforeBuffer,   this);
        vw.un('cursormove',    this.onCursorMove,   this);
        vw.un('buffer',        this.onBuffer,       this);
        vw.un('bufferfailure', this.enableLoading,  this);
        this.grid = undefined;
    },

    /**
     * Binds the toolbar to the specified {@link Ext.ux.grid.Livegrid}
     * @param {Ext.ux.grid.Livegrid} view The view to bind
     */
    bind : function(grid)
    {
        var st = grid.getStore();
        var vw = grid.view;

        st.on('loadexception', this.enableLoading,  this);
        st.on('beforeload',    this.disableLoading, this);
        st.on('load',          this.enableLoading,  this);
        vw.on('rowremoved',    this.onRowRemoved,   this);
        vw.on('rowsinserted',  this.onRowsInserted, this);
        vw.on('beforebuffer',  this.beforeBuffer,   this);
        vw.on('cursormove',    this.onCursorMove,   this);
        vw.on('buffer',        this.onBuffer,       this);
        vw.on('bufferfailure', this.enableLoading,  this);
        this.grid = grid;
    },

// ----------------------------------- Listeners -------------------------------
    enableLoading : function()
    {
        this.loading.setDisabled(false);
    },

    disableLoading : function()
    {
        this.loading.setDisabled(true);
    },

    onCursorMove : function(view, rowIndex, visibleRows, totalCount)
    {
        this.updateInfo(rowIndex, visibleRows, totalCount);
    },

    // private
    onRowsInserted : function(view, start, end)
    {
        this.updateInfo(view.rowIndex, Math.min(view.ds.totalLength, view.visibleRows-view.rowClipped),
                        view.ds.totalLength);
    },

    // private
    onRowRemoved : function(view, index, record)
    {
        this.updateInfo(view.rowIndex, Math.min(view.ds.totalLength, view.visibleRows-view.rowClipped),
                        view.ds.totalLength);
    },

    // private
    beforeBuffer : function(view, store, rowIndex, visibleRows, totalCount)
    {
        this.loading.disable();
        this.updateInfo(rowIndex, visibleRows, totalCount);
    },

    // private
    onBuffer : function(view, store, rowIndex, visibleRows, totalCount)
    {
        this.loading.enable();
        this.updateInfo(rowIndex, visibleRows, totalCount);
    },

    // private
    onClick : function(type)
    {
        switch (type) {
            case 'refresh':
                if (this.grid.view.reset(true)) {
                    this.loading.disable();
                } else {
                    this.loading.enable();
                }
            break;

        }
    },

    // private
    onRender : function(ct, position)
    {
        Ext.PagingToolbar.superclass.onRender.call(this, ct, position);

        this.loading = this.addButton({
            tooltip : this.refreshText,
            iconCls : "x-tbar-loading",
            handler : this.onClick.createDelegate(this, ["refresh"])
        });

        this.addSeparator();

        if(this.displayInfo){
            this.displayEl = Ext.fly(this.el.dom).createChild({cls:'x-paging-info'});
        }
    }
});