/**
 * Ext.ux.grid.GridViewMenuPlugin
 * Copyright (c) 2008-2014, http://www.siteartwork.de
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
 * @class Ext.ux.grid.GridViewMenuPlugin
 * @extends Object
 * @constructor
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
Ext.ux.grid.GridViewMenuPlugin = Ext.extend(Object, {

    /**
     * The {Ext.grid.GridPanel} this plugin is bound to.
     * @type {Ext.grid.GridPanel}
     * @protected
     */
    _grid : null,

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
     * If the bound view is an instance of {Ext.grid.GroupingView}, this
     * property will hold the last column that was used as the group field.
     * @type {String}
     */
    _lastGroupField : null,

    /**
     * If the bound view is an instance of {Ext.grid.GroupingView}, this
     * menu will hold all fields to switch the grouping field.
     * @type {Ext.menu.Menu}
     */
    _groupMenu : null,

    /**
     * An array with all menu items that should not be removed from the
     * colMenu.
     */
    _keepItems : null,

    /**
     * Inits this plugin.
     * Method is API-only. Will be called automatically from the grid this
     * plugin is bound to.
     *
     * @param {Ext.grid.GridPanel} grid
     */
    init : function(grid)
    {
        if (grid.enableHdMenu === false) {
            return;
        }

        this._keepItems = [];

        this._grid = grid;

        grid.enableHdMenu = false;

        this._view = grid.getView();

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

        this.colMenu = new Ext.menu.Menu({
            id           : grid.id + "-hcols-menu",
            subMenuAlign : "tr-tl?"
        });
        this.colMenu.on("afterrender",  this._beforeColMenuShow, this);
        this.colMenu.on("beforeshow",   this._beforeColMenuShow, this);
        this.colMenu.on("beforeremove", this._beforeColMenuRemove, this);
        this.colMenu.on("itemclick",    this._handleHdMenuClick, this);
    },

// -------- listeners

    /**
     * Called before any item gets removed from the colMenu. Will return
     * false for any item that was registered in keepItems.
     */
    _beforeColMenuRemove : function(menu, item)
    {
        if (this._keepItems.indexOf(item) != -1) {
            return false;
        }
    },

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
        return this._view.handleHdMenuClickDefault(item, e);
    },

    /**
     * Listener for the beforeshow/afterrender-event of the menu.
     * Default implementation calls the view's beforeColMenuShow-method
     * in the scope of this plugin. If the bound view is an instance of
     * {Ext.grid.GroupingView}, additionally items will be rendered to
     * control the grouping state of the grid.
     *
     * Overwrite this for custom behavior.
     *
     * @param {Ext.menu.Menu} menu
     *
     * @protected
     */
    _beforeColMenuShow : function(menu)
    {
        this.colMenu.suspendEvents();
        for (var i = 0, len = this._keepItems.length; i < len; i++) {
            this.colMenu.remove(this._keepItems[i], false);
        }
        this.colMenu.resumeEvents();

        this._view.beforeColMenuShow.call(this, menu);

        if(this._view.enableGroupingMenu && this.colMenu){

            if (!this._groupMenu) {
                this._groupMenu = new Ext.menu.Menu({
                    id : this._grid.id + "-hgroupcols-menu"
                });
                this._groupMenu.on("afterrender", this._onBeforeGroupMenuShow, this);
                this._groupMenu.on("beforeshow",  this._onBeforeGroupMenuShow, this);
                this._groupMenu.on("itemclick",   this._onGroupMenuItemClick,  this);

                var conf = {
                    itemId       : 'showGroups',
                    text         : this._view.showGroupsText,
                    menu         : this._groupMenu
                };

                if(this._view.enableNoGroups) {
                    var field = this._view.getGroupField();

                    Ext.apply(conf, {
                        checked      : !!field,
                        checkHandler : function(menuItem, checked) {
                            if(checked){
                                this._view.enableGrouping = true;
                                this._grid.store.groupBy(this._lastGroupField);
                                this._grid.fireEvent(
                                    'groupchange', this._grid, this._grid.store.getGroupState()
                                );
                            }else{
                                this._view.enableGrouping = false;
                                this._grid.store.clearGrouping();
                                this._grid.fireEvent('groupchange', this._grid, null);
                            }
                        },
                        scope : this
                    });
                }

                var sep = new Ext.menu.Separator();
                var gI  = this._view.enableNoGroups
                    ? new Ext.menu.CheckItem(conf)
                    : new Ext.menu.Item(conf);

                this._keepItems.unshift(sep, gI);
            }
        }

        for (var i = 0, len = this._keepItems.length; i < len; i++) {
            this.colMenu.add(this._keepItems[i]);
        }

        if(this._view.enableNoGroups) {
            this._keepItems[1].setChecked(!!this._view.getGroupField(), true);
        }

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
            this.colMenu.show(t, 'tr-br?');
        }
    },

    /**
     * Listener for the afterrender/beforeshow event of this plugin's group menu.
     *
     */
    _onBeforeGroupMenuShow : function()
    {
        var cm       = this._view.cm,
            colCount = cm.getColumnCount(),
            field    = this._view.getGroupField();

        this._groupMenu.removeAll();
        for(var i = 0; i < colCount; i++){
            this._groupMenu.add(new Ext.menu.CheckItem({
                itemId      : "groupcol-"+cm.getColumnId(i),
                text        : cm.getColumnHeader(i),
                checked     : cm.getDataIndex(i) == field,
                hideOnClick : true,
                disabled    : cm.config[i].groupable === false
            }));
        }
    },

    /**
     * Listener for the itemclick event of this plugin's group menu.
     *
     */
    _onGroupMenuItemClick : function(item)
    {
        var cm     = this._view.cm,
            index  = cm.getIndexById(item.itemId.substr(9)),
            dIndex = cm.getDataIndex(index);

        if(index != -1){
            if(item.checked){
                this._view.enableGrouping = false;
                this._grid.store.clearGrouping();
                this._grid.fireEvent('groupchange', this._grid, null);
            } else {
                this._view.enableGrouping = true;
                this._grid.store.groupBy(dIndex);
                this._lastGroupField = this._view.getGroupField();
                this._grid.fireEvent(
                    'groupchange', this._grid, this._grid.store.getGroupState()
                );

            }
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
     * Sequenced function for storing the view's cm property and
     * to initially set the last grouped field if the bound view is an
     * instace of Ext.grid.GroupingView.
     * Called in the scope of this plugin.
     */
    initData : function()
    {
        this.cm = this._view.cm;

        if (this._view.enableGroupingMenu) {
            this._lastGroupField = this._view.getGroupField();
        }
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
            this.colMenu.un("beforeremove", this._beforeColMenuRemove, this);
            this.colMenu.removeAll(true);
            Ext.menu.MenuMgr.unregister(this.colMenu);
            if (this.colMenu.getEl()) {
                this.colMenu.getEl().remove();
            }
            delete this.colMenu;
        }

        if(this._groupMenu){
            this._groupMenu.removeAll(true);
            Ext.menu.MenuMgr.unregister(this._groupMenu);
            delete this._groupMenu;
        }

        if(this._menuBtn){
            this._menuBtn.remove();
            delete this._menuBtn;
        }
    }

});
