/**
 * Ext.ux.grid.livegrid.DragZone
 * Copyright (c) 2007-2008, http://www.siteartwork.de
 *
 * Ext.ux.grid.livegrid.DragZone is licensed under the terms of the
 *                  GNU Open Source GPL 3.0
 * license.
 *
 * Commercial use is prohibited. Contact "Thorsten Suckow-Homberg" <ts@siteartwork.de>
 * if you need a commercial license.
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
 * If you would like to support the development and support of the Ext.ux.Livegrid
 * component, you can make a donation: <http://www.siteartwork.de/livegrid>
 */

Ext.namespace('Ext.ux.grid.livegrid');

/**
 * @class Ext.ux.grid.livegrid.DragZone
 * @extends Ext.dd.DragZone
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
Ext.ux.grid.livegrid.DragZone = function(grid, config){

    this.view = grid.getView();
    Ext.ux.grid.livegrid.DragZone.superclass.constructor.call(this, this.view.mainBody.dom, config);

    if(this.view.lockedBody){
        this.setHandleElId(Ext.id(this.view.mainBody.dom));
        this.setOuterHandleElId(Ext.id(this.view.lockedBody.dom));
    }

    this.scroll = false;
    this.grid   = grid;
    this.ddel   = document.createElement('div');

    this.ddel.className = 'x-grid-dd-wrap';

    this.view.ds.on('beforeselectionsload', this.onBeforeSelectionsLoad, this);
    this.view.ds.on('selectionsload',       this.onSelectionsLoad,       this);
};

Ext.extend(Ext.ux.grid.livegrid.DragZone, Ext.dd.DragZone, {
    ddGroup : "GridDD",

    isDropValid : true,

    getDragData : function(e)
    {
        var t = Ext.lib.Event.getTarget(e);
        var rowIndex = this.view.findRowIndex(t);
        if(rowIndex !== false){
            var sm = this.grid.selModel;
            if(!sm.isSelected(rowIndex) || e.hasModifier()){
                sm.handleMouseDown(this.grid, rowIndex, e);
            }

            return {grid: this.grid, ddel: this.ddel, rowIndex: rowIndex, selections:sm.getSelections()};
        }
        return false;
    },

    onInitDrag : function(e)
    {
        this.view.ds.loadSelections(this.grid.selModel.getPendingSelections(true));

        var data = this.dragData;
        this.ddel.innerHTML = this.grid.getDragDropText();
        this.proxy.update(this.ddel);
    },

    onBeforeSelectionsLoad : function()
    {
        this.isDropValid = false;
        Ext.fly(this.proxy.el.dom.firstChild).addClass('x-dd-drop-waiting');
    },

    onSelectionsLoad : function()
    {
        this.isDropValid = true;
        this.ddel.innerHTML = this.grid.getDragDropText();
        Ext.fly(this.proxy.el.dom.firstChild).removeClass('x-dd-drop-waiting');
    },

    afterRepair : function()
    {
        this.dragging = false;
    },

    getRepairXY : function(e, data)
    {
        return false;
    },

    onStartDrag : function()
    {

    },

    onEndDrag : function(data, e)
    {
    },

    onValidDrop : function(dd, e, id)
    {
        this.hideProxy();
    },

    beforeInvalidDrop : function(e, id)
    {

    }
});