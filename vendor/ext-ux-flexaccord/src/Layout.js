/**
 * Ext.ux.layout.flexAccord.Layout
 * Copyright (c) 2009, http://www.siteartwork.de
 *
 * Ext.ux.layout.flexAccord.Layout is licensed under the terms of the
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
 * A layout manager that allows for managing collapsible panels which can be
 * resized using {Ext.ux.layout.flexAccord.SplitBar}s. In most places, it behaves
 * like an Accordionlayout.
 * In this kind of layout, all panels may be opened at once. You may specify
 * two additional properties for a panel: "height" and "resizable". The layout
 * manager will then calculate the dimension before a panel expands so that each panel
 * still fits into the container to which this layout is attached. If a height for a
 * panel is specified (i.e. equals to a number > 0), then the layout manager will
 * try to keep this height for the panel if a resize of the container or an expand
 * of the panel itself happens. However, if a panel gets resized manually by the
 * user using the SplitBar, then the initial height will get set to the new value
 * of the drag operation. If the "resizable" property was set for any panel, this panel
 * will not be resizable, and when using the SplitBar to manually resize the panel,
 * it will either collapse (if the set height is less than the value of it's "height"
 * property), or expand if the panel's height is greater than/equal to the panel's "height"
 * property.
 *
 * Example for a simple setup that used splitbars to resize panels and allows for drag/drop
 * operations:
<pre>
    // a panel that is not resizable with a fixed height
    // of 150
    var ddPanel1 = new Ext.Panel({
        title     : "<b>DD Panel 1</b> (not resizable)",
        // we will not allow this panel to be resizable.
        resizable : false,
        // the height of this panel will always be 150, and since
        // resizable is set to false, it will always stay like this
        height    : 150,
        // yes, this panel is draggable!
        draggable : true,
        layout    : 'fit',
        autoScroll : true,
        html      : '<div style="background:#ffc1f1;height:500px;width:500px">This panel has an init height of 150px and is NOT resizable!</div>'
    });

    // a draggable, resizable panel with no intial height set
    var ddPanel2 = new Ext.Panel({
        title     : "<b>DD Panel 2</b> (resizable)",
        // yes, this panel is draggable!
        draggable : true,
        layout    : 'fit',
        autoScroll : true,
        html      : '<div style="background:#c1c7ff;height:500px;width:500px">This panel\'s height is automatically calculated. The panel is resizable</div>'
    });

    // We build the DropPanel here. Components within the panel get
    // managed by the Ext.ux.layout.flexAccord.Layout. We do not need to specify the
    // layout, since AccordionDropPanel uses Ext.ux.layout.flexAccord.Layout by
    // default. Basically, you can use any container as long as you set its layout
    // manager to an instance of Ext.ux.layout.flexAccord.Layout. By using
    // Ext.ux.layout.flexAccord.DropPanel as teh container, however, you are allowed
    // to drag/drop containing panels to another position.
    var accordionDropPanel = new Ext.ux.layout.flexAccord.DropPanel({
        layoutConfig : {
            animate : true
        },
        border : false,
        items : [
            ddPanel1,
            ddPanel2
        ]
    });
</pre>
 *
 *
 * @class Ext.ux.layout.flexAccord.Layout
 * @extends Ext.layout.ContainerLayout
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
Ext.ux.layout.flexAccord.Layout = Ext.extend(Ext.layout.ContainerLayout, {

	/**
     * @cfg {Number}
     * The height of the panels which have no height specified (defaults to 100)
     */
    defaultHeight : 100,

    /**
     * @cfg {Boolean} titleCollapse
     * True to allow expand/collapse of each contained panel by clicking anywhere on the title bar, false to allow
     * expand/collapse only when the toggle tool button is clicked (defaults to true).  When set to false,
     * {@link #hideCollapseTool} should be false also.
     */
    titleCollapse : true,

    /**
     * @cfg {Boolean} hideCollapseTool
     * True to hide the contained panels' collapse/expand toggle buttons, false to display them (defaults to false).
     * When set to true, {@link #titleCollapse} should be true also.
     */
    hideCollapseTool : false,

    /**
     * @cfg {Boolean} animate
     * True to slide the contained panels open and closed during expand/collapse using animation, false to open and
     * close directly with no animation (defaults to false).  Note: to defer to the specific config setting of each
     * contained panel for this property, set this to undefined at the layout level.
     */
    animate : false,

    /**
     * Makes sure the layout manager listens to the resize event of the
     * container the layout is attached to. Defaults to true and should not be
     * changed.
     * @type {Boolean} monitorResize
     * @protected
     */
    monitorResize:true,

    /**
     * @type {Object} maps requested heights from panels which are not resizable with their
     * specified width
     */
    _orgHeights : null,

    /**
     * Renders the specified item into the layout and adds the splitbar
     * to the item if item's properties "resizable" does not equal to "false".
     *
     * @param {Ext.Panel} c
     */
    renderItem : function(c, position, target)
    {
        if(this.animate === false){
            c.animCollapse = false;
        }
        c.collapsible = true;
        c.autoWidth   = true;

        if(this.titleCollapse){
            c.titleCollapse = true;
        }
        if(this.hideCollapseTool){
            c.hideCollapseTool = true;
        }

        var initDDOverride = !c.rendered;

        Ext.ux.layout.flexAccord.Layout.superclass.renderItem.call(this, c, position, target);

        if (initDDOverride && c.dd) {
            c.dd.b4StartDrag = c.dd.b4StartDrag.createInterceptor(
                function() {
                    if (!this.panel.collapsed) {
                        this.panel._wasExpanded = true;
                        this.panel.ownerCt.getLayout().collapse(this.panel, true);
                    }
                }
            , c.dd);
        }

        if (!this._orgHeights) {
            this._orgHeights = {};
        }

        if (c.height === undefined || c.height == 'auto') {
            c.height = this.defaultHeight;
        }

        if (c.resizable === false && !this._orgHeights[c.getId()]) {
            this._orgHeights[c.getId()] = c.height;
        }

        if (c.collapsed) {
            c.height = this.getHeaderHeight(c);
        }

        c.header.addClass('x-accordion-hd');

        // during drag and drop, the item might get re-rendered
        // detach listeners first, just to be sure
        c.un('beforeexpand',   this.beforeExpand, this);
        c.un('collapse',       this.onCollapse, this);
        c.on('beforeexpand',   this.beforeExpand, this);
        c.on('collapse',       this.onCollapse, this);
    },


    deleteSplitter : function(c)
    {
        if (c.splitter) {
            c.splitter.destroy(true);
            c.splitter = null;
            delete c.splitter;
            delete c.splitEl;
        }
    },

    /**
     * Adds a Ext.ux.layout.flexAccord.SplitBar to the specified panel.
     */
    addSplitter : function(c)
    {
        if (c.splitter) return;
        //this.deleteSplitter(c);

        c.splitEl = c.el.createChild({
            cls  : 'ext-ux-flexaccord-splitter x-layout-split x-layout-split-south',
            html : "&#160;",
            id   : c.getId() + '-xsplit'
        });
        c.splitter = new Ext.ux.layout.flexAccord.SplitBar(
            c.splitEl.dom, c, Ext.SplitBar.TOP
        );
    },

    /**
     * Registers a panel for this layout. Convinient if one panel gets dragged
     * from one container with an instance of this layout to a container with an
     * instance of this layout.
     * Unregisters all listeners and takes care of properly moving all related
     * properties needed to manage the panel to this layout.
     *
     * @param {Ext.Panel} panel
     */
    unregisterPanel : function(panel, newContainer)
    {
        // remove listeners
        panel.un('collapse',     this.onCollapse,   this);
        panel.un('beforeexpand', this.beforeExpand, this);

        this.deleteSplitter(panel);

        // check for orgheight
        if (this._orgHeights[panel.getId()]) {
            var newLayout = newContainer.getLayout();
            if (!newLayout._orgHeights) {
                newLayout._orgHeights = {};
            }
            newLayout._orgHeights[panel.getId()] = this._orgHeights[panel.getId()];
            delete this._orgHeights[panel.getId()];
        }
    },

    /**
     * API only.
     */
    collapse : function(p, suspend)
    {
        if (!p.rendered) {
            return;
        }
        if (suspend === true) {
            p.un('collapse', this.onCollapse, this);
        }
        p.collapse(false);
        if (suspend === true) {
            p.on('collapse', this.onCollapse, this);
        }
    },

    /**
     * API only
     */
    expand : function(p, ignoreListener)
    {
        if (ignoreListener !== false) {
            p.un('beforeexpand', this.beforeExpand, this);
        }

        p.expand(false);

        if (ignoreListener !== false) {
            p.on('beforeexpand', this.beforeExpand, this);
        }
    },

    /**
     * manages the rendering of the splitbars.
     */
    manageSplitbars : function()
    {
        var items = this.container.items.items;
        var len   = items.length;

        if (len == 0) {
            return;
        }

        // delete the splitter for the last item
        this.deleteSplitter(items[len-1]);

        for (var i = 0; i < len-1; i++) {

            this.addSplitter(items[i]);

            if (items[i+1] && items[i+1].dd && items[i+1].dd.proxy && items[i+1].dd.proxy.getProxy()) {
                this.deleteSplitter(items[i]);
            }
        }
    },

    /**
     * Manages setting the width of the Splitbars of the containing
     * panels.
     */
    onLayout : function(ct, target)
    {
        Ext.ux.layout.flexAccord.Layout.superclass.onLayout.call(this, ct, target);

        var width = target.getStyleSize().width;

        var items = ct.items.items;

        this.manageSplitbars();

        for (var i = 0, len = items.length; i < len; i++) {

            if (items[i].splitEl) {
                items[i].splitEl.setWidth(items[i].el.getWidth());
            }

        }

        this.rendered = true;
    },

    /**
     * API only.
     * Returns the height of the headers of a panel.
     *
     * @param {Ext.Panel} panel The panel to retrieve the header's height for.
     * @param {Boolean} toolbars Whether to consider any containing toolbars.
     *
     * @return {Number}
     */
    getHeaderHeight : function(panel, toolbars)
    {
        return panel.getSize().height
        + (toolbars === true && panel.getBottomToolbar()
          ? panel.getBottomToolbar().getSize().height : 0)
        + (toolbars === true && panel.getTopToolbar()
           ? panel.getTopToolbar().getSize().height : 0)
        - panel.bwrap.getHeight();
    },

    /**
     * Sets the height of the panels and takes care of adjusting height
     * if there is remining space left in the container.
     *
     * @param {Array} exclude An array of panels that should not be used as the
     * firstSpillItem, i.e. a panel that should be used to fill out remaining space.
     */
    adjustHeight : function(exclude)
    {
        var width = this.container.el.getStyleSize().width;

        if (!Ext.isArray(exclude)) {
            exclude = [];
        }
        var items        = this.container.items;
        var innerHeight  = this.container.getInnerHeight();
        var panelHeights = 0;
        var firstSpillItem  = null;
        for (var i = 0, len = items.items.length; i < len; i++) {
            var item = items.get(i);

            if (item.hidden) {
                continue;
            }

            if (!firstSpillItem && this._isResizable(item, true)) {
                if (exclude.indexOf(item) == -1) {
                    firstSpillItem = item;
                }
            }

            if (item.height <= this.getHeaderHeight(item)+3) {
                this.collapse(item, true);
            } else if (item.collapsed) {
                this.expand(item);
            }

            item.setSize({height : item.height, width : width});

            panelHeights += item.height;
        }

        if (panelHeights < innerHeight && firstSpillItem) {
            firstSpillItem.height = firstSpillItem.getSize().height+(innerHeight-panelHeights);
            firstSpillItem.setHeight(firstSpillItem.height);
        } else if (panelHeights > innerHeight && firstSpillItem) {
            // if rem is smaller than hh, call this method recursively
            var rem = firstSpillItem.getSize().height-(panelHeights-innerHeight);
            var hh  = this.getHeaderHeight(firstSpillItem);
            firstSpillItem.height = Math.max(hh, rem);
            firstSpillItem.setHeight(firstSpillItem.height);
            if (rem <= hh) {
                this.adjustHeight(exclude.concat(firstSpillItem));
            }
        }
    },

    /**
     * Takes care of resizing the containing panels when the container
     * was resized.
     *
     */
    onResize : function()
    {
        if (!this.rendered) {
            return;
        }

        Ext.ux.layout.flexAccord.Layout.superclass.onResize.call(this);

        var items = this.container.items;

        var resizables    = [];
        var notResizables = [];
        var panelsHeight  = 0;
        var innerHeight   = this.container.getInnerHeight();

        for (var i = 0, len = items.length; i < len; i++) {
            if (this._isResizable(items.get(i))) {
                resizables.push(items.get(i));
            } else {
                notResizables.push(items.get(i));
            }
            panelsHeight += items.get(i).height;
        }

        var remaining = this.container.getInnerHeight() - panelsHeight;

        if (remaining < 0) {

            var sortFunc = function(a, b) {
                return a.height-b.height;
            };

            resizables.sort(sortFunc);
            notResizables.sort(sortFunc);

            resizables.reverse();

            var spill = remaining;

            if (resizables.length > 0) {
                spill = Math.floor(spill/resizables.length);
            } else if (notResizables.length > 0) {
                spill = Math.floor(spill/notResizables.length);
            }

            for (var i = 0, len = resizables.length; i < len && spill != 0 && panelsHeight > innerHeight; i++) {
                spill = this.addSpill(resizables[i], spill);
                panelsHeight -= spill;
            }

            for (var i = 0, len = notResizables.length; i < len && spill != 0  && panelsHeight > innerHeight; i++) {
                spill = this.addSpill(notResizables[i], spill);
                panelsHeight -= spill;
            }

            this.adjustHeight(notResizables.concat(resizables));

        } else {
            this.adjustHeight();
        }


    },


    /**
     * Returns true if the item can be resized.
     *
     * @param {Ext.Panel} item
     * @param {Boolean} ignoreCollapse Whether to ignore collapsed state
     * @param {boolean} ignoreResizable Whether to ignore the panel's
     * resizable proeprty
     *
     * @return {boolean}
     */
    _isResizable : function(item, ignoreCollapse, ignoreResizable)
    {
        if (!item.rendered || (ignoreResizable !== true && item.resizable === false)) {
            return false;
        }
        itemId     = item.getId();
        itemHeight = item.getSize().height;

        if (itemHeight <= this.getHeaderHeight(item) && ignoreCollapse !== true) {
            return false;
        }

        return true;
    },

    /**
     * Called when a panel is about to get expanded.
     *
     * @param {Ext.Panel} p
     * @param {Boolean} anim
     */
    beforeExpand : function(p, anim)
    {
        var heightToSet = this._orgHeights[p.id] ? this._orgHeights[p.id] : this.defaultHeight;

        var items        = this.container.items;
        var item         = null;
        var panelHeights = null;

        for (var i = 0, len = items.length; i < len; i++) {
            item = items.get(i);
            panelHeights += items.get(i).getSize().height;
        }

        if (panelHeights < this.container.getInnerHeight()) {
            heightToSet = this.container.getInnerHeight()-
                          (panelHeights - this.getHeaderHeight(p));
            if (this._orgHeights[p.id] && heightToSet > this._orgHeights[p.id]) {
                p.height = this._orgHeights[p.id];
                this.adjustHeight(items.items);
                return;
            } else if (this._orgHeights[p.id]) {
                heightToSet = this._orgHeights[p.id];
            }
        }
        this.setItemHeight(p, heightToSet);
    },

    /**
     * Listener for the collapse event of the panels in the container
     * this layout is attached to. Will search for items which can be resized
     * so that all panels fill out the space in the container.
     *
     * @param {Ext.Panel} p
     * @param {Boolean} anim
     */
    onCollapse : function(p, anim)
    {
        var items        = this.container.items;
        var itemPos      = items.indexOf(p);
        var item         = null;
        var panelHeights = 0;
        var tmpItem      = null;

        for (var i = 0, len = items.length; i < len; i++) {
            tmpItem = items.get(i);

            // mark the first item that is resizable and which is a previous
            // node of the item collapsed
            if (!item && this._isResizable(tmpItem)) {
                item = tmpItem;
            }
            panelHeights += tmpItem.getSize().height;
        }

        if (item) {
            item.height = item.height + (this.container.getInnerHeight()-panelHeights);
            item.setHeight(item.height);
        }

        // its important to set the height attribute of the
        // collapsed item to its header height
        p.height = this.getHeaderHeight(p);
    },

    /**
     * Sets the height for a specified item in this container. Resizing
     * The item may trigger resizing (collapsing, expanding)
     * of other items in this conatiner.
     *
     * @param {Ext.Panel} resizedElement
     * @param {Number} newSize The new size for this item
     */
    setItemHeight : function(resizedElement, newSize, considerAll)
    {
        if (newSize <= 0) {
            return;
        }

        var container      = this.container;
        var items          = container.items;
        var spill          = 0;

        // the overflow
        var itemPos     = items.indexOf(resizedElement);
        var innerHeight = container.getInnerHeight();

        // first off, resize the element accordingly
        spill = newSize - resizedElement.height;
        var direction = spill  > 0 ? 'down' : 'up';

        spill = this.addSpill(resizedElement, spill);

        var panelHeights = resizedElement.height;

        var after   = [];
        var ordered = [];
        var notResizables  = [];
        for (var i = itemPos+1, len = items.items.length; i < len; i++) {
            if (items.get(i).resizable === false) {
                notResizables.push(items.get(i));
            } else {
                after.push(items.get(i));
            }
        }


        if (items.get(itemPos+1) && items.get(itemPos+1).resizable === false) {
            notResizables.reverse();
            //after.reverse();
        }

        ordered = after.concat(notResizables);

        for (var i = 0, len = ordered.length; i < len && spill != 0; i++) {
            spill = this.addSpill(ordered[i], spill);
        }

        this.adjustHeight();
    },


    /**
     * Adds spill to a specified panel. If the spill is negative, the specified
     * panel will be scaled down, otehrwise scaled up.
     * The method will return the overall spill of the container: If the sum of
     * heights of the panel within the container is bigger than the container's inner
     * height itself, a positive value will be returned, otherwise, a negative value
     * will be returned, or 0 (zero), if the sum of heights of the panels equal to
     * the container's inner height.
     * If the panel is not resizable, then the panel's height will  be automatically
     * set to the original height.
     *
     * @param {Ext.Panel} panel The panel to resize
     * @param {Number} spill The amount of pixels the panel gets resized with.
     */
    addSpill : function(panel, spill)
    {
        var tHeight  = panel.resizable === false
                       ? (spill > 0 ? this._orgHeights[panel.id] : this.getHeaderHeight(panel))
                       : (panel.height+spill) <= this.getHeaderHeight(panel)+2
                         ? this.getHeaderHeight(panel) : (panel.height+spill);
        var retSpill = 0;

        panel.height = tHeight <= 0 ? this.getHeaderHeight(panel) : tHeight;
        var panelHeights = 0;
        for (var i = 0, len = this.container.items.items.length; i < len; i++){
            panelHeights += this.container.items.get(i).height;
        }

        return this.container.getInnerHeight()-panelHeights;
    },

});