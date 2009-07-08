/**
 * Ext.ux.layout.flexAccord.DropTarget
 * Copyright (c) 2009, http://www.siteartwork.de
 *
 * Ext.ux.layout.flexAccord.DropTarget is licensed under the terms of the
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
 * A special implementation of a DropTarget for use with
 * {Ext.ux.layout.flexAccord.DropPanel}.
 *
 * @class Ext.ux.layout.flexAccord.DropTarget
 * @extends Ext.dd.DropTarget
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
Ext.ux.layout.flexAccord.DropTarget = function(accordionPanel, cfg)
{
    this.accordionPanel = accordionPanel;
    Ext.dd.ScrollManager.register(accordionPanel.body);
    Ext.ux.layout.flexAccord.DropTarget.superclass.constructor.call(
        this,
        accordionPanel.bwrap.dom,
        cfg
    );
};

Ext.extend(Ext.ux.layout.flexAccord.DropTarget, Ext.dd.DropTarget, {

    /**
     * Caches the last index position that was determined by a drag event.
     */
    _lastPos : -1,

    /**
     * Looks up the first element that's resizable in the panel when a proxy
     * gets dragged to a specific position.
     *
     * @param {Number} height The height of the proxy that gets dragged.
     */
    _findResizableElement : function(accordionPanel, neededHeight)
    {
        var items  = accordionPanel.items.items;
        var layout = accordionPanel.getLayout();

        var resizable    = null;
        var notResizable = null;

        var overallHeight = 0;
        var innerHeight   = accordionPanel.getInnerHeight();

        for (var i = 0, len = items.length; i < len; i++) {
            overallHeight += items[i].getSize().height;
            if (!items[i].collapsed) {
                if (!resizable && layout._isResizable(items[i]) &&  items[i].getSize().height > neededHeight) {
                    resizable = items[i];
                }

                if (!notResizable && !layout._isResizable(items[i]) &&  items[i].getSize().height > neededHeight) {
                    notResizable = items[i];
                }
            }
        }

        if (resizable) {
            return {
                item          : resizable,
                overallHeight : overallHeight,
                innerHeight   : innerHeight
            };
        }

        return {
            item          : notResizable,
            overallHeight : overallHeight,
            innerHeight   : innerHeight
        };
    },

    /**
     * Convinient method for creating configuration objects for use with
     * drag/drop events.
     *
     * @return  {Object}
     */
    createEvent : function(dd, e, data, pos)
    {
        return {
            accordionPanel : this.accordionPanel,
            panel          : data.panel,
            position       : pos,
            data           : data,
            source         : dd,
            rawEvent       : e,
            status         : this.dropAllowed
        };
    },

    /**
     * Overrides parent implementation. Checks if a last resize info is available and accordingly
     * resets the last resized panel to its original value, before a drag occured.
     *
     * @param {Ext.dd.DragSource} source The drag source that was dragged over this drop target
     * @param {Event} e The event
     * @param {Object} data An object containing arbitrary data supplied by the drag source
     * @return {String} status The CSS class that communicates the drop status back to the source so that the
     * underlying {@link Ext.dd.StatusProxy} can be updated
     */
    notifyEnter : function(dd, e, data)
    {
        var dAllowed = Ext.ux.layout.flexAccord.DropTarget.superclass.notifyEnter.call(this, dd, e, data);

        var pId = dd.getProxy().getProxy().dom.parentNode.id;

        if (data._lastResizeInfo && pId != this.accordionPanel.body.dom.id) {
            data._lastResizeInfo.panel.setHeight(data._lastResizeInfo.height);
            data._lastResizeInfo = null;
        }

        return dAllowed;
    },

    /**
     * Called when the dragged item moves over a possible drop target.
     *
     */
    notifyOver : function(dd, e, data)
    {
        var xy             = e.getXY()
        var accordionPanel = this.accordionPanel;
        var px             = dd.proxy;

        // find insert position
        var p     = null;
        var match = false;
        var pos   = 0;
        var items = accordionPanel.items;

        if (items) {
            for(var len = items.length; pos < len; pos++){
                p = items.get(pos);
                var h = p.el.getHeight();
                if(h !== 0 && (p.el.getY()+(h/2)) > xy[1]){
                    match = true;
                    break;
                }
            }
        } else {
            pos = false;
        }

        var overEvent = this.createEvent(dd, e, data, pos);

        if(accordionPanel.fireEvent('validatedrop', overEvent) !== false &&
           accordionPanel.fireEvent('beforedragover', overEvent) !== false){

            var layout     = this.accordionPanel.getLayout();
            var proxyEl    = px.getProxy();

            // if the proxy moves over a panel that has an expanded active item,
            // we need to resize the active item so the proxy fits into the panel
            // without the need of scrolling, if, and only if the proxy moves to a panel that did
            // not hold the active item in first place,

            if (dd.panel.ownerCt.getId() != accordionPanel.getId()) {
                var resizeInfo = this._findResizableElement(accordionPanel, proxyEl.getSize().height);

                if (resizeInfo.item && resizeInfo.innerHeight - resizeInfo.overallHeight < proxyEl.getSize().height) {
                    accordionPanel.doLayout();
                    data._lastResizeInfo = {
                        height : resizeInfo.item.getSize().height,
                        panel  : resizeInfo.item
                    };
                    resizeInfo.item.setHeight(resizeInfo.item.getSize().height-proxyEl.getSize().height);
                }
            }

            proxyEl.setWidth('auto');

            if (match) {
                if (px.proxy.dom.nextSibling != p.el.dom) {
                    px.moveProxy(p.el.dom.parentNode, p.el.dom);
                }
            } else {
                Ext.fly(accordionPanel.body).appendChild(proxyEl);
            }

            this._lastPos = pos;

            accordionPanel.fireEvent('dragover', overEvent);

            return overEvent.status;
        }else{
            return overEvent.status;
        }

    },

    notifyDrop : function(dd, e, data, pos)
    {
        if(this._lastPos == -1){
            return;
        }

        var pos = this._lastPos;

        var dropEvent = this.createEvent(dd, e, data, pos);

        if(this.accordionPanel.fireEvent('validatedrop', dropEvent) !== false &&
           this.accordionPanel.fireEvent('beforedrop', dropEvent) !== false){

            dd.proxy.getProxy().remove();
            data._lastResizeInfo = null;

            var oldPanel  = dd.panel.ownerCt;
            var newPanel  = this.accordionPanel;
            var newLayout = newPanel.getLayout();
            var oldLayout = oldPanel.getLayout();

            if (oldPanel.getId() != newPanel.getId()) {
                oldPanel.getLayout().unregisterPanel(dd.panel, this.accordionPanel);
                oldPanel.remove(dd.panel, false);
                oldPanel.doLayout();
                oldPanel.getLayout().adjustHeight();
            };

            dd.panel.el.dom.parentNode.removeChild(dd.panel.el.dom);

            if(pos !== false){
                this.accordionPanel.insert(pos, dd.panel);
            } else {
                this.accordionPanel.add(dd.panel);
            }

            (function() {
                this.ownerCt.getLayout().rendered = false;
                this.ownerCt.doLayout();
                if (this._wasExpanded) {
                    this.expand(false);
                    delete this._wasExpanded;
                }
            }).defer(1, dd.panel);


            this.accordionPanel.fireEvent('drop', dropEvent);
        }

        this._lastPos = -1;
    }

});
