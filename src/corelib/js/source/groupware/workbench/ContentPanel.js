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

    /**
     * @type {com.conjoon.groupware.email.EmailPanel} _emailPanel
     */
    _emailPanel : null,

    /**
     * @type {Array} _removeList A list of ids of components which have to be confirmed
     * to get removed before tehy actually are
     */
    _removeList : null,

    /**
     * Inits this component.
     *
     */
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

        // we need to put the listener init here, since initEvents gets called
        // after the initial configuration for the items has been finished
        this.on('add', this._onAdd, this);

        com.conjoon.util.Registry.register('com.conjoon.groupware.ContentPanel', this);
        com.conjoon.groupware.workbench.ContentPanel.superclass.initComponent.call(this);
    },

    initEvents : function()
    {
        com.conjoon.groupware.workbench.ContentPanel.superclass.initEvents.call(this);

        this.on('resize', this.doLayout, this);
        this.on('beforeremove', this._onBeforeRemove, this);
    },

    /**
     *
     * @return {com.conjoon.groupware.email.EmailPanel}
     */
    getEmailPanel : function()
    {
        if (!this._emailPanel) {
            this._emailPanel = this._getEmailPanel();
        }

        return this._emailPanel;
    },

    /**
     * Shows a confirm message before the specified panel should get removed from
     * this container.
     *
     * @param {Ext.Panel} panelToRemove
     *
     * @return {Boolean} false This method always returns false
     */
    _confirmCloseTab : function(panelToRemove)
    {
        com.conjoon.SystemMessageManager.confirm(
            new com.conjoon.SystemMessage({
                title : com.conjoon.Gettext.gettext("Close tab?"),
                text  : String.format(
                    com.conjoon.Gettext.gettext("Are you sure you want to close the \"{0}\" tab?"),
                    panelToRemove.title
                ),
                type  : com.conjoon.SystemMessage.TYPE_CONFIRM
            }), {
            fn : function(button) {
                this._confirmCloseTabCallback(button, panelToRemove);
            },
            scope : this
        });

        return false;
    },

// -------- listeners

    /**
     * Callback for the dialog that was created via the _confirmCloseTab
     * method.
     *
     * @param {String} buttonText
     * @param {Ext.Panel} panelToRemove
     */
    _confirmCloseTabCallback : function(buttonText, panelToRemove)
    {
        if (buttonText == 'yes') {
            this._removeList.remove(panelToRemove.getId());
            this.remove(panelToRemove, true);
        }
    },

    /**
     * Listener for the beforeremove event for this panel.
     *
     * @param {com.conjoon.groupware.workbench.ContentPanel} contentPanel
     * @param {Ext.Component} componentToRemove
     */
    _onBeforeRemove : function(contentPanel, componentToRemove)
    {
        if (this._removeList.indexOf(componentToRemove.getId()) >= 0) {
            return this._confirmCloseTab(componentToRemove);
        }
    },

    _onAdd : function(container, component, index)
    {
        if (!this._removeList) {
            this._removeList = [];
        }

        var id  = component.id;
        var add = true;

        switch (id) {
            case 'DOM:com.conjoon.groupware.HomePanel':
            case 'DOM:com.conjoon.groupware.EmailPanel':
            case 'DOM:com.conjoon.groupware.ContactsPanel':
            case 'DOM:com.conjoon.groupware.TodoPanel':
            case 'DOM:com.conjoon.groupware.CalendarPanel':
                if (this._removeList.indexOf(id) < 0) {
                    this._removeList.push(id);
                    component.on('destroy', function(component) {
                        this._removeList.remove(component.getId());
                    }, this);
                }
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
    },

// -------- builders

    _getItems : function()
    {
        return [
            new com.conjoon.groupware.home.HomePanel(),
            this.getEmailPanel()
        ];
    },

    /**
     *
     * @return  {com.conjoon.groupware.email.EmailPanel}
     *
     * @protected
     */
    _getEmailPanel : function()
    {
        var w = new com.conjoon.groupware.email.EmailPanel();

        w.on('destroy', function() {
            this._emailPanel = null;
        }, this);

        return w;
    }


});