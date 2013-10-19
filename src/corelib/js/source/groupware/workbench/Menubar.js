/**
 * conjoon
 * (c) 2002-2012 siteartwork.de/conjoon.org
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
 * @class com.conjoon.groupware.workbench.Menubar
 * @singleton
 */
com.conjoon.groupware.workbench.Menubar = function(){

    var _menubar = null;

    var _viewMenu = null;

    var _emailMenu = null;

    var _accStore = null;

    /**
     *
     * @return {Ext.Toolbar.Button}
     */
    var _getEmailMenu = function()
    {
        if (_emailMenu) {
            return _emailMenu;
        }

        _emailMenu = new Ext.Toolbar.Button({
            text : com.conjoon.Gettext.gettext("Email"),
            menu : [{
                text    : com.conjoon.Gettext.gettext("Add account..."),
                handler : function() {
                    var w = new com.conjoon.groupware.email.EmailAccountWizard();
                    w.show();
                }
            },{
                text    : com.conjoon.Gettext.gettext("Options..."),
                handler : function() {
                    var w = new com.conjoon.groupware.email.EmailOptionsDialog();
                    w.show();
                }
            }, '-', {
                text    : com.conjoon.Gettext.gettext("Accounts..."),
                handler : function() {
                    var dialog = new com.conjoon.groupware.email.EmailAccountDialog();
                    dialog.show();
                }
            }]
        });

        return _emailMenu;
    };

    /**
     *
     * @param workbench {com.conjoon.groupware.Workbench}
     *
     * @return {Ext.Toolbar.Button}
     */
    var _getViewMenu = function(workbench)
    {
        if (_viewMenu) {
            return _viewMenu;
        }

        var westPanel = workbench.getWestPanel();
        var eastPanel = workbench.getEastPanel();

        var showWestPanelItem = new Ext.menu.CheckItem({
            text    : com.conjoon.Gettext.gettext("Left Dock"),
            checked : !westPanel.hidden,
            listeners : {
                checkchange : function(item, checked) {
                    this.getWestPanel().setVisible(item.checked);
                },
                scope : workbench
            }
        });

        var showEastPanelItem = new Ext.menu.CheckItem({
            text    : com.conjoon.Gettext.gettext("Right Dock"),
            checked : !eastPanel.hidden,
            listeners : {
                checkchange : function(item, checked) {
                    this.getEastPanel().setVisible(item.checked);
                },
                scope : workbench
            }
        });

        westPanel.on('add', function() {
            this.setDisabled(false);
        }, showWestPanelItem);

        eastPanel.on('add', function() {
            this.setDisabled(false);
        }, showEastPanelItem);

        eastPanel.on('remove', function(panel) {
            if (panel.items.items.length == 0) {
                this.setDisabled(true);
            }
        }, showEastPanelItem);

        westPanel.on('remove', function(panel) {
            if (panel.items.items.length == 0) {
                this.setDisabled(true);
            }
        }, showWestPanelItem);

        westPanel.on('show', function() {
            this.setChecked(true, true);
        }, showWestPanelItem);
        westPanel.on('hide', function() {
            this.setChecked(false, true);
        }, showWestPanelItem);

        eastPanel.on('show', function() {
            this.setChecked(true, true);
        }, showEastPanelItem);
        eastPanel.on('hide', function() {
            this.setChecked(false, true);
        }, showEastPanelItem);

        var itemMenu = new Ext.menu.Menu({
            items : [
                showWestPanelItem,
                showEastPanelItem,
                '-'
            ]
        });

        _viewMenu = new Ext.Toolbar.Button({
            text : com.conjoon.Gettext.gettext("View"),
            menu : [{
                text : com.conjoon.Gettext.gettext("Widgets &amp; Docks"),
                menu : itemMenu
            }, '-', {
                text    : com.conjoon.Gettext.gettext("Statusbar"),
                checked : true,
                handler : function(item) {
                    this.getSouthPanel().setVisible(!item.checked);
                    this.doLayout();
                },
                scope : workbench
            }]
        });

        workbench.on('afterlayout', function() {

            var items = [];

            var w = this.getWestPanel();
            var e = this.getEastPanel();

            var wItems = w.items;
            var eItems = e.items;
            var item   = null;

            for (var i = 0, len = wItems.length; i < len; i++) {
                item = wItems.get(i);
                itemMenu.add({
                    text    : item.itemTitle ? item.itemTitle : item.title,
                    checked : !item.hidden,
                    handler : function(menuItem) {
                        if (this.ownerCt.hidden && !menuItem.checked) {
                            this.ownerCt.setVisible(true);
                        }
                        this.setVisible(!menuItem.checked);
                    },
                    scope : item
                });
            }
            if (len == 0) {
                showWestPanelItem.setDisabled(true);
            }

            for (var i = 0, len = eItems.length; i < len; i++) {
                item = eItems.get(i);
                var menuItem = new Ext.menu.CheckItem({
                    text    : item.itemTitle ? item.itemTitle : item.title,
                    checked : !item.hidden,
                    handler : function(menuItem) {
                        if (this.ownerCt.hidden && !menuItem.checked) {
                            this.ownerCt.setVisible(true);
                        }
                        this.setVisible(!menuItem.checked);
                    },
                    scope : item
                });
                itemMenu.add(menuItem);
            }
            if (len == 0) {
                showEastPanelItem.setDisabled(true);
            }

        }, workbench, {single : true});

        return _viewMenu;
    };


    return {

        /**
         *
         * @param workbench {com.conjoon.groupware.Workbench}
         *
         * @return {Ext.Toolbar}
         */
        getInstance : function(workbench)
        {
            if (!_menubar) {

                _menubar = new Ext.Toolbar({
                    cls   : 'com-conjoon-groupware-Menubar',
                    items : [{
                        text : com.conjoon.Gettext.gettext("File"),
                        menu : [{
                            text : com.conjoon.Gettext.gettext("New"),
                            menu : [com.conjoon.groupware.email.decorator.AccountActionComp.decorate(new Ext.menu.Item({
                                text    : com.conjoon.Gettext.gettext("Email..."),
                                handler : function() {
                                   com.conjoon.groupware.email.EmailEditorManager.createEditor();
                                }
                            }))]
                        }, '-', {
                            text    : com.conjoon.Gettext.gettext("Logout..."),
                            handler : function() {
                                com.conjoon.groupware.Reception.showLogout();
                            }
                        }]
                    }, _getViewMenu(workbench)
                    , _getEmailMenu(), {
                        text : com.conjoon.Gettext.gettext("Extras"),
                        menu : [{
                            text    : com.conjoon.Gettext.gettext("Feeds..."),
                            handler : function() {
                                var dialog = new com.conjoon.groupware.feeds.FeedOptionsDialog();
                                dialog.show();
                            }
                        }, '-', {
                            text : com.conjoon.Gettext.gettext("Local cache"),
                            handler : function() {
                                com.conjoon.groupware.localCache.options.Dialog.showDialog();
                            }
                        }]
                    }, {
                        text : com.conjoon.Gettext.gettext("Help"),
                        menu : [{
                            text     : com.conjoon.Gettext.gettext("Online User Guide"),
                            handler  : function() {
                                window.open("http://conjoon.org/wiki/display/DOC/conjoon+User%27s+Guide", 'conjoonResources');
                            }
                        }, {
                            text     : com.conjoon.Gettext.gettext("Online Forums"),
                            handler  : function() {
                                window.open("http://conjoon.org/forum", 'conjoonResources');
                            }
                        }, {
                            text     : com.conjoon.Gettext.gettext("Provide Feedback..."),
                            handler  : function() {
                                var sd = new com.conjoon.groupware.workbench.tools.FeedbackDialog();
                                sd.show();
                            }
                        }, '-', {
                            text : com.conjoon.Gettext.gettext("About conjoon"),
                            handler : function() {
                                com.conjoon.groupware.workbench.AboutDialog.show();
                            }
                        }]
                    }]
                });

            }

            return _menubar;
        }
    };

}();
