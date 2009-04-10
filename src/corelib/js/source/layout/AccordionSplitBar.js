/**
 * conjoon
 * (c) 2002-2009 siteartwork.de/conjoon.org
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

Ext.namespace('com.conjoon.layout');

/**
 * Splitbars to be used with com.conjoon.layout.ResizableAccordionLayout
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
com.conjoon.layout.AccordionSplitBar = function(dragElement, resizingElement, orientation, placement, existingProxy){


    this.resizingComponent = resizingElement;

    com.conjoon.layout.AccordionSplitBar.superclass.constructor.call(this, dragElement, resizingElement.el, orientation, placement, existingProxy);

    /**
     * @param {Ext.SplitBar} s The SplitBar using this adapter
     * @param {Number} newSize The new size to set
     * @param {Function} onComplete A function to be invoked when resizing is complete
     */
    this.adapter.setElementSize = function(s, newSize, onComplete)
    {
        var resizedElement = s.resizingComponent;
        var container      = resizedElement.ownerCt;
        var layout         = resizedElement.ownerCt.getLayout();

        var items = container.items.items;
        var spill = 0;

        var tHeight = 0;

        for (var i = 0, len = items.length; i < len; i++) {
            if (items[i] == resizedElement) {
                spill = items[i].getSize().height - newSize;

                if (resizedElement.collapsed) {
                    layout.expand(resizedElement);
                } else if (newSize - (items[i].getSize().height - resizedElement.bwrap.getHeight()) <= 5) {
                    layout.collapse(items[i]);
                    return;
                }

                items[i].setHeight(newSize);
                items[i].height = newSize;

                var nextResizableIndex = -1;

                for (var m = i+1; m < len; m++) {
                    if (layout._isResizable(items[m])) {
                        nextResizableIndex = m;
                        break;
                    }
                }

                if (items[nextResizableIndex]) {

                    tHeight = items[nextResizableIndex].getSize().height;

                    if (items[nextResizableIndex].collapsed) {
                        layout.expand(items[nextResizableIndex]);
                    } else if ((items[nextResizableIndex].bwrap.getHeight()+(spill)) <= 5) {
                        layout.collapse(items[nextResizableIndex]);
                        return;
                    }

                    var sizings = {
                        remainingSpace : 0,
                        overallHeight  : 0
                    };

                    for (var a = 0; a < len; a++) {
                        if (items[a] != items[nextResizableIndex]) {
                            sizings.overallHeight += items[a].getSize().height;
                        }
                    }
                    sizings.remainingSpace = container.getInnerHeight() - sizings.overallHeight;

                    items[nextResizableIndex].setHeight(sizings.remainingSpace);
                    items[nextResizableIndex].height = sizings.remainingSpace;


                }

                break;
            }
        }



    };

};


Ext.extend(com.conjoon.layout.AccordionSplitBar, Ext.SplitBar, {

    getMinimumSize : function()
    {
        return this.resizingComponent.getSize().height-
               this.resizingComponent.bwrap.getHeight();
    },

    getMaximumSize : function()
    {
        var items = this.resizingComponent.ownerCt.items.items;
        var sibl  = null;
        var item  = this.resizingComponent;

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