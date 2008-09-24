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

    var loadedViews = [];

    var registerToolbar = function()
    {
        if (toolbar == null) {
            var tbarManager = de.intrabuild.groupware.ToolbarManager;

            var decorateAccountRelatedClk = de.intrabuild.groupware.email.decorator.AccountActionComp.decorate;

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
                replyButton,
                replyAllButton,
                forwardButton,
                separator,
                editDraftButton
            ]));

            separator.render = function(td){
                this.td = td;
                td.appendChild(this.el);
                td.style.display = "none";
            };



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

        EmailEditorManager.createEditor(emailItem.id, type);
    };

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

                var tbarManager = de.intrabuild.groupware.ToolbarManager;

                if (autoRender) {
                    view.emailRecord = emailItem;
                    view.on('render', function(){
                        this.renderView();
                        loadedViews[emailItem.id] = true;
                    }, view);
                }

                view.on('destroy', function(panel){
                    openedEmails[panel.emailItem.id] = null;
                    loadedViews[panel.emailItem.id] = null;
                    delete loadedViews[panel.emailItem.id];
                    delete openedEmails[panel.emailItem.id];
                    tbarManager.hide('de.intrabuild.groupware.email.EmailView.toolbar');
                });


                view.on('activate', function(panel) {
                    tbarManager.show('de.intrabuild.groupware.email.EmailView.toolbar');
                    if (loadedViews[panel.emailItem.id]) {
                        tbarManager.disable('de.intrabuild.groupware.email.EmailView.toolbar', false);
                    } else {
                        tbarManager.disable('de.intrabuild.groupware.email.EmailView.toolbar', true);
                    }

                    var toolbar = tbarManager.get('de.intrabuild.groupware.email.EmailView.toolbar');

                    if (panel.emailItem.data.is_draft) {
                        toolbar.items.get(3).show();
                        toolbar.items.get(4).show();
                    } else {
                        toolbar.items.get(3).hide();
                        toolbar.items.get(4).hide();
                    }
                });

                view.on('deactivate', function(panel) {
                    tbarManager.hide('de.intrabuild.groupware.email.EmailView.toolbar');
                    tbarManager.disable('de.intrabuild.groupware.email.EmailView.toolbar', true);
                });

                view.on('emailload', function() {
                    loadedViews[emailItem.id] = true;
                    tbarManager.disable('de.intrabuild.groupware.email.EmailView.toolbar', false);
                    Ext.ux.util.MessageBus.publish(
                        'de.intrabuild.groupware.email.EmailViewBaton.onEmailLoad', {
                        id : emailItem.id
                    });
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
                    var tbarManager = de.intrabuild.groupware.ToolbarManager;
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