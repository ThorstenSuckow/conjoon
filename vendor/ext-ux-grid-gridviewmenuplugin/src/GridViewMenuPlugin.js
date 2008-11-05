/**
 * Ext.ux.grid.GridViewMenuPlugin
 * Copyright (c) 2008, http://www.siteartwork.de
 *
 * Ext.ux.grid.GridViewMenuPlugin is licensed under the terms of the
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

Ext.namespace('Ext.ux.grid');

/**
 * Renders a menu button to the upper right corner of the grid this plugin is
 * bound to. The menu items will represent the column model and hide/show
 * the columns on click.
 *
 * Note that you have to set the enableHdMenu-property of the bound grid to
 * "false" so this plugin does not interfere with the header menus of the grid's view.
 *
 * @class Ext.ux.grid.GridViewMenuPlugin
 * @extends Object
 * @constructor
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
Ext.ux.grid.GridViewMenuPlugin = Ext.extend(Object, {

    /**
     * The {Ext.grid.GridView} this plugin is bound to.
     * @type {Ext.grid.GridView}
     * @protected
     */
    _view : null,

    /**
     * The menu button that gets rendered to the upper right corner of the
     * grid's view.
     * @type {Ext.Element}
     * @protected
     */
    _menuBtn : null,

    /**
     * The menu that will be shown when the menu button gets clicked.
     * Named after the "colModel" property of the grid's view so this plugin
     * can easily operate in the scope of the view.
     * @type {Ext.Menu}
     */
    colMenu : null,

    /**
     * The column model of the grid this plugin is bound to.
     * Named after the "cm" property of the grid's view so this plugin
     * can easily operate in the scope of the view.
     * @type {Ext.grid.ColumnModel}
     */
    cm : null,

    /**
     * Inits this plugin.
     * Method is API-only. Will be called automatically from the grid this
     * plugin is bound to.
     *
     * @param {Ext.grid.GridPanel} grid
     *
     * @throws {Exception} throws an exception if the plugin recognizes the
     * grid's "enableHdMenu" property to be set to "true"
     */
    init : function(grid)
    {
        if (grid.enableHdMenu === true) {
            throw("Ext.ux.grid.GridViewMenuPlugin - grid\"s \"enableHdMenu\" property has to be set to \"false\"");
        }

        this._view = grid.view;

        this._view.initElements = this._view.initElements.createSequence(
            this.initElements,
            this
        );

        this._view.initData = this._view.initData.createSequence(
            this.initData,
            this
        );

        this._view.destroy = this._view.destroy.createInterceptor(
            this._destroy,
            this
        );

        this.colMenu = new Ext.menu.Menu();
        this.colMenu.on("beforeshow", this._beforeColMenuShow, this);
        this.colMenu.on("itemclick",  this._handleHdMenuClick, this);
    },

// -------- listeners

    /**
     * Callback for the itemclick event of the menu.
     * Default implementation calls the view's handleHdMenuClick-method in the
     * scope of the view itself.
     *
     * @param {Ext.menu.BaseItem baseItem} item
     * @param {Ext.EventObject} e
     *
     * @return {Boolean} returns false if hiding the column represented by the
     * column is not allowed, otherwise true
     *
     * @protected
     */
    _handleHdMenuClick : function(item, e)
    {
        return this._view.handleHdMenuClick(item, e);
    },

    /**
     * Listener for the beforeshow-event of the menu.
     * Default implementation calls the view's beforeColMenuShow-method
     * in the scope of this plugin.
     *
     * Overwrite this for custom behavior.
     *
     * @param {Ext.menu.Menu} menu
     *
     * @protected
     */
    _beforeColMenuShow : function(menu)
    {
        this._view.beforeColMenuShow.call(this, menu);
    },

    /**
     * Listener for the click event of the menuBtn element.
     * Used internally to show the menu.
     *
     * @param {Ext.EventObject} e
     * @param {HtmlElement} t
     *
     * @protected
     */
    _handleHdDown : function(e, t)
    {
        if(Ext.fly(t).hasClass('x-grid3-hd-btn')){
            e.stopEvent();
            this.colMenu.show(t, "tr-br?");
        }
    },

// -------- helpers

    /**
     * Builds the element that gets added to teh grid's header for showing
     * the menu.
     * The default implementation will render the menu button into the upper
     * right corner of the grid.
     * Overwrite for custom behavior.
     *
     * @return {Ext.Element}
     *
     * @protected
     */
    _getMenuButton : function()
    {
        var a = document.createElement('a');
        a.className = 'ext-ux-grid-gridviewmenuplugin-menuBtn x-grid3-hd-btn';
        a.href = '#';

        return new Ext.Element(a);
    },

    /**
     * Sequenced function for storing the view's cm property,
     * Called in the scope of this plugin.
     */
    initData : function()
    {
        this.cm = this._view.cm;
    },

    /**
     * Sequenced function for adding the menuBtn to the grid's header.
     * Called in the scope of this plugin.
     */
    initElements : function()
    {
        this.menuBtn = this._getMenuButton();
        this._view.mainHd.dom.appendChild(this.menuBtn.dom);
        this.menuBtn.on("click", this._handleHdDown, this);

        this.menuBtn.dom.style.height = (this._view.mainHd.dom.offsetHeight-1)+'px';
    },

    /**
     * Hooks into the view's destroy method and removes the menu and the menu
     * button.
     *
     * @protected
     */
    _destroy : function()
    {
        if(this.colMenu){
            this.colMenu.removeAll();
            Ext.menu.MenuMgr.unregister(this.colMenu);
            this.colMenu.getEl().remove();
            delete this.colMenu;
        }

        if(this._menuBtn){
            this._menuBtn.remove();
            delete this._menuBtn;
        }
    }

});