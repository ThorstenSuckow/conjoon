/**
 * Ext.ux.layout.flexAccord.Layout
 * Copyright (c) 2009-2014, http://www.siteartwork.de
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
 * Any panel that is managed by this layout manager can have new configuration
 * properties:
 * - {Number} height The initial height of this panel in this container. Due to
 *   resizing processes it is not guaranteed that the panel will have exactly
 *   this initial height
 * - {Boolean} resizable Whether this panel is resizable using the SplitBar.
 *   Defaults to true. If the panel is not resizable, the SplitBar can only be
 *   dragged to expand/collapse the panel.
 *
 * Additionally, during runtime, there will be a few more variables injected to
 * the panel:
 * - {Boolean} _wasExpanded Temporarily stores the "collapsed" state of the panel
 *   during drag drop. If this property equals to "true", the panel will be expanded
 *   again after dropped
 * - {String} _oldId Temporarily stores the id of the ownerCt where the drag operation
 *   started
 * - {Number} _oldHeight Temporarily caches the height of an panel before it gets
 *   collapsed during drag/drop
 * - {Ext.Element} splitEl The HTMLElement for use with the SplitBar
 * - {Ext.ux.layout.flexAccord.SplitBar} splitter The SplitBar for this panel
 * - {Boolean} _isDragged true if the panel is currently being dragged
 *
 * Any panel's dd methods "b4StartDrag" and "endDrag" will be intercepted/sequenced.
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
     * @type {Boolean} rendered Defaults to false and will be set to true once the first call
     * to onLayout was called, to prevent errors due to unrendered containing panels.
     */
    rendered : false,

    /**
     * @type {Number} previousHeight caches the innerHeight of the container when a resize
     * occurs. If that value does not change during the next resize event, no sizing
     * calculations will be made for the height of the containing panels.
     */
    previousHeight : 0,

    /**
     * Renders the specified item into the layout and adds the splitbar
     * to the item if item's properties "resizable" does not equal to "false".
     *
     * @param {Ext.Panel} c
     */
    renderItem : function(c, position, target)
    {
        if(this.animate === false) {
            c.animCollapse = false;
        } else if (this.animate === true){
            c.animCollapse = true;
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

            if (Ext.isIE7) {
                // override for IE7 - panel's bwrap will still be determined
                // to the state it was expanded - comment this and the panel's ghost
                // will always have the height of the panel before it got dragged
                c.dd.proxy.show = c.dd.proxy.show.createSequence(function(){
                    this.ghost.setHeight(this.panel.height);
                }, c.dd.proxy);
            }

            c.dd.b4StartDrag = c.dd.b4StartDrag.createInterceptor(
                function() {
                    var panel        = this.panel;
                    panel._oldId     = panel.ownerCt.getId();
                    panel._isDragged = true;
                    if (!panel.collapsed) {
                        panel._wasExpanded = true;
                        panel._oldHeight   = panel.height;
                        // its important to set the height property here
                        // since we will ignore the layout's oncollapse listener
                        // which usually takes care of this
                        var layout = panel.ownerCt.getLayout();
                        panel.height = layout.getHeaderHeight(panel, true);
                        layout.layout(panel.ownerCt.items.items);
                    }
                }
            , c.dd);
            // don't intercept - sequence!
            c.dd.endDrag = c.dd.endDrag.createSequence(
                function() {
                    var panel = this.panel;
                    // unset this property before any layout actions
                    // are processed
                    delete panel._isDragged;
                    if (panel._oldId == panel.ownerCt.getId()) {
                        if (panel._wasExpanded) {
                            panel.height = panel._oldHeight;
                        }
                        panel.ownerCt.doLayout();
                        delete panel._wasExpanded;
                    }
                    delete panel._oldHeight;
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
            c.height = 0;
        }

        c.header.addClass('x-accordion-hd');

        // during drag and drop, the item might get re-rendered
        // detach listeners first, just to be sure
        c.un('beforeexpand',   this.beforeExpand, this);
        c.un('collapse',       this.onCollapse, this);
        c.on('beforeexpand',   this.beforeExpand, this);
        c.on('collapse',       this.onCollapse, this);
    },


    /**
     * removes the SplitBar from the specified panel if it contains one.
     *
     * @param {Ext.Panel} panel
     * @private
     */
    deleteSplitter : function(panel)
    {
        if (panel.splitter) {
            panel.splitter.destroy(true);
            panel.splitter = null;
            delete panel.splitter;
            delete panel.splitEl;
        }
    },

    /**
     * Adds a Ext.ux.layout.flexAccord.SplitBar to the specified panel, if
     * it doesn't already contain one.
     *
     * @param {Ext.Panel} panel
     * @private
     */
    addSplitter : function(panel)
    {
        if (panel.splitter) {
            return;
        }

        panel.splitEl = panel.el.createChild({
            cls  : 'ext-ux-flexaccord-splitter x-layout-split x-layout-split-south',
            html : "&#160;",
            id   : panel.getId() + '-xsplit'
        });
        panel.splitter = new Ext.ux.layout.flexAccord.SplitBar(
            panel.splitEl.dom, panel, Ext.SplitBar.TOP
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
     * @private
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
     * Collapses the specified panel. If "suspend" is set to "true",
     * all "collapse"-listeners that were registered using this layout
     * manager will be suspendet during the collapse process.
     *
     * @param {Ext.Panel} p The panel to collapse
     * @param {Boolean} suspend Whether to suspend "collapse"-listeners
     * that were attached by this layout manager
     *
     * @private
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
     * Expands the specified panel. If "suspend" is set to "false",
     * all "beforeexpand"-listeners that were registered using this layout
     * manager will be called. Either submit "true" or nothing to suspend
     * the events.
     *
     * @param {Ext.Panel} p
     * @param {Boolean} suspend Whether to suspend the "beforeexpand" events defined
     * by this layout manager
     * @param {Boolean} animate Whether to show the expand animation effect
     *
     * @private
     */
    expand : function(p, suspend, animate)
    {
        if (animate !== true) {
            animate = false;
        }
        if (suspend !== false) {
            p.un('beforeexpand', this.beforeExpand, this);
        }

        p.expand(animate);

        if (suspend !== false) {
            p.on('beforeexpand', this.beforeExpand, this);
        }
    },

    /**
     * Manages the rendering of the splitbars.
     *
     * @private
     */
    manageSplitbars : function()
    {
        var items = this.container.items.items;
        var len   = items.length;

        if (len == 0) {
            return;
        }
        this.deleteSplitter(items[len-1]);
        var checkVisibility = true;
        for (var i = len - 2; i >= 0; i--) {
            if (checkVisibility && items[i+1].hidden) {
                this.deleteSplitter(items[i]);
            } else {
                checkVisibility = false;
                this.addSplitter(items[i]);
            }

        }
    },

    /**
     * Called when the container's "doLayout" method was called.
     * Takes care of properly sizing the splitbars of the containing
     * panels and adjusting the height of each of the containing panels.
     *
     */
    onLayout : function(ct, target)
    {
        Ext.ux.layout.flexAccord.Layout.superclass.onLayout.call(this, ct, target);

        var width = target.getStyleSize().width;

        var items = ct.items.items;
        this.manageSplitbars();

        for (var i = 0, len = items.length; i < len; i++) {

            if (items[i].splitEl) {
                items[i].splitEl.setWidth(items[i].getSize().width);
            }

        }

        if (!this.rendered) {
            this.rendered = true;
            this.onResize();
            return;
        }
    },

    /**
     * Takes care of resizing the containing panels when the container
     * was resized, to make sure they fit in this container without causing
     * spill or leaving remaining space.
     * Any calculation of containing panel's sizings will only be made if
     * this.previousHeight does not equal to the actual innerHeight of the
     * container.
     *
     */
    onResize : function()
    {
        Ext.ux.layout.flexAccord.Layout.superclass.onResize.call(this);

        var innerHeight = this.container.getInnerHeight();

        if (!this.rendered || this.previousHeight === innerHeight) {
            return;
        }

        this.previousHeight = innerHeight;

        var items = this.container.items;

        var resizables    = [];
        var notResizables = [];
        var panelsHeight  = 0;
        var innerHeight   = this.container.getInnerHeight();
        var item          = null;

        for (var i = 0, len = items.length; i < len; i++) {
            item = items.get(i);
            if (this.isResizable(item)) {
                resizables.push(item);
            } else {
                notResizables.push(item);
            }
            panelsHeight += item.height;
        }

        var remaining = innerHeight - panelsHeight;

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
     * Returns the height of the headers of a panel for calculating min/max sizes.
     * If the second parameter was set to "true", toolbars will be considered and
     * their height will be added to the regular header height.
     *
     * @param {Ext.Panel} panel The panel to retrieve the header's height for.
     * @param {Boolean} toolbars Whether to consider any containing toolbars.
     *
     * @return {Number}
     *
     * @private
     */
    getHeaderHeight : function(panel, toolbars)
    {
        return panel.header.getHeight()
        + (toolbars === true && panel.getBottomToolbar()
          ? panel.getBottomToolbar().getSize().height : 0)
        + (toolbars === true && panel.getTopToolbar()
           ? panel.getTopToolbar().getSize().height : 0);

    },

    /**
     * Actually applies the value found in the panel's "height" property visually
     * height the height of the panels and takes care of re-adjusting height
     * if there is remaining space left in the container, or if the overall height
     * of the panels exceeds the inner height of the container.
     *
     * @param {Array} exclude An array of panels that should not be used as the
     * firstSpillItem, i.e. a panel that should be used to fill out remaining space.
     * @param {Ext.Panel} animatePanel If specified and the panel gets expanded, the
     * animation effect for the expand action will be used for this panel
     *
     * @private
     */
    adjustHeight : function(exclude, animatePanel)
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

            if (item.height <= this.getHeaderHeight(item, true)+3) {
                this.collapse(item, true);
                item.height = this.getHeaderHeight(item);
            } else if (item.collapsed && !item._isDragged) {

                if (animatePanel == item) {
                    this.expand(item, undefined, true);
                } else {
                    this.expand(item);
                }

                item.height = Math.max(item.height, this.getHeaderHeight(item, true));
            }

            if (!firstSpillItem && this.isResizable(item)) {
                if (exclude.indexOf(item) == -1) {
                    firstSpillItem = item;
                }
            }

            item.setSize({height : item.height, width : width});

            panelHeights += item._isDragged ? item._oldHeight : item.height;
        }

        if (panelHeights < innerHeight && firstSpillItem) {
            firstSpillItem.height = firstSpillItem.getSize().height
                                    +(innerHeight-panelHeights);
            firstSpillItem.setHeight(firstSpillItem.height);
        } else if (panelHeights > innerHeight && firstSpillItem) {
            // if rem is smaller than hh, call this method recursively
            var rem = firstSpillItem.getSize().height-(panelHeights-innerHeight);
            var hh  = this.getHeaderHeight(firstSpillItem, true);
            firstSpillItem.height = Math.max(hh, rem);
            firstSpillItem.setHeight(firstSpillItem.height);
            if (rem <= hh) {
                this.adjustHeight(exclude.concat([firstSpillItem]));
            }
        }
    },

    /**
     * Override for parent's layout implementation to fire the "afterlayout"
     * event directly or - if there is currently a panel that needs to be
     * expanded - after the "expand" event of this panels fires.
     *
     * @param {Array} exclude gets passed to the "adjustHeight()" method
     * @param {Ext.Panel} panel if psupplied, this panel will be expanded in
     * the "adjustHeight()" method
     */
    layout : function(exclude, animatePanel) {
        var target = this.container.getLayoutTarget();
        this.onLayout(this.container, target);
        this.adjustHeight(exclude, animatePanel);

        if (animatePanel) {
            animatePanel.on('expand', function() {
                this.container.fireEvent('afterlayout', this.container, this);
            }, this, {single : true});
        } else {
            this.container.fireEvent('afterlayout', this.container, this);
        }

    },

    /**
     * Returns true if the item can be resized.
     * An item can be resized if its "resizable" property does not equal
     * to false, if its not collapsed and its height is greater then
     * its headerHeight (along with containing toolbars). Additionally,
     * this method may return "true" for items which are collapsed, if the
     * "ignoreCollapsed" property was set to true.
     *
     * @param {Ext.Panel} item
     * @param {Boolean} ignoreCollapse Whether to ignore collapsed state
     * @param {boolean} ignoreResizable Whether to ignore the panel's
     * resizable proeprty
     *
     * @return {boolean}
     */
    isResizable : function(item, ignoreCollapse, ignoreResizable)
    {
        if (!item.rendered || (ignoreResizable !== true && item.resizable === false)) {
            return false;
        }
        itemId     = item.getId();
        itemHeight = item.height;

        if (itemHeight <= this.getHeaderHeight(item, true) && ignoreCollapse !== true) {
            return false;
        }

        return true;
    },

    /**
     * Listener for the beforeexpand-event for any of the panels this container holds.
     * This listener will in any case return false to prevent the panels own "expand"
     * implementation, and instead delegates the whole process of expanding a panel
     * to this layout manager.
     *
     * @param {Ext.Panel} p The panel to expand
     * @param {Boolean} anim Whether to animate the "expand" process
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
                this.layout(items.items, (anim !== false ? p : undefined));
                return false;
            } else if (this._orgHeights[p.id]) {
                heightToSet = this._orgHeights[p.id];
            }
        }

        this.setItemHeight(p, heightToSet, (anim !== false));

        return false;
    },

    /**
     * Listener for the collapse event of the panels in the container
     * this layout is attached to. Will search for the first item that is resizable
     * so that the space freed by the collapsing panel can be added to this panel.
     *
     * @param {Ext.Panel} p The panel to collapse.
     * @param {Boolean} anim Whether to show an animation effect during the process
     * of collapsing.
     */
    onCollapse : function(p, anim)
    {
        var items        = this.container.items;
        var item         = null;
        var panelHeights = 0;
        var tmpItem      = null;

        for (var i = 0, len = items.length; i < len; i++) {
            tmpItem = items.get(i);

            // mark the first item that is resizable and which is a previous
            // node of the item collapsed
            if (!item && p != tmpItem && this.isResizable(tmpItem)) {
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
        this.layout();
    },

    /**
     * Sets the height for a specified item in this container. Resizing
     * the item may trigger resizing (collapsing, expanding) of other items
     * in this container.
     * After all panels have been processed, a call to "adjustHeight()" is made,
     * to ensure that the overall height of panels does not cause any spill or
     * leave remaining space.
     *
     * @param {Ext.Panel} resizedElement
     * @param {Number} newSize The new size for this item
     * @param {Boolean} animateIfExpand whether to animate the expand-effect if this
     * item gets expanded
     */
    setItemHeight : function(resizedElement, newSize, animateIfExpand)
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

        var len = items.items.length;

        var after          = [];
        var notResizables  = [];
        var allCollapsed   = true;
        for (var i = itemPos+1; i < len ; i++) {
            if (items.get(i).hidden) {
                continue;
            }
            if (!items.get(i).collapsed) {
                allCollapsed = false;
            }

            if (items.get(i).resizable === false) {
                notResizables.push(items.get(i));
            } else {
                after.push(items.get(i));
            }
        }

        if (!allCollapsed || resizedElement.resizable !== false || itemPos != 0) {

            if (items.get(itemPos+1) && items.get(itemPos+1).resizable === false) {
                notResizables.reverse();
            }

            after = after.concat(notResizables);

            for (var i = 0, len = after.length; i < len && spill != 0; i++) {
                spill = this.addSpill(after[i], spill);
            }
        }

        this.layout([], (animateIfExpand === true ? resizedElement : null));
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
        if (panel.resizable === false) {
            if (spill > 0) {
                tHeight = this._orgHeights[panel.id];
            } else {
                tHeight = this.getHeaderHeight(panel);
            }
        } else {
            tHeight = Math.max(this.getHeaderHeight(panel), panel.height + spill);
        }

        var retSpill = 0;

        panel.height = tHeight <= 0 ? this.getHeaderHeight(panel) : tHeight;
        var panelHeights = 0;
        var items = this.container.items;
        var item  = null;
        for (var i = 0, len = items.items.length; i < len; i++){
            item = items.get(i);
            if (item.hidden) {
                continue;
            }
            panelHeights += item.height;
        }

        return this.container.getInnerHeight()-panelHeights;
    }

});
