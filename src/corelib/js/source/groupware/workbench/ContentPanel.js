/**
 * conjoon
 * (c) 2007-2015 conjoon.org
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
     * @type {com.conjoon.groupware.home.HomePanel} _homePanel
     */
    _homePanel : null,

    /**
     * @type {Array} _removeList A list of ids of components which have to be confirmed
     * to get removed before tehy actually are
     */
    _removeList : null,

    /**
     * @type {com.conjoon.groupware.workbench.dd.TabDragZone) tabDragZone
     */
    _tabDragZone : null,

    /**
     * @type {com.conjoon.groupware.workbench.dd.TabDropZone) tabDropZone
     */
    _tabDropZone : null,

    /**
     * Inits this component.
     *
     */
    initComponent : function()
    {
        this.addEvents(
            /**
             * @event validatedrop Fires before an item gets dropped onto the DropZone.
             * Listeners should return false to cancel the drop.
             * @param {Object} dragData
             */
            'validatedrop',
            /**
             * @event beforedrop Fires before an item gets dropped onto the DropZone.
             * Listeners should return false to cancel the drop.
             * @param {Object} dragData
             */
            'beforedrop',
            /**
             * @event drop  Fires after an item was successfully dropped onto the DropZone.
             * @param {Object} dragData
             */
            'drop'
        );

        Ext.apply(this, {
            id              : 'DOM:com.conjoon.groupware.ContentPanel',
            region          : 'center',
            stateful : true,
            stateId : conjoon.state.base.Identifiers.workbench.panels.contentPanel,
            stateEvents : ['drop', 'tabchange', 'remove'],
            getState : function() {
                var tab = this.getActiveTab()

                var state = {
                    activeTabId : tab.id,
                    panelStates : []
                };

                for (var i = 0, items = this.items.items, len = items.length; i< len; i++) {
                    if ((items[i] instanceof com.conjoon.groupware.email.EmailViewPanel) ||
                        items[i].cnCompType === 'feedViewPanel') {
                        state.panelStates.push(items[i].getState());
                    }
                }

                return state;
            },
            applyState : function(state) {

                if (!state || !state.activeTabId) {
                    return;
                }
                var id = state.activeTabId,
                    tab,
                    panelStates = state.panelStates || [],
                    panel,
                    currPanelState;

                for (var i = 0, len = panelStates.length; i < len; i++) {

                    currPanelState = panelStates[i];

                    switch(currPanelState.cnCompType) {
                        case 'emailViewPanel':
                            panel = com.conjoon.groupware.email.EmailViewBaton.showEmail(
                                new com.conjoon.groupware.email.EmailItemRecord(
                                    currPanelState.emailItem,
                                    currPanelState.emailItem.id
                                )
                            );
                            // delete unneccessary state props before applying state
                            // to panel
                            delete currPanelState.emailItem;
                            delete currPanelState.cnCompType;

                            break;

                        case 'feedViewPanel':
                            panel = com.conjoon.groupware.feeds.FeedViewBaton.showFeed(
                                new com.conjoon.groupware.feeds.ItemRecord(
                                    currPanelState.feedItem, currPanelState.feedItem.id
                                ), true
                            );

                            panel = panel.view

                            // delete unneccessary state props before applying state
                            // to panel
                            delete currPanelState.feedItem;
                            delete currPanelState.cnCompType;

                            break;

                    }

                    // apply remaining state props, such as icon and title
                    panel.applyState(currPanelState);

                    /**
                     * When state is restored, the tabs do not get rendered when the state
                     * is applied in this method, since the content panel itself is not rendered
                     * yet.
                     * We have to manually call setIconCls and SetTitle for changing the according
                     * menu item in the start/bookmark menu
                     * @ticket CN-843
                     */
                    if (currPanelState.iconCls) {
                        panel.setIconClass(currPanelState.iconCls);
                    }
                    if (currPanelState.title) {
                        panel.setTitle(currPanelState.title);
                    }

                }

                tab = this.getComponent(id);

                if (tab) {
                    this.setActiveTab(tab);
                }

            },
            activeTab       : 0,
            resizeTabs      : true,
            minTabWidth     : 115,
            tabWidth        : 115,
            enableTabScroll : true,
            hideMode        : 'offsets',
            border          : false,
            baseCls         : 'com-conjoon-groupware-TabHeader',
            items           : this._getItems()
        });

        // we need to put the listener init here, since initEvents gets called
        // after the initial configuration for the items has been finished
        this.on('add',    this._onAdd, this);

        com.conjoon.util.Registry.register('com.conjoon.groupware.ContentPanel', this);
        com.conjoon.groupware.workbench.ContentPanel.superclass.initComponent.call(this);
    },

    initEvents : function()
    {
        com.conjoon.groupware.workbench.ContentPanel.superclass.initEvents.call(this);

        this.on('resize',       this.doLayout,        this);
        this.on('beforeremove', this._onBeforeRemove, this);
        this.on('beforedrop',   this._onBeforeDrop,   this);
        this.on('drop',         this._onDrop,         this);

        this.mon(this.header, 'mousedown', this._handleHeaderMouseDown, this);

        this._createDragDrop();
    },


    /**
     * Listener for the 'render' event.
     *
     */
    _createDragDrop : function()
    {
        var m = new Ext.Element(this.header.dom.firstChild);
        this._tabDragZone = new com.conjoon.groupware.workbench.dd.TabDragZone(
            this, m, [this.getHomePanel()]
        );
        this._tabDropZone = new com.conjoon.groupware.workbench.dd.TabDropZone(
            this, m, [{panel : this.getHomePanel(), pos : ['before']}]
        );

    },

    /**
     *
     */
    _handleHeaderMouseDown : function(e)
    {
        this._tabDragZone.callHandleMouseDown(e);
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
     *
     * @return {com.conjoon.groupware.home.HomePanel}
     */
    getHomePanel : function()
    {
        if (!this._homePanel) {
            this._homePanel = this._getHomePanel();
        }

        return this._homePanel;
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

    /**
     * Overrides parent implemenation to use custom ClickRepeaters of type
     * {com.conjoon.groupware.workbench.ClickRepeater}
     */
    createScrollers : function()
    {
        var _cr = Ext.util.ClickRepeater;

        Ext.util.ClickRepeater = Ext.emptyFn;

        com.conjoon.groupware.workbench.ContentPanel.superclass.createScrollers.call(this);

        Ext.util.ClickRepeater = _cr;

        this.leftRepeater = new com.conjoon.groupware.workbench.ClickRepeater(this.scrollLeft, {
            interval : this.scrollRepeatInterval,
            handler  : this.onScrollLeft,
            scope    : this
        });

        this.rightRepeater = new com.conjoon.groupware.workbench.ClickRepeater(this.scrollRight, {
            interval : this.scrollRepeatInterval,
            handler  : this.onScrollRight,
            scope    : this
        });

    },

    /**
     * Called from tabDropZone when "validatedrop"/"beforedrop" did not cancel
     * the drop event. Passes the dragged panel's container, the panel itself
     * and the position where to insert the panel as arguments.
     * After this method was called, the "drop" event will be fired.
     *
     * @param {Ext.TabPanel} container The container from which the panel was dragged
     * @param {Ext.Panel} panel The panel that was dragged and is bout to be dropped
     * @param {Number} position The index where to place the dragged panel
     */
    dropTo : function(container, panel, position)
    {
        var emailForm = com.conjoon.groupware.forms.QuickEmailForm;

        if (panel == emailForm.getComponent()) {
            com.conjoon.groupware.email.EmailEditorManager.createEditor(
                -1, 'new', {
                    name    : '',
                    address          : emailForm.getRecipient(),
                    contentTextPlain : emailForm.getMessage(),
                    subject          : emailForm.getSubject()
                }, position
            );
            emailForm.reset();

        } else if (panel == com.conjoon.groupware.service.youtube.ViewBaton.getBasePanel()) {

            com.conjoon.groupware.service.youtube.ViewBaton.showInFeaturePanel(position);

        } else {
            container.remove(panel, false);
            this.insert(position, panel);
            this.setActiveTab(panel);
        }

    },

// -------- listeners

    /**
     * Listener for the beforedrop event of the attached drop zone.
     * Suspends all "beforeremove" listeners.
     *
     * @param {Object} data The drag data that initiated the beforedrop-event.
     */
    _onBeforeDrop : function(data)
    {
        this.un('beforeremove', this._onBeforeRemove, this);
    },

    /**
     * Listener for the drop event of the attached drop zone.
     * Resumed all "beforeremove" listeners.
     *
     * @param {Object} data The drag data that initiated the drop-event.
     */
    _onDrop : function(data)
    {
        this.on('beforeremove', this._onBeforeRemove, this);
    },

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
        /**
         * @Ext-bug 3.1.2
         * we need to check this because working with bottom/right preview of
         * the EmailPanel might trigger the add event that bubbles up here.
         * not sure yet if this is an error or not
         */
        if (container.getId() != "DOM:com.conjoon.groupware.ContentPanel") {
            return;
        }

        if (!this._removeList) {
            this._removeList = [];
        }

        var id  = component.id;
        var add = true;

        var fix = [
            'DOM:com.conjoon.groupware.HomePanel', 'DOM:com.conjoon.groupware.EmailPanel',
            'DOM:com.conjoon.groupware.ContactsPanel', 'DOM:com.conjoon.groupware.TodoPanel',
            'DOM:com.conjoon.groupware.CalendarPanel'
        ]

        if (fix.indexOf(id) != -1) {
            if (this._removeList.indexOf(id) < 0) {
                this._removeList.push(id);
                component.on('destroy', function(component) {
                    this._removeList.remove(component.getId());
                }, this);
            }
            add = false;
        }

        var title   = component.title;
        var iconCls = component.iconCls;

        if (add && title) {
            var menu = com.conjoon.groupware.workbench.BookmarkController.getButton().menu;

            var dynIndex = com.conjoon.groupware.workbench.BookmarkController.getDynIndex();

            var existingItem = menu.findById('mi_'+component.getId());

            // item already exists, add position was obviously called because of
            // a dd operation
            if (existingItem) {
                // removing from the dom is needed as the menu's layout manager wouldn't
                // render the item at its correct position
                if (existingItem.el) {
                    existingItem.el.dom.parentNode.parentNode.removeChild(existingItem.el.dom.parentNode);
                    existingItem.rendered = false;
                }

                // calculate the index by checking if any tabs found in "fix"
                // appear -before- the newly added tab.
                var citems = container.items;
                var pos    = index;
                for (var i = 0, len = citems.length; i < len; i++) {
                    if (i > index) {
                        break;
                    }
                    if (fix.indexOf(citems.get(i).id) != -1) {
                        pos--;
                    }
                }
                menu.remove(existingItem, false);
                menu.insert(dynIndex+pos, existingItem);
                return;
            }

            var item = menu.add({
                id      : 'mi_'+component.getId(),
                iconCls : iconCls,
                text    : title
            });
            item.on('click', function() {
                this.setActiveTab(component);
            }, this);
            component.on('titlechange', function(){
                item.setText(this.title);
            }, component);
            component.on('iconchange', function(panel, newIconCls, oldIconCls){
                item.setIconClass(newIconCls);
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
            this.getHomePanel(),
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
    },

    /**
     *
     * @return  {com.conjoon.groupware.home.HomePanel}
     *
     * @protected
     */
    _getHomePanel : function()
    {
        var w = new com.conjoon.groupware.home.HomePanel();

        w.on('destroy', function() {
            this._homePanel = null;
        }, this);

        return w;
    }


});
