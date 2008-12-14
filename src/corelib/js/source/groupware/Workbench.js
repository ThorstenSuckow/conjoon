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
     * Inits this component.
     */
    initComponent : function()
    {
       Ext.apply(this, {
            layout : 'border',
            border : false,
            items  : this._getItems()
        });

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
            this.getEastPanel()
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
                com.conjoon.groupware.workbench.Menubar.getInstance(),
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
            border  : false,
            margins : '3 0 0 0'
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
    _getEastPanel   : function()
    {
        return new Ext.Panel({
            region       : 'east',
            collapsible  : true,
            split        : true,
            collapsed    : false,
            collapseMode : 'mini',
            width        : 225,
            minSize      : 75,
            maxSize      : 600,
            border       : true,
            margins      : '64 4 0 0',
            layout       : 'border',
            items        : [{
                region      : 'north',
                title       : com.conjoon.Gettext.gettext("Quickpanel"),
                height      : 205,
                cls         : 'com-conjoon-groupware-QuickPanel-editPanel',
                collapsible : true,
                layout      : 'fit',
                iconCls     : 'com-conjoon-groupware-quickpanel-NewIcon',
                border      : false,
                autoLoad    : false,
                items       : com.conjoon.groupware.QuickEditPanel.getComponent()
            },{
                region       : 'center',
                border       : false,
                layout       : 'accordion',
                cls          : 'com-conjoon-groupware-QuickPanel-itemPanel',
                layoutConfig : {
                    animate:true
                },
                items : [
                    new com.conjoon.groupware.email.LatestEmailsPanel({
                        collapsed:true
                    }),
                    new com.conjoon.groupware.feeds.FeedGrid()
                ]
            }]
        });
    }



});


