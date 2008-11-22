/**
 * intrabuild
 * (c) 2002-2008 siteartwork.de/MindPatterns
 * license@siteartwork.de
 *
 * $Author$
 * $Id$
 * $Date$
 * $Revision$
 * $LastChangedDate$
 * $LastChangedBy$
 * $URL$
 */

Ext.namespace('de.intrabuild.groupware.email');

/**
 * Helps adding email views to the main content panel.
 *
 */
de.intrabuild.groupware.email.EmailViewBaton = function() {

    var openedEmails = {};

    var EmailEditorManager = de.intrabuild.groupware.email.EmailEditorManager;

    var contentPanel = null;

    var toolbar = null;

    var activeRecord = null;

    var loadedViews = {};

    var tbarManager = de.intrabuild.groupware.ToolbarManager;

    var registerToolbar = function()
    {
        if (toolbar == null) {
            tbarManager = de.intrabuild.groupware.ToolbarManager;

            var decorateAccountRelatedClk = de.intrabuild.groupware.email.decorator.AccountActionComp.decorate;

            var sendNowButton = new Ext.Toolbar.Button({
                cls     : 'x-btn-text-icon',
                hidden  : true,
                iconCls : 'de-intrabuild-groupware-email-EmailView-toolbar-sendNowButton-icon',
                text    : '&#160;'+de.intrabuild.Gettext.gettext("Send now"),
                handler : function() {
                    _sendEmail();
                },
                scope   : de.intrabuild.groupware.email.EmailViewBaton
            });
            var sendNowSeparator = new Ext.Toolbar.Separator({
                hidden : true
            });

            var forwardButton = new Ext.Toolbar.Button({
                id       : 'de.intrabuild.groupware.email.EmailView.toolbar.ForwardButton',
                cls      : 'x-btn-text-icon',
                iconCls  : 'de-intrabuild-groupware-email-EmailView-toolbar-forwardButton-icon',
                text     : '&#160;'+de.intrabuild.Gettext.gettext("Forward"),
                handler  : function(){openEmailEditPanel('forward');}
            });
            var replyButton = new Ext.Toolbar.Button({
                id       : 'de.intrabuild.groupware.email.EmailView.toolbar.ReplyButton',
                cls      : 'x-btn-text-icon',
                iconCls  : 'de-intrabuild-groupware-email-EmailView-toolbar-replyButton-icon',
                text     : '&#160;'+de.intrabuild.Gettext.gettext("Reply"),
                handler  : function(){openEmailEditPanel('reply');}
            });
            var replyAllButton = new Ext.Toolbar.Button({
                id       : 'de.intrabuild.groupware.email.EmailView.toolbar.ReplyAllButton',
                cls      : 'x-btn-text-icon',
                iconCls  : 'de-intrabuild-groupware-email-EmailView-toolbar-replyAllButton-icon',
                text     : '&#160;'+de.intrabuild.Gettext.gettext("Reply all"),
                handler  : function(){openEmailEditPanel('reply_all');},
                scope    : this
            });

            var separator = new Ext.Toolbar.Separator({
                hidden : true
            });

            var editDraftButton = new Ext.Toolbar.Button({
                id       : 'de.intrabuild.groupware.email.EmailView.toolbar.EditDraftButton',
                cls      : 'x-btn-text-icon',
                iconCls  : 'de-intrabuild-groupware-email-EmailView-toolbar-editDraftButton-icon',
                text     : '&#160;'+de.intrabuild.Gettext.gettext("Edit draft"),
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

            var func = function(td){
                this.td = td;
                td.appendChild(this.el);
                td.style.display = "none";
            };

            sendNowSeparator.render = func;
            separator.render        = func;

            tbarManager.register('de.intrabuild.groupware.email.EmailView.toolbar', toolbar);
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
     * Called when the message with the subject "de.intrabuild.groupware.email.EmailGrid.store.remove"
     * or "de.intrabuild.groupware.email.EmailGrid.store.bulkremove"
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

        // should be instance of de.intrabuild.groupware.email.EmailViewPanel
        if (!emailItem || !emailItem.get('isOutboxPending')) {
            return;
        }

        de.intrabuild.groupware.email.Dispatcher.sendPendingEmails(
            [emailItem],
            ((new Date()).getTime()/1000)
        );
    };

    /**
     * Subscribes to the EmailGrid store's remove message to close any tab that might
     * show the removed message
     */
    Ext.ux.util.MessageBus.subscribe(
        'de.intrabuild.groupware.email.EmailGrid.store.remove',
        _onEmailRemove
    );
    Ext.ux.util.MessageBus.subscribe(
        'de.intrabuild.groupware.email.LatestEmailsPanel.store.remove',
        _onEmailRemove
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
                contentPanel = de.intrabuild.util.Registry.get('de.intrabuild.groupware.ContentPanel');
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
                    fromValue       : de.intrabuild.Gettext.gettext("From"),
                    toValue         : de.intrabuild.Gettext.gettext("To"),
                    ccValue         : de.intrabuild.Gettext.gettext("CC"),
                    bccValue        : de.intrabuild.Gettext.gettext("BCC"),
                    replyToValue    : de.intrabuild.Gettext.gettext("Reply to"),
                    attachmentValue : de.intrabuild.Gettext.gettext("Attachments")
                });

                Ext.applyIf(config, {
                    emailItem : emailItem,
                    autoLoad  : true,
                    border    : true
                });

                var view = new de.intrabuild.groupware.email.EmailViewPanel(config);

                if (autoRender) {
                    view.emailRecord = emailItem;
                    view.on('render', function(){
                        this.renderView();
                        loadedViews[emailItem.id] = true;
                    }, view);
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
                        tbarManager.hide('de.intrabuild.groupware.email.EmailView.toolbar');
                    }
                });


                view.on('activate', function(panel) {
                    tbarManager.show('de.intrabuild.groupware.email.EmailView.toolbar');

                    var eItem = panel.emailItem;

                    if (loadedViews[eItem.id]) {
                        tbarManager.disable('de.intrabuild.groupware.email.EmailView.toolbar', false);
                    } else {
                        tbarManager.disable('de.intrabuild.groupware.email.EmailView.toolbar', true);
                    }

                    var toolbar = tbarManager.get('de.intrabuild.groupware.email.EmailView.toolbar');

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
                    tbarManager.hide('de.intrabuild.groupware.email.EmailView.toolbar');
                    tbarManager.disable('de.intrabuild.groupware.email.EmailView.toolbar', true);
                });

                view.on('emailload', function() {
                    loadedViews[emailItem.id] = true;
                    tbarManager.disable('de.intrabuild.groupware.email.EmailView.toolbar', false);
                });

                view.on('emailloadfailure', function(response, options) {
                    de.intrabuild.groupware.ResponseInspector.handleFailure(response, {
                        onLogin: {
                            fn : function(){
                                view.load();
                            },
                            scope : view
                        }
                    });
                });

                view.on('beforeemailload', function() {
                    loadedViews[emailItem.id] = false;
                    tbarManager.disable('de.intrabuild.groupware.email.EmailView.toolbar', true);
                });

                contentPanel.add(view);
                contentPanel.setActiveTab(view);
                openedEmails[emailItem.id] = view;

                return view;
            }

        }
    }
}();