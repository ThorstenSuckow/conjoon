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

Ext.namespace('com.conjoon.groupware.workbench.dd');

/**
 *
 *
 *
 */
com.conjoon.groupware.workbench.dd.TabDragZone = function(tabPanel, hd, notDraggable){
    this.tabPanel = tabPanel;
    this.ddGroup  = "workbench";
    com.conjoon.groupware.workbench.dd.TabDragZone.superclass.constructor.call(this, hd);
    this.scroll = false;
    this.notDraggable = notDraggable || [];
};

Ext.extend(com.conjoon.groupware.workbench.dd.TabDragZone, Ext.dd.DragZone, {

    /**
     * @type {Ext.TabPanel} tabPanel The TabPanel to which this DragZone is attached
     * to
     */
    tabPanel : null,

    /**
     * @type {HTMLElement} proxyTpl A template that can be reused for this proxy.
     */
    proxyTpl : null,

// -------- helpers

    /**
     * Listener for the tab's iconchange event. This is needed
     * since a dragged panel might have the icon "loading" due to being
     * busy loading its contents.
     *
     * @param {Ext.Panel} tab The tab which icon gets changed
     * @param {String} iconCls The new iconCls for this tab
     */
    onTabIconChange : function(tab, iconCls)
    {
        this.setProxyTplData(tab.title, iconCls);

    },

    /**
     * Listener for the tab's titlechange event. This is needed
     * since a dragged panel might have the title "loading" due to being
     * busy loading its contents.
     *
     * @param {Ext.Panel} tab The tab which title gets changed
     * @param {String} title The new title for this tab
     */
    onTabTitleChange : function(tab, title)
    {
        this.setProxyTplData(title, tab.iconCls);
    },

    /**
     * Gets the {Ext.Panel} represented by the passed DOM element which points
     * to the clickable tab element.
     *
     * @param {HTMLElement} tabEl
     *
     * @return {Object} an object with the following properties:
     * - {Ext.Panel} panel: The panel represented by the tabEl
     * - {Number} pos: The position of the panel within the TabPanel
     */
    getPanelInfoForTabEl : function(tabEl)
    {
        var items = this.tabPanel.items;
        var item  = null;

        for (var i = 0, len = items.length; i < len; i++) {
            var item = items.get(i);
            if (this.tabPanel.getTabEl(item) == tabEl) {
                return {
                    panel : item,
                    pos   : items.indexOf(item)
                }
            }
        }

        return null;
    },

    /**
     * Updates the icon cls and the title for the proxyTpl.
     *
     */
    setProxyTplData : function(title, iconCls)
    {
        title   = title ? title : '&#160;'
        iconCls = iconCls ? iconCls : '';

        var cont = this.dragData.proxyEl.firstChild;

        cont.className = iconCls;
        cont.innerHTML = title;
    },

    /**
     * Creates a template for the proxy that can be reused for
     * drag operations.
     *
     */
    getProxyTpl : function()
    {
        if (!this.proxyTpl) {
            this.proxyTpl = document.createElement('div');
            this.proxyTpl.className = 'com-conjoon-groupware-workbench-tabProxy';
            var span = document.createElement('span');
            this.proxyTpl.appendChild(span);
        }

        return this.proxyTpl;
    },


// -------- Ext.dd.DragZone

    handleMouseDown : Ext.emptyFn,

    callHandleMouseDown : function(e)
    {
        com.conjoon.groupware.workbench.dd.TabDragZone.superclass.handleMouseDown.call(this, e);
    },

    /**
     * Returns the data object associated with this drag source
     * @return {Object} data An object containing arbitrary data
     */
    getDragData : function(e)
    {
        var t = Ext.lib.Event.getTarget(e);
        var h = Ext.fly(t).findParent('li', 5);

        if (h) {
            var info = this.getPanelInfoForTabEl(h);
            if (!info || this.notDraggable.indexOf(info.panel) != -1) {
                return false;
            }
            var proxyTpl = this.getProxyTpl().cloneNode(true);

            return {
                ddel    : h,
                proxyEl : proxyTpl,
                tab     : h,
                panel   : info.panel,
                pos     : info.pos
            };
        }
        return false;
    },

    /**
     * Visually updates the proxy with informations about the tab to drag.
     * It does also add listeners for the iconchange/titlechange event for the
     * tab.
     *
     * @return {Boolean}
     */
    onInitDrag : function(e)
    {
        this.dragData.panel.on('iconchange',  this.onTabIconChange,  this);
        this.dragData.panel.on('titlechange', this.onTabTitleChange, this);

        var tpl = this.dragData.proxyEl;
        tpl.id  = Ext.id();

        tpl.style.width = this.dragData.tab.offsetWidth + "px";
        this.proxy.update(tpl);
        this.setProxyTplData(this.dragData.panel.title, this.dragData.panel.iconCls);

        return true;
    },

    /**
     * Action performed after a valid drop has occurred.
     * Removes any listeners from the tab to be dragged added by this DragZone.
     *
     * @param {Object} target The target DD
     * @param {Event} e The event object
     * @param {String} id The id of the dropped element
     */
    afterValidDrop : function(target, e, id)
    {
        this.dragData.panel.un('iconchange',  this.onTabIconChange,  this);
        this.dragData.panel.un('titlechange', this.onTabTitleChange, this);
    },

    /**
     * Action performed after an invalid drop has occurred.
     * Removes any listeners from the tab to be dragged added by this DragZone.
     *
     * @param {Object} target The target DD
     * @param {Event} e The event object
     * @param {String} id The id of the dropped element
     */
    afterInvalidDrop : function(target, e, id)
    {
        this.dragData.panel.un('iconchange',  this.onTabIconChange,  this);
        this.dragData.panel.un('titlechange', this.onTabTitleChange, this);
    }

});