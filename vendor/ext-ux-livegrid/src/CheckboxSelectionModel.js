/**
 * Ext.ux.grid.livegrid.CheckboxSelectionModel
 * Copyright (c) 2007-2013, http://www.siteartwork.de
 *
 * Ext.ux.grid.livegrid.CheckboxSelectionModel is licensed under the terms of the
 *                  GNU Open Source GPL 3.0
 * license.
 *
 * Commercial use is prohibited. Visit <http://ext-livegrid.com>
 * if you need to obtain a commercial license.
 *
 * This program is free software: you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more
 * details.
 *
 * You should have received a copy of the GNU General Public License along with
 * this program. If not, see <http://www.gnu.org/licenses/gpl.html>.
 *
 */

Ext.namespace('Ext.ux.grid.livegrid');

/**
 * @class Ext.ux.grid.livegrid.CheckboxSelectionModel
 * @extends Ext.ux.grid.livegrid.RowSelectionModel
 * @constructor
 * @param {Object} config
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
Ext.ux.grid.livegrid.CheckboxSelectionModel = Ext.extend(Ext.ux.grid.livegrid.RowSelectionModel, {

    /**
     * @cfg {Boolean} checkOnly <tt>true</tt> if rows can only be selected by clicking on the
     * checkbox column (defaults to <tt>false</tt>).
     */
    /**
     * @cfg {Number} width The default width in pixels of the checkbox column (defaults to <tt>20</tt>).
     */
    width : 20,

    // private
    menuDisabled : true,
    sortable : false,
    fixed : true,
    dataIndex : '',
    id : 'checker',
    headerCheckbox : null,
    markAll : false,

    constructor : function()
    {
        if (!this.header) {
            this.header = Ext.grid.CheckboxSelectionModel.prototype.header;
        }

        this.sortable = false;

        Ext.ux.grid.livegrid.CheckboxSelectionModel.superclass.constructor.call(this);
    },

    // private
    initEvents : function()
    {
        Ext.ux.grid.livegrid.CheckboxSelectionModel.superclass.initEvents.call(this);

        this.grid.view.on('reset', function(gridView, forceReload) {
                this.headerCheckbox = new Ext.Element(
                    gridView.getHeaderCell(this.grid.getColumnModel().getIndexById(this.id)).firstChild
                );
                if (this.markAll && forceReload === false) {
                    this.headerCheckbox.addClass('x-grid3-hd-checker-on');
                }
        }, this);

        Ext.grid.CheckboxSelectionModel.prototype.initEvents.call(this);
    },

    // private
    onMouseDown : function(e, t)
    {
        if(e.button === 0 && t.className == 'x-grid3-row-checker') {
            e.stopEvent();
            var row = e.getTarget('.x-grid3-row');
            if(row){
                if (this.headerCheckbox) {
                    this.markAll = false;
                    this.headerCheckbox.removeClass('x-grid3-hd-checker-on');
                }
            }
        }

        return Ext.grid.CheckboxSelectionModel.prototype.onMouseDown.call(this, e, t);
    },

    // private
    onHdMouseDown : function(e, t)
    {
        if (t.className == 'x-grid3-hd-checker' && !this.headerCheckbox) {
            this.headerCheckbox = new Ext.Element(t.parentNode);
        }

        return Ext.grid.CheckboxSelectionModel.prototype.onHdMouseDown.call(this, e, t);
    },

    // private
    renderer : function(v, p, record)
    {
        return Ext.grid.CheckboxSelectionModel.prototype.renderer.call(this, v, p, record);
    },

// -------- overrides

    /**
     * Overriden to prevent selections by shift-clicking
     */
    handleMouseDown : function(g, rowIndex, e)
    {
        if (e.shiftKey) {
            return;
        }

        this.markAll = false;

        if (this.headerCheckbox) {
            this.headerCheckbox.removeClass('x-grid3-hd-checker-on');
        }

        Ext.ux.grid.livegrid.CheckboxSelectionModel.superclass.handleMouseDown.call(this, g, rowIndex, e);
    },

    /**
     * Overriden to clear header sort state
     */
    clearSelections : function(fast)
    {
        if(this.isLocked()){
            return;
        }

        this.markAll = false;

        if (this.headerCheckbox) {
            this.headerCheckbox.removeClass('x-grid3-hd-checker-on');
        }

        Ext.ux.grid.livegrid.CheckboxSelectionModel.superclass.clearSelections.call(this, fast);
    },

    /**
     * Selects all rows if the selection model
     * {@link Ext.grid.AbstractSelectionModel#isLocked is not locked}.
     */
    selectAll : function()
    {
        Ext.ux.grid.livegrid.CheckboxSelectionModel.superclass.selectAll.call(this);

        this.markAll = true;

        if (this.headerCheckbox) {
            this.headerCheckbox.addClass('x-grid3-hd-checker-on');
        }
    }


});
