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
 * A layout manager that allows for adding collapsible panels which can be
 * resized via drag and drop. In most places, it behaves like an Accordionlayout.
 * In this kind of layout, all panels may be opened at once. You may specify
 * a height for the panel and if the panel is resizable. The layout manager will
 * then calculate the dimension before a panel expands so that each panel still
 * fits into the container to which this layout is attached. If a height for a
 * panel is specified (i.e. equals to a number > 0), then the layout manager will
 * try to keep this height for the panel if a resize of the container or an expand
 * of the panel itself happens. However, if a panel gets resized manually by the
 * user using the SplitBar, then the initial height will get set to the new value
 * of the drag operation.
 *
 *
 * @class com.conjoon.layout.ResizableAccordionLayout
 * @extends Ext.layout.ContainerLayout
 *
 * @author Thorsten Suckow-Homberg
 */
com.conjoon.layout.ResizableAccordionLayout = Ext.extend(Ext.layout.ContainerLayout, {

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

        com.conjoon.layout.ResizableAccordionLayout.superclass.renderItem.call(this, c, position, target);

        if (!this._orgHeights) {
            this._orgHeights = {};
        }

        if (c.height === undefined || c.height == 'auto') {
            c.height = this.defaultHeight;
        }

        if (c.resizable === false) {
            this._orgHeights[c.getId()] = c.height;
        }

        if (c.collapsed) {
            c.height = this.getHeaderHeight(c);//23;//.on('afterlayout', function(){c.height=this.getHeaderHeight(c);}, this, {single:true});
        }

        c.header.addClass('x-accordion-hd');

        // during drag and drop, the item might get re-rendered
        // detach listeners first, just to be sure
        c.un('beforeexpand',   this.beforeExpand, this);
        c.un('collapse',       this.onCollapse, this);
        c.on('beforeexpand',   this.beforeExpand, this);
        c.on('collapse',       this.onCollapse, this);

        var itemPos    = this.container.items.indexOf(c);
        var itemLength = this.container.items.length;

        // add the splitbar
        if (c.resizable !== false && itemPos != itemLength-1) {
            this.addSplitter(c);
        } else if (itemPos == itemLength-1) {
            this.deleteSplitter(c);

            // check if the previous panel needs a sploitbar, in case
            // the panel is re-rendered and drag/dropped at the end of the
            // container
            var previous = this.container.items.get(itemPos-1);
            if (previous && previous.resizable !== false) {
                this.addSplitter(previous);
            }
        }
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
     * Adds a com.conjoon.layout.AccordionSplitBar to the specified panel.
     */
    addSplitter : function(c)
    {
        this.deleteSplitter(c);

        c.splitEl = c.el.createChild({
            cls : "x-layout-split x-layout-split-south", html: "&#160;",
            id  : c.getId() + '-xsplit'
        });
        c.splitter = new com.conjoon.layout.AccordionSplitBar(
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


    collapse : function(p, suspend)
    {
        if (!p.rendered) {
            return;
        }
        if (suspend === true) {
            p.suspendEvents();
        }
        p.collapse(false);
        if (suspend === true) {
            p.resumeEvents();
        }
    },

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

    onLayout : function(ct, target)
    {
        com.conjoon.layout.ResizableAccordionLayout.superclass.onLayout.call(this, ct, target);

        if (!this.rendered) {
            this.fitPanels();
        }

        var width = target.getStyleSize().width;

        var items = ct.items.items;

        for (var i = 0, len = items.length; i < len; i++) {
            if (items[i].hidden) {
                continue;
            }
            if (items[i].height <= 26 && !items[i].collapsed) {
                this.collapse(items[i], true);
            } else {
                items[i].setSize({height : items[i].height, width : width});
            }

            if (items[i].splitEl) {
                items[i].splitEl.setWidth(items[i].el.getWidth());
            }

        }

        this.rendered   = true;
    },

    getHeaderHeight : function(panel)
    {
        return panel.getSize().height - panel.bwrap.getHeight();
    },

    adjustCol : function()
    {
        var items = this.container.items.items;

        var innerHeight   = this.container.getInnerHeight();
        var overallHeight = 0;
        var adjustCol     = null;

        for (var i = 0, len = items.length; i < len; i++) {

            if (this._isResizable(items[i])) {
                adjustCol = items[i];
            }

            overallHeight += items[i].getSize().height;
        }

        if (adjustCol && overallHeight < innerHeight) {
            adjustCol.height += (innerHeight-overallHeight);
            adjustCol.setHeight(adjustCol.height);
        }
    },

    fitPanels : function(considerPanel)
    {
        var innerHeight = this.container.getInnerHeight();

        var items = this.container.items.items;

        var overallHeight = 0;

        var panels = [];

        var expanded       = 0;
        var expandedHeight = 0;

        for (var i = 0, len = items.length; i < len; i++) {
            if (!items[i].hidden && this._isResizable(items[i])) {
                expandedHeight += items[i].height;
                expanded++;
            }
        }

        var oh = -1;

        for (var i = 0, len = items.length; i < len; i++) {

            if (considerPanel == items[i]) {
                oh = considerPanel.height;
                if (this._orgHeights[considerPanel.getId()]) {
                    items[i].height = this._orgHeights[considerPanel.getId()];
                } else {
                    items[i].height = Math.round(expandedHeight/Math.max(1, expanded));
                }

                overallHeight += (items[i].height);
            } else if (items[i].hidden) {
                overallHeight += 0;
            } else if (!items[i].collapsed) {
                overallHeight += (items[i].height);
            } else {
                overallHeight += this.getHeaderHeight(items[i]);
           }

            if (!items[i].hidden && (items[i] == considerPanel || this._isResizable(items[i]))) {
                panels.push(items[i]);
            }
        }

        var heightToSpare = innerHeight-overallHeight;

        if (panels.length == 0 || heightToSpare == 0) {
            return;
        }

        var rem = Math.round((heightToSpare)/(panels.length-
            (considerPanel && this._orgHeights[considerPanel.getId()] ? 1 : 0)
       ));

        while (panels.length) {
            var panel = panels.pop();

            panel.height =
                panel == considerPanel && this._orgHeights[considerPanel.getId()]
                ? panel.height
                : Math.max(panel.height+rem, this.getHeaderHeight(panel));

            if (considerPanel == panel) {
                if (oh == panel.height) {
                    panel.on('expand', this.adjustCol, this, {single : true});
                } else {
                    panel.on('afterlayout', this.adjustCol, this, {single : true});
                }
            }

        }




    },


    onResize : function()
    {
        if (!this.rendered) {
            return;
        }
        this.fitPanels();
        com.conjoon.layout.ResizableAccordionLayout.superclass.onResize.call(this);
        this.adjustCol();
    },


    /**
     * Returns true if the item can be resized.
     *
     * @param {Ext.Panel} item
     *
     * @return {boolean}
     */
    _isResizable : function(item)
    {
        if (!item.rendered || item.resizable === false) {
            return false;
        }
        itemId     = item.getId();
        itemHeight = item.getSize().height;

        if (itemHeight <= this.getHeaderHeight(item)) {
            return false;
        }

        return true;
    },

    /**
     * Called when a panel is about to get expanded.
     * This implementation will check if there is enough room to expand
     * the specified panel. If that is not the case, the method will try to
     * resize the other panels subsequently so that the other panel can expand
     * properly. Depending on the sizes of the container and the other panel,
     * the requested height of the panel (specified in the panels "height" property)
     * might not be reached.
     *
     * @param {Ext.Panel} p
     * @param {Boolean} anim
     */
    beforeExpand : function(p, anim)
    {
        this.fitPanels(p);
        this.layout();


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
        this.fitPanels();
        this.layout();
        this.adjustCol();

    }

});