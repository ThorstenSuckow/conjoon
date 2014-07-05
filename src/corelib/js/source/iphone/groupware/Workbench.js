/**
 * conjoon
 * (c) 2007-2014 conjoon.org
 * licensing@conjoon.org
 *
 * conjoon
 * Copyright (C) 2014 Thorsten Suckow-Homberg/conjoon.org
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
*
 * $Author$
 * $Id$
 * $Date$
 * $Revision$
 * $LastChangedDate$
 * $LastChangedBy$
 * $URL$
 */

Ext.namespace('com.conjoon.iphone.groupware');

/**
 * The default workbench for the conjoon project for iPhone devices.
 * The workbench contains in any case an instance of {com.conjoon.iphone.groupware.workbench.HomePanel},
 * which will list the available modules to choose from.
 *
 * This class serves also as an controller and manages displaying the chosen
 * modules.
 *
 * @class com.conjoon.iphone.groupware.Workbench
 * @extends Ext.Viewport
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
com.conjoon.iphone.groupware.Workbench = Ext.extend(Ext.Viewport, {

    /**
     * @type {com.conjoon.service.twitter.TwitterPanel} _twitterPanel
     */
    _twitterPanel : null,

    /**
     * @type {com.conjoon.iphone.groupware.workbench.HomePanel} _homePanel
     */
    _homePanel : null,

    /**
     * @type {Ext.Toolbar} _toolbar
     */
    _toolbar : null,

    /**
     * @type {Ext.Panel} _navPanel
     */
    _navPanel : null,

    /**
     * Inits this component.
     */
    initComponent : function()
    {
        var items      = this._getItems();
        var homePanel  = this.getHomePanel();

        for (var i = 0, len = items.length; i < len; i++) {
            items[i].hideMode = 'visibility';

            if (items[i] instanceof com.conjoon.iphone.groupware.workbench.HomePanel) {
                continue;
            }

            homePanel.addNavPanel(items[i]);
        }

        this._navPanel = new Ext.Panel({
            border     : false,
            activeItem : 0,
            layout     : new Ext.ux.layout.SlideLayout(),
            items      : this._getItems()
        });

        Ext.apply(this, {
            cls        : 'com-conjoon-iphone-groupware-workbench',
            layout     : 'fit',
            border     : false,
            items      : [
                this._navPanel
            ]
        });

        this.on('render', this.initEvents, this);

        com.conjoon.iphone.groupware.Workbench.superclass.initComponent.call(this);
    },

    /**
     * Maps listeners to the specific events.
     *
     * @protected
     */
    initEvents : function()
    {
        var homePanel = this.getHomePanel();
        homePanel.on('navclick', this._onNavClick, this);

        var twitterPanel = this.getTwitterPanel();
        twitterPanel.getChooseAccountButton().on(
            'exitclick',
            this._onExitClick,
            this
        );
    },

// -------- listeners

    /**
     * Listens to the "exitclick" event of the accountButton.
     * Will show the home panel if no account is currently choosen in the
     * twitterPanel, which should tell that the intro screen is shown.
     *
     * @param {com.conjoon.service.twitter.AccountButton} accountButton
     * @param {Ext.menu.Item} menuItem
     *
     * @protected
     */
    _onExitClick : function(accountButton, menuItem)
    {
        if (this.getTwitterPanel().getCurrentAccountId() <= 0) {
            this._navPanel.getLayout().setActiveItem(this.getHomePanel().getId());
        }
    },

    /**
     * Listens to the "navclick" event of the homePanel.
     * Requests the layout of the _navPanel to show the panel which is
     * connected to the event.
     *
     * @param {com.conjoon.iphone.groupware.workbench.HomePanel} homePanel
     * @param {Ext.Panel}
     *
     * @protected
     */
    _onNavClick : function(homePanel, panel)
    {
        this._navPanel.getLayout().setActiveItem(this.getTwitterPanel().getId());
    },

// -------- public API

    /**
     * Returns the twitterPanel used for the Twitter module.
     *
     * @return {com.conjoon.groupware.service.TwitterPanel}
     */
    getTwitterPanel : function()
    {
        if (!this._twitterPanel) {
            this._twitterPanel = this._getTwitterPanel();
        }

        return this._twitterPanel;
    },

    /**
     * Returns the homePanel used to list the available modules.
     *
     * @return {com.conjoon.iphone.groupware.workbench.HomePanel}
     */
    getHomePanel : function()
    {
        if (!this._homePanel) {
            this._homePanel = this._getHomePanel();
        }

        return this._homePanel;
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
            this.getHomePanel(),
            this.getTwitterPanel()
        ];
    },

    /**
     * Overwrite this to return a custom TwitterPanel
     *
     * @return {com.conjoon.groupware.service.TwitterPanel}
     *
     * @protected
     */
    _getTwitterPanel : function()
    {
        return new com.conjoon.iphone.groupware.service.TwitterPanel({
            appTitle   : 'Twitter',
            appIconCls : 'icon-twitter'
        });
    },

    /**
     * Overwrite this to return a custom HomePanel
     *
     * @return {com.conjoon.iphone.groupware.workbench.HomePanel}
     *
     * @protected
     */
    _getHomePanel : function()
    {
        return new com.conjoon.iphone.groupware.workbench.HomePanel();
    }

});