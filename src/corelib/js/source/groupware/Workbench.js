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

Ext.namespace('com.conjoon.groupware');

/**
 * The default workbench for the conjoon project. Will render the workbench with
 * its needed containers. The default layout is as follows:
 *
 * @class com.conjoon.groupware.Workbench
 */
com.conjoon.groupware.Workbench = Ext.extend(Ext.Viewport, {

    /**
     * @type {Ext.BoxComponent} _southPanel
     */
    _southPanel : null,

    /**
     * @type {Ext.BoxComponent} _eastPanel
     */
    _eastPanel : null,

    /**
     * @type {Ext.BoxComponent} _centerPanel
     */
    _centerPanel : null,

    /**
     * @type {Ext.BoxComponent} _northPanel
     */
    _northPanel : null,

    /**
     * @type {Ext.BoxComponent} _westPanel
     */
    _westPanel : null,

    /**
     * @type {HtmlElement} _dropTargetWest
     */
    _dropTargetWest : null,

    /**
     * Inits this component.
     */
    initComponent : function()
    {
       Ext.apply(this, {
            layout : 'border',
            border : false,
            items  : this._getItems()
        });

        this.getCenterPanel().on('resize', this.doLayout, this);

        com.conjoon.groupware.Workbench.superclass.initComponent.call(this);
    },



// -------- public API

    /**
     * Returns the northpanel for the workbench.
     *
     * @return {Ext.Panel}
     */
    getNorthPanel : function()
    {
        if (!this._northPanel) {
            this._northPanel = this._getNorthPanel();
        }

        return this._northPanel;
    },

    /**
     * Returns the southpanel for the workbench.
     *
     * @return {Ext.Panel}
     */
    getSouthPanel : function()
    {
        if (!this._southPanel) {
            this._southPanel = this._getSouthPanel();
        }

        return this._southPanel;
    },

    /**
     * Returns the eastpanel for the workbench.
     *
     * @return {Ext.Panel}
     */
    getEastPanel : function()
    {
        if (!this._eastPanel) {
            this._eastPanel = this._getEastPanel();
        }

        return this._eastPanel;
    },

    /**
     * Returns the westpanel for the workbench.
     *
     * @return {Ext.Panel}
     */
    getWestPanel : function()
    {
        if (!this._westPanel) {
            this._westPanel = this._getWestPanel();
        }

        return this._westPanel;
    },

    /**
     * Returns the centerpanel for the workbench.
     *
     * @return {Ext.Panel}
     */
    getCenterPanel : function()
    {
        if (!this._centerPanel) {
            this._centerPanel = this._getCenterPanel();
        }

        return this._centerPanel;
    },

// -------- builders

    /**
     * Overwrite this to return a custom list of elements to
     * add to this component's items.
     *
     * @return {Array}
     *
     * @protected
     */
    _getItems : function()
    {
        return [
            this.getNorthPanel(),
            this.getSouthPanel(),
            this.getCenterPanel(),
            this.getEastPanel(),
            this.getWestPanel(),
        ];
    },

    /**
     *
     * @return {Ext.Panel}
     *
     * @protected
     */
    _getNorthPanel  : function()
    {
        return  new Ext.Panel({
            region  : 'north',
            height  : 21,
            baseCls : 'com-conjoon-groupware-Header',
            items   : [
                com.conjoon.groupware.workbench.Menubar.getInstance(this),
                com.conjoon.groupware.workbench.ToolbarController.getContainer(),
                com.conjoon.groupware.workbench.BookmarkController.getContainer()
            ]
        });

    },

    /**
     *´
     * @return {Ext.Panel}
     *
     * @protected
     */
    _getSouthPanel  : function()
    {
        /**
         * @bug Ext2.2 We need to wrap the statusbar in another panel, otherwise
         * IE7 will have problems calculating the size properly
         */
        return {
            region   : 'south',
            html     : '<div style=\'display:none\'></div>',
            bbar     : com.conjoon.groupware.StatusBar.getStatusBar(),
            border   : false,
            margins  : '3 0 0 0'
        };
    },

    /**
     *
     * @return {Ext.Panel}
     *
     * @protected
     */
    _getCenterPanel : function()
    {
        return new com.conjoon.groupware.workbench.ContentPanel();
    },

    /**
     *
     * @return {Ext.Panel}
     *
     * @protected
     */
    _getWestPanel : function()
    {
        return new com.conjoon.dd.AccordionDropPanel({
            region       : 'west',
            layoutConfig : {
                animate : true,
            },
            split        : true,
            minSize      : 0,
            margins      : '64 0 0 0',
            width        : 225,
            hidden       : true,
            maxSize      : 600,
            border       : false,
            cls          : 'com-conjoon-groupware-QuickPanel-itemPanel',
            listeners    : {
                show  : this._onQuickPanelVisibilityChange,
                hide  : this._onQuickPanelVisibilityChange,
                scope : this
            }
        });
    },

    _onQuickPanelVisibilityChange : function(panel)
    {
        var region = null;

        if (panel == this.getWestPanel()) {
            region = 'west';
        } else {
            region = 'east';
        }

        var borderRegion = this.getLayout()['center'];
        if (!borderRegion) {
            return;
        }

        if (region == 'west') {
            borderRegion.margins.left = panel.hidden ? 4 : 0;
        } else {
            borderRegion.margins.right =  panel.hidden ? 4 : 0;
        }

        // check if the panel is already rendered... just a hack
        // otherwise doLayout will cause a few errors in other components
        if (panel.items) {
            this.doLayout();
        }
    },


    /**
     *
     * @return {Ext.Panel}
     *
     * @protected
     */
    _getEastPanel   : function()
    {
        var quickPanel = com.conjoon.groupware.QuickEditPanel.getComponent({
            draggable : com.conjoon.groupware.workbench.PanelDragSource.getConfig()
        });

        var feedGrid = new com.conjoon.groupware.feeds.FeedGrid({
            draggable : com.conjoon.groupware.workbench.PanelDragSource.getConfig()
        });

        var emailsPanel = new com.conjoon.groupware.email.LatestEmailsPanel({
            collapsed : true,
            draggable : com.conjoon.groupware.workbench.PanelDragSource.getConfig()
        });

        var twitterPanel = new com.conjoon.groupware.service.TwitterPanel({
            draggable : com.conjoon.groupware.workbench.PanelDragSource.getConfig(),
            itemTitle : com.conjoon.Gettext.gettext("Twitter")
        });

        quickPanel.on('show', this._onInfoPanelVisibilityChange, this);
        quickPanel.on('hide', this._onInfoPanelVisibilityChange, this);

        feedGrid.on('show', this._onInfoPanelVisibilityChange, this);
        feedGrid.on('hide', this._onInfoPanelVisibilityChange, this);

        emailsPanel.on('show', this._onInfoPanelVisibilityChange, this);
        emailsPanel.on('hide', this._onInfoPanelVisibilityChange, this);

        twitterPanel.on('show', this._onInfoPanelVisibilityChange, this);
        twitterPanel.on('hide', this._onInfoPanelVisibilityChange, this);

        return new com.conjoon.dd.AccordionDropPanel({
            layoutConfig : {
                animate : true
            },
            region       : 'east',
            split        : true,
            width        : 225,
            minSize      : 0,
            maxSize      : 600,
            border       : false,
            margins      : '64 0 0 0',
            cls          : 'com-conjoon-groupware-QuickPanel-itemPanel',
            items : [
                quickPanel,
                emailsPanel,
                feedGrid,
                twitterPanel
            ],
            listeners : {
                show  : this._onQuickPanelVisibilityChange,
                hide  : this._onQuickPanelVisibilityChange,
                scope : this
            }
        });


    },

    /**
     * Returns if the layout process was inited again, otherwise false.
     *
     */
    checkIfCollapsible : function(panel)
    {
        var doLayout      = false;
        var collapsePanel = false;

        if (panel.items.length == 0) {
            collapsePanel = true;
        } else {
            collapsePanel = true;
            for (var i = 0, len = panel.items.length; i < len; i++) {
                if (!panel.items.get(i).hidden) {
                    collapsePanel = false;
                    break;
                }
            }
        }

        if (collapsePanel) {
            panel.hide();
            this.doLayout();
        }
        return collapsePanel;
    },

    _onInfoPanelVisibilityChange : function(panel)
    {
        var toShow = !panel.hidden;
        var owner  = panel.ownerCt;

        if (!this.checkIfCollapsible(owner)) {
            owner.getLayout().fitPanels();
            owner.doLayout();
        }
    },

    _resizeHeaders : function(wWidth)
    {
        // right toolbar controls
        var td = document.getElementById('DOM:com.conjoon.groupware.Toolbar.controls');
        if (td) {
            td.style.width = document.body.offsetWidth-250+"px";
        }

        if (wWidth === undefined) {
            wWidth = this.getWestPanel().el.getWidth();
        }

        this.getCenterPanel().header.dom.style.position="relative";
        this.getCenterPanel().header.dom.style.left = -wWidth+50+"px";
        this.getCenterPanel().header.dom.style.width = document.body.offsetWidth-52+"px";

        this.getCenterPanel().delegateUpdates();
    },

    doLayout : function()
    {
        com.conjoon.groupware.Workbench.superclass.doLayout.call(this);

        this._resizeHeaders();
    }

});


