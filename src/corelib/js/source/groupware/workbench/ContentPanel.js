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

Ext.namespace('com.conjoon.groupware.workbench');

/**
 *
 * @class com.conjoon.groupware.worbench.ContentPanel
 */
com.conjoon.groupware.workbench.ContentPanel = Ext.extend(Ext.TabPanel, {

    initComponent : function()
    {
        Ext.apply(this, {
            id              : 'DOM:com.conjoon.groupware.ContentPanel',
            region          : 'center',
            activeTab       : 0,
            resizeTabs      : true,
            minTabWidth     : 115,
            tabWidth        : 115,
            enableTabScroll : true,
            hideMode        : 'offsets',
            margins         : '0 0 0 0',
            border          : false,
            baseCls         : 'com-conjoon-groupware-TabHeader',
            items           : this._getItems()
        });

        com.conjoon.util.Registry.register('com.conjoon.groupware.ContentPanel', this);
        com.conjoon.groupware.workbench.ContentPanel.superclass.initComponent.call(this);
    },

    _getItems : function()
    {
        return [
            new com.conjoon.groupware.home.HomePanel(),
            new com.conjoon.groupware.email.EmailPanel()
        ];
    },

    initEvents : function()
    {
        com.conjoon.groupware.workbench.ContentPanel.superclass.initEvents.call(this);

        this.on('add',    this._onAdd,    this);
    },

    doLayout : function()
    {
        com.conjoon.groupware.workbench.ContentPanel.superclass.doLayout.call(this);

        var td = document.getElementById('DOM:com.conjoon.groupware.Toolbar.controls');
        if (!td) {
            return;
        }
        td.style.width = document.body.offsetWidth-250+"px";
        this.header.setWidth(document.body.offsetWidth-50)
        this.header.dom.nextSibling.style.width = (document.body.offsetWidth-50)+"px";
        this.el.dom.firstChild.style.marginLeft= "50px";
        this.delegateUpdates();
    },

    _onAdd : function(container, component, index)
    {
        var id  = component.id;
        var add = true;

        switch (id) {
            case 'DOM:com.conjoon.groupware.HomePanel':
            case 'DOM:com.conjoon.groupware.EmailPanel':
            case 'DOM:com.conjoon.groupware.ContactsPanel':
            case 'DOM:com.conjoon.groupware.TodoPanel':
            case 'DOM:com.conjoon.groupware.CalendarPanel':
                add = false;
            break;
        }

        var title   = component.title;
        var iconCls = component.iconCls;

        if (add && title) {
            var menu = com.conjoon.groupware.workbench.BookmarkController.getButton().menu;

            var item = menu.add({iconCls : iconCls, text : title});
            item.on('click', function() {
                this.setActiveTab(component);
            }, this)
            component.on('titlechange', function(){
                item.setText(this.title);
            }, component);
            component.on('destroy', function() {
                menu.remove(item);
            });
        }
    }

});