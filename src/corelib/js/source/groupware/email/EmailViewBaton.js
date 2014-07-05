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

Ext.namespace('com.conjoon.groupware.email');

/**
 * Helps adding email views to the main content panel.
 *
 */
com.conjoon.groupware.email.EmailViewBaton = function() {

    var openedEmails = {};

    var EmailEditorManager = com.conjoon.groupware.email.EmailEditorManager;

    var contentPanel = null;

    var toolbar = null;

    var activeRecord = null;

    var loadedViews = {};

    var tbarManager = com.conjoon.groupware.workbench.ToolbarController;

    var registerToolbar = function()
    {
        if (toolbar == null) {
            tbarManager = com.conjoon.groupware.workbench.ToolbarController;

            var decorateAccountRelatedClk = com.conjoon.groupware.email.decorator.AccountActionComp.decorate;

            var sendNowButton = new Ext.Toolbar.Button({
                cls     : 'x-btn-text-icon',
                iconCls : 'com-conjoon-groupware-email-EmailView-toolbar-sendNowButton-icon',
                text    : '&#160;'+com.conjoon.Gettext.gettext("Send now"),
                handler : function() {
                    _sendEmail();
                },
                scope   : com.conjoon.groupware.email.EmailViewBaton
            });
            var sendNowSeparator = new Ext.Toolbar.Separator();

            var forwardButton = new Ext.Toolbar.Button({
                id       : 'com.conjoon.groupware.email.EmailView.toolbar.ForwardButton',
                cls      : 'x-btn-text-icon',
                iconCls  : 'com-conjoon-groupware-email-EmailView-toolbar-forwardButton-icon',
                text     : '&#160;'+com.conjoon.Gettext.gettext("Forward"),
                handler  : function(){openEmailEditPanel('forward');}
            });
            var replyButton = new Ext.Toolbar.Button({
                id       : 'com.conjoon.groupware.email.EmailView.toolbar.ReplyButton',
                cls      : 'x-btn-text-icon',
                iconCls  : 'com-conjoon-groupware-email-EmailView-toolbar-replyButton-icon',
                text     : '&#160;'+com.conjoon.Gettext.gettext("Reply"),
                handler  : function(){openEmailEditPanel('reply');}
            });
            var replyAllButton = new Ext.Toolbar.Button({
                id       : 'com.conjoon.groupware.email.EmailView.toolbar.ReplyAllButton',
                cls      : 'x-btn-text-icon',
                iconCls  : 'com-conjoon-groupware-email-EmailView-toolbar-replyAllButton-icon',
                text     : '&#160;'+com.conjoon.Gettext.gettext("Reply all"),
                handler  : function(){openEmailEditPanel('reply_all');},
                scope    : this
            });

            var separator = new Ext.Toolbar.Separator();

            var editDraftButton = new Ext.Toolbar.Button({
                id       : 'com.conjoon.groupware.email.EmailView.toolbar.EditDraftButton',
                cls      : 'x-btn-text-icon',
                iconCls  : 'com-conjoon-groupware-email-EmailView-toolbar-editDraftButton-icon',
                text     : '&#160;'+com.conjoon.Gettext.gettext("Edit draft"),
                hidden   : true,
                handler  : function(){openEmailEditPanel('edit');}
            });


            toolbar = decorateAccountRelatedClk(new Ext.Toolbar([
                sendNowButton,
                sendNowSeparator,
                replyButton,
                replyAllButton,
                forwardButton,
                separator,
                editDraftButton
            ]));

            tbarManager.register('com.conjoon.groupware.email.EmailView.toolbar', toolbar);
        }
    };

    var openEmailEditPanel = function(type)
    {
        var tab = contentPanel.getActiveTab();

        var emailItem = tab.emailItem;

        if (!emailItem) {
            return;
        }

        EmailEditorManager.createEditor(emailItem, type);
    };

    /**
     * Called when the message with the subject "com.conjoon.groupware.email.editor.draftSave"
     * is published over Ext.ux.util.MessageBus.
     * Will check if a new version of the draft is available and remap the
     * openedEmail cache if necessary.
     *
     * @param {String} subject
     * @param {Object} message
     *
     */
    var _onDraftSave = function(subject, message)
    {
        var newVersion = message.newVersion;
        var previousId =  message.previousId;

        if (newVersion && previousId) {
            if (openedEmails[previousId]) {
                openedEmails[message.itemRecord.get('id')] = openedEmails[previousId];
                delete openedEmails[previousId];
            }
        }

    };

    /**
     * Called when the message with the subject "com.conjoon.groupware.email.EmailGrid.store.remove"
     * or "com.conjoon.groupware.email.EmailGrid.store.bulkremove"
     * is published over Ext.ux.util.MessageBus.
     * Will remove any tab that displays the removed record.
     *
     * @param {String} subject
     * @param {Object} message
     *
     */
    var _onEmailRemove = function(subject, message)
    {
        var items  = message.items;
        var opened = null;

        for (var i = 0, len = items.length; i < len; i++) {
            opened = openedEmails[items[i].id];

            if (opened) {
                contentPanel.remove(opened);
            }
        }
    };

    /**
     * Sends a request to the server to send the currently viewed email item,
     * if, and only if the email item's property "isOutboxPending" is set to true.
     *
     */
    var _sendEmail = function()
    {
        var emailPanel = contentPanel.getActiveTab();
        var emailItem  = emailPanel.emailItem;

        // should be instance of com.conjoon.groupware.email.EmailViewPanel
        if (!emailItem || !emailItem.get('isOutboxPending')) {
            return;
        }

        com.conjoon.groupware.email.Dispatcher.sendPendingEmails(
            [emailItem],
            Math.floor(((new Date()).getTime()/1000))
        );
    };

    /**
     * Subscribes to the EmailGrid store's remove message to close any tab that might
     * show the removed message
     */
    Ext.ux.util.MessageBus.subscribe(
        'com.conjoon.groupware.email.EmailGrid.store.remove',
        _onEmailRemove
    );
    Ext.ux.util.MessageBus.subscribe(
        'com.conjoon.groupware.email.LatestEmailsPanel.store.remove',
        _onEmailRemove
    );
    Ext.ux.util.MessageBus.subscribe(
        'com.conjoon.groupware.email.editor.draftSave',
        _onDraftSave
    );
    return {

        /**
         * Returns the record of an already opened view panel.
         *
         */
        getRecord : function(id)
        {
            if (loadedViews[id] === true) {
                return openedEmails[id].emailRecord;
            }
        },


        /**
         *
         * @param {Boolean} autoRender wether to try to render the email item that was
         * soecified in emailItem
         */
        showEmail : function(emailItem, config, autoRender)
        {
            if (!contentPanel) {
                contentPanel = com.conjoon.util.Registry.get('com.conjoon.groupware.ContentPanel');
            }

            if (toolbar == null) {
                registerToolbar();
            }

            var opened = openedEmails[emailItem.id];
            if (opened) {
                contentPanel.setActiveTab(opened);
                return opened;
            } else {

                if (!config) {
                    config = {};
                }

                if (autoRender) {
                    config.autoLoad = false;
                }

                if (!config.viewConfig) {
                    config.viewConfig = {};
                }

                Ext.applyIf(config.viewConfig, {
                    fromValue       : com.conjoon.Gettext.gettext("From"),
                    toValue         : com.conjoon.Gettext.gettext("To"),
                    ccValue         : com.conjoon.Gettext.gettext("CC"),
                    bccValue        : com.conjoon.Gettext.gettext("BCC"),
                    replyToValue    : com.conjoon.Gettext.gettext("Reply to")
                });

                Ext.applyIf(config, {
                    emailItem : emailItem,
                    autoLoad  : true,
                    border    : true
                });

                var view = new com.conjoon.groupware.email.EmailViewPanel(config);

                if (autoRender) {
                    view.emailRecord = emailItem;
                    view.on('render', function(){
                        this.renderView();
                        loadedViews[emailItem.id] = true;
                    }, view, {single : true});
                }

                view.on('destroy', function(panel){
                    delete loadedViews[panel.emailItem.id];
                    delete openedEmails[panel.emailItem.id];
                    // hide this only if there are no more email tabs to display
                    // this is needed if there is no tab which could be activated
                    // which shows a toolbar upon activate
                    var hide = true;
                    for (var i in loadedViews) {
                        hide = false;
                        break;
                    }
                    if (hide) {
                        tbarManager.hide('com.conjoon.groupware.email.EmailView.toolbar');
                    }
                });


                view.on('activate', function(panel) {
                    tbarManager.show('com.conjoon.groupware.email.EmailView.toolbar');

                    var eItem = panel.emailItem;

                    if (loadedViews[eItem.id]) {
                        tbarManager.disable('com.conjoon.groupware.email.EmailView.toolbar', false);
                    } else {
                        tbarManager.disable('com.conjoon.groupware.email.EmailView.toolbar', true);
                    }

                    var toolbar = tbarManager.get('com.conjoon.groupware.email.EmailView.toolbar');

                    var tItems = toolbar.items;

                    if (eItem.get('isDraft')) {
                        tItems.get(5).show();
                        tItems.get(6).show();
                    } else {
                        tItems.get(5).hide();
                        tItems.get(6).hide();
                    }

                    if (eItem.get('isOutboxPending')) {
                        tItems.get(0).show();
                        tItems.get(1).show();
                    } else {
                        tItems.get(0).hide();
                        tItems.get(1).hide();
                    }
                });

                view.on('deactivate', function(panel) {
                    tbarManager.hide('com.conjoon.groupware.email.EmailView.toolbar');
                    tbarManager.disable('com.conjoon.groupware.email.EmailView.toolbar', true);
                });

                view.on('emailload', function() {
                    loadedViews[emailItem.id] = true;
                    tbarManager.disable('com.conjoon.groupware.email.EmailView.toolbar', false);
                });

                view.on('emailloadfailure', function(response, options) {
                    com.conjoon.groupware.ResponseInspector.handleFailure(response, {
                        onLogin: {
                            fn : function(){
                                view.load();
                            },
                            scope : view
                        },
                        onGeneral : {
                            fn : function() {
                                this.ownerCt.remove(this);
                            },
                            scope : view
                        }
                    });
                });

                view.on('beforeemailload', function() {
                    loadedViews[emailItem.id] = false;
                    tbarManager.disable('com.conjoon.groupware.email.EmailView.toolbar', true);
                });

                contentPanel.add(view);
                contentPanel.setActiveTab(view);
                openedEmails[emailItem.id] = view;

                return view;
            }

        }
    }
}();