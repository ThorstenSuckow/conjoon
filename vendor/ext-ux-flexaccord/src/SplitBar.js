/**
 * Ext.ux.layout.flexAccord.SplitBar
 * Copyright (c) 2009, http://www.siteartwork.de
 *
 * Ext.ux.layout.flexAccord.SplitBar is licensed under the terms of the
 *                  GNU Open Source LGPL 3.0
 * license.
 *
 * This program is free software: you can redistribute it and/or modify it under
 * the terms of the LGPL as published by the Free Software
 * Foundation, either version 3 of the License, or any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the LGPL License for more
 * details.
 *
 * You should have received a copy of the GNU LGPL along with
 * this program. If not, see <http://www.gnu.org/licenses/lgpl.html>.
 *
 */

Ext.namespace('Ext.ux.layout.flexAccord');

/**
 * Splitbars to be used with {Ext.ux.layout.flexAccord.Layout}.
 * Splitbars allow to dynamically resize panels.
 *
 * @class Ext.ux.layout.flexAccord.SplitBar
 * @extends Ext.SplitBar
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
Ext.ux.layout.flexAccord.SplitBar = function(
    dragElement,  resizingElement, orientation, placement, existingProxy){


    this.resizingComponent = resizingElement;

    Ext.ux.layout.flexAccord.SplitBar.superclass.constructor.call(
        this, dragElement, resizingElement.el, orientation, placement, existingProxy
    );

    /**
     * Overrides parent implementation.
     * The method will call the setItemHeight method of the {Ext.ux.layout.flexAccord.Layout}
     * layout manager of the resizing component's owner.
     *
     * @param {Ext.SplitBar} s The SplitBar using this adapter
     * @param {Number} newSize The new size to set
     * @param {Function} onComplete A function to be invoked when resizing is complete
     */
    this.adapter.setElementSize = function(s, newSize, onComplete)
    {
        var resizedElement = s.resizingComponent;
        resizedElement.ownerCt.getLayout().setItemHeight(resizedElement, newSize, true);
    };

};


Ext.extend(Ext.ux.layout.flexAccord.SplitBar, Ext.SplitBar, {

    /**
     * Gets the minimum size for the resizing element
     * @return {Number} The minimum size
     */
    getMinimumSize : function()
    {
        var item   = this.resizingComponent;
        var items  = item.ownerCt.items.items;
        var sibl   = null;
        var layout = item.ownerCt.getLayout()

        var itemPos = items.indexOf(item);

        if (itemPos == items.length-2 && items[items.length-1].resizable === false) {
            if (!items[items.length-1].collapsed) {
                return item.getSize().height;
            } else {
                return item.getSize().height-
               (layout._orgHeights[items[items.length-1].id]-layout.getHeaderHeight(item));

            }
        }

        return this.resizingComponent.getSize().height-
               this.resizingComponent.bwrap.getHeight();
    },

    /**
     * Gets the maximum size for the resizing element
     * @return {Number} The maximum size
     */
    getMaximumSize : function()
    {
        var items       = this.resizingComponent.ownerCt.items.items;
        var sibl        = null;
        var item        = this.resizingComponent;
        var innerHeight = item.ownerCt.getInnerHeight();
        var layout      = item.ownerCt.getLayout();

        var itemPos = items.indexOf(item);

        if (item.resizable !== false && itemPos != items.length-2) {
            var pH = 0;
            for (var i = 0, len = items.length; i < len; i++) {
                if (i > itemPos) {
                    pH += layout.getHeaderHeight(items[i]);
                } else if (i != itemPos) {
                    pH += items[i].height;
                }
            }
            return (innerHeight - pH);
        }

        if (item.resizable === false) {
            return item.ownerCt.getLayout()._orgHeights[item.id];
        }

        for (var i = 0, len = items.length; i < len; i++) {
            if (items[i] == item && items[i+1]) {
                sibl = items[i+1];

                return item.getSize().height +
                       sibl.getSize().height -
                       (item.getSize().height - item.bwrap.getHeight());
            }

        }
    }

});