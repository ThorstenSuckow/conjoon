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
* Controller for the emailpanels tree, preview and grid.
*
*/
com.conjoon.groupware.email.EmailPanel = Ext.extend(Ext.Panel, {

    buttonsLocked : false,

    pendingRecords : null,

    pendingRecordsDate : {},

    queue : null,

    clkNodeId : null,

    /**
     * @type {Number} lastClkNodeId Caches the id of the node that was last clicked
     * before it gets set to null
     */
    lastClkNodeId : null,

    initComponent : function()
    {
        var me = this;

        this.addEvents({
             /**
              * @event emaildeleted
              * Fires when an email has been permanently deleted from the grid's store
              * @param {Array} records
              */
            'emailsdeleted' : true
        });

        /**
         * The tree panel that holds the tree representing the folder structure
         * of the user.
         */
        this.treePanel = Ext.createInstance('conjoon.mail.folder.comp.StatefulFolderPanel', {
            region            : 'west',
            stateId           : conjoon.state.base.Identifiers.emailModule.contentPanel.folderTree,
            anonymousNodeText : com.conjoon.Gettext.gettext("New folder"),
            width             : 200,
            split             : true,
            collapsible       : true,
            collapseMode      :'mini'
        });


        /**
         * Preview panel for the emails
         */
        this.preview = new com.conjoon.groupware.email.EmailViewPanel({
            autoLoad     : false,
            border       : false,
            hideMode     : 'offsets',
            refreshFrame : true
        });

        /**
         * The grid that shows the email items.
         */
        this.gridPanel = new com.conjoon.groupware.email.EmailGrid({
            region     : 'center',
            split      : true,
            controller : this
        });

        this.introductionPanel = new com.conjoon.groupware.email.view.IntroductionPanel();

        this.mainContentPanel = new Ext.Container({
            layout   : 'border',
            border   : false,
            hideMode : 'offsets',
            items    : [
                this.gridPanel,
                new Ext.Container({
                    id       : 'com-conjoon-groupware-email-rightPreview',
                    layout   : 'fit',
                    hideMode : 'offsets',
                    stateful : true,
                    stateId : conjoon.state.base.Identifiers.emailModule.contentPanel.rightPreview,
                    stateEvents : ['show', 'hide', 'resize'],
                    applyState : function(state) {
                        Ext.Container.prototype.applyState.apply(this, arguments);

                        if (state.hidden === false) {
                            this.add(me.preview);
                        }
                    },
                    getState : function() {
                        return {
                            width : this.getWidth(),
                            hidden : !this.isVisible()
                        };
                    },
                    region   : 'east',
                    split    : true,
                    hidden   : true,
                    listeners : {
                        render : {
                            fn : function(cont) {
                                if (cont.width && cont.width <
                                    this.centerPanel.el.getWidth() - this.treePanel.el.getWidth()) {
                                    return;
                                }

                                var w = Math.round((this.centerPanel.el.getWidth() -
                                        this.treePanel.el.getWidth())/2);
                                cont.width = w;
                            }
                        },
                        scope : this
                    }
            }), new Ext.Container({
                id        : 'com-conjoon-groupware-email-bottomPreview',
                layout    : 'fit',
                stateful : true,
                stateId : conjoon.state.base.Identifiers.emailModule.contentPanel.bottomPreview,
                stateEvents : ['show', 'hide', 'resize'],
                hidden : true,
                initState : function() {

                    Ext.Container.prototype.initState.call(this);

                    /** defines default behavior so that bottom preview is by
                     * default the container for the mail preview if no other
                     * container is explicitely defined.
                     * provides also a few fallback mechanisms in case state
                     * data is corrupted
                     * @ticket CN-856
                     */
                    var StateManager = Ext.state.Manager,
                        contentPanelStateId = conjoon.state.base.Identifiers.emailModule.contentPanel,
                        bottomState = StateManager.get(contentPanelStateId.bottomPreview),
                        rightState = StateManager.get(contentPanelStateId.rightPreview);

                    if ((!rightState && !bottomState) ||
                        (!bottomState && rightState && rightState.hidden)) {
                        this.add(me.preview);
                        this.setVisible(true);
                    }

                },
                applyState : function(state) {
                    Ext.Container.prototype.applyState.apply(this, arguments);

                    if (state.hidden === false) {
                        this.add(me.preview);
                    }
                },
                getState : function() {
                    return {
                        height : this.getHeight(),
                        hidden : !this.isVisible()
                    };
                },
                hideMode  : 'offsets',
                split     : true,
                region    : 'south',
                listeners : {
                    render : {
                        fn : function(cont) {
                            if (cont.height && cont.height < this.ownerCt.body.getHeight()) {
                                return;

                            }
                            var h = Math.round(this.ownerCt.body.getHeight()/2);
                            cont.height = h;
                        }
                    },
                    scope : this
                }
            })]
        });

        this.centerPanel = new Ext.Container({
            region     : 'center',
            layout     : new Ext.layout.CardLayout({
                forceLayout : true
            }),
            border     : false,
            activeItem : 0,
            hideMode   : 'offsets',
            items:[
                this.introductionPanel,
                this.mainContentPanel
            ]
        });

        this.items = [
            this.treePanel,
            this.centerPanel
        ];

        /**
         * Top toolbar
         * @param {Ext.Toolbar}
         */
        var decorateAccountRelatedClk = com.conjoon.groupware.email.decorator.AccountActionComp.decorate;



        this.sendNowButton = decorateAccountRelatedClk(new Ext.Toolbar.Button({
            id      : 'com.conjoon.groupware.email.toolbar.SendButton',
            iconCls : 'com-conjoon-groupware-email-EmailPanel-toolbar-sendNowButton-icon',
            cls     : 'x-btn-text-icon',
            text    : com.conjoon.Gettext.gettext("Send now"),
            hidden  : true,
            handler : function(){this.sendPendingItems();},
            scope   : this
        }));
        this.forwardButton = decorateAccountRelatedClk(new Ext.Toolbar.Button({
            id       : 'com.conjoon.groupware.email.toolbar.ForwardButton',
            cls      : 'x-btn-text-icon',
            iconCls  : 'com-conjoon-groupware-email-EmailPanel-toolbar-forwardButton-icon',
            text     : '&#160;'+com.conjoon.Gettext.gettext("Forward"),
            handler  : function(){this.openEmailEditPanel(true, 'forward');},
            disabled : true,
            scope    : this
        }));
        this.replyButton = decorateAccountRelatedClk(new Ext.Toolbar.Button({
            id       : 'com.conjoon.groupware.email.toolbar.ReplyButton',
            cls      : 'x-btn-text-icon',
            iconCls  : 'com-conjoon-groupware-email-EmailPanel-toolbar-replyButton-icon',
            text     : '&#160;'+com.conjoon.Gettext.gettext("Reply"),
            handler  : function(){this.openEmailEditPanel(true, 'reply');},
            disabled : true,
            scope    : this
        }));
        this.replyAllButton = decorateAccountRelatedClk(new Ext.Toolbar.Button({
            id       : 'com.conjoon.groupware.email.toolbar.ReplyAllButton',
            cls      : 'x-btn-text-icon',
            iconCls  : 'com-conjoon-groupware-email-EmailPanel-toolbar-replyAllButton-icon',
            text     : '&#160;'+com.conjoon.Gettext.gettext("Reply all"),
            handler  : function(){this.openEmailEditPanel(true, 'reply_all');},
            disabled : true,
            scope    : this
        }));
        this.deleteButton = new Ext.Toolbar.Button({
            id       : 'com.conjoon.groupware.email.toolbar.DeleteButton',
            cls      : 'x-btn-text-icon',
            iconCls  : 'com-conjoon-groupware-email-EmailPanel-toolbar-deleteButton-icon',
            text     : '&#160;'+com.conjoon.Gettext.gettext("Delete"),
            disabled : true,
            handler  : function(){this.deleteEmails(this.gridPanel.selModel.getSelections());},
            scope    : this
        });
        this.spamButton = new Ext.Toolbar.Button({
            id       : 'com.conjoon.groupware.email.toolbar.SpamButton',
            cls      : 'x-btn-text-icon',
            iconCls  : 'com-conjoon-groupware-email-EmailPanel-toolbar-spamButton-icon',
            text     : '&#160;'+com.conjoon.Gettext.gettext("Spam"),
            disabled : true,
            handler  : function(){this.setItemsAsSpam(this.gridPanel.selModel.getSelections(), true);},
            scope    : this
        });
        this.noSpamButton = new Ext.Toolbar.Button({
            id       : 'com.conjoon.groupware.email.toolbar.NoSpamButton',
            cls      : 'x-btn-text-icon',
            iconCls  : 'com-conjoon-groupware-email-EmailPanel-toolbar-noSpamButton-icon',
            text     : '&#160;'+com.conjoon.Gettext.gettext("No spam"),
            disabled : true,
            hidden   : true,
            handler  : function(){this.setItemsAsSpam(this.gridPanel.selModel.getSelections(), false);},
            scope    : this
        });
        this.editDraftButton = new Ext.Toolbar.Button({
            id       : 'com.conjoon.groupware.email.toolbar.EditDraftButton',
            cls      : 'x-btn-text-icon',
            iconCls  : 'com-conjoon-groupware-email-EmailPanel-toolbar-editDraftButton-icon',
            text     : '&#160;'+com.conjoon.Gettext.gettext("Edit draft"),
            disabled : true,
            hidden   : true,
            handler  : function(){this.openEmailEditPanel(true, 'edit');},
            scope    : this
        });
        this.newButton = decorateAccountRelatedClk(new Ext.Toolbar.Button({
            cls      : 'x-btn-text-icon',
            iconCls  : 'com-conjoon-groupware-email-EmailPanel-toolbar-newButton-icon',
            text     : '&#160;'+com.conjoon.Gettext.gettext("New email"),
            handler  : function(){this.openEmailEditPanel(false, 'new');},
            scope    : this
        }));

        this.previewButton = new Ext.Toolbar.SplitButton({
            id           : 'com.conjoon.groupware.email.previewButton',
            split        : true,
            enableToggle : true,
            hidden       : true,
            stateId      : conjoon.state.base.Identifiers.emailModule.contentPanel.previewButton,
            stateEvents  : ['toggle'],
            stateful : true,
            applyState : function(state) {
                // hidePreview() will trigger the saveState for the button so the
                // state of the menu items gets saved when they are clicked
                // and hidePreview is invoked
                var checkedMenuItemId = state.checkedMenuItemId,
                    items = this.menu.items.items;
                delete state.checkedMenuItemId;

                Ext.Toolbar.SplitButton.prototype.applyState.apply(this, arguments);

                for (var i = 0, len = items.length; i < len; i++) {
                    items[i].checked = items[i].itemId == 'menuItemBottom' && !checkedMenuItemId
                                       ? true
                                       : false;
                    if (items[i].itemId == checkedMenuItemId) {
                        items[i].checked = true;
                    }
                }
            },
            getState : function() {

                var items = this.menu.items.items,
                    checkedItemId = null;


                for (var i = 0, len = items.length; i < len; i++) {
                    if (items[i].checked) {
                        checkedItemId = items[i].itemId;
                        break;
                    }
                }

                return {
                    pressed : this.pressed,
                    checkedMenuItemId : checkedItemId
                };
            },
            pressed      : true,
            handler      : this.hidePreview,
            scope        : this,
            cls          : 'x-btn-text-icon',
            iconCls      : 'com-conjoon-groupware-email-EmailPanel-toolbar-previewBottomButton-icon',
            text         : com.conjoon.Gettext.gettext("Preview"),
            menu         : {
                id    : 'com.conjoon.groupware.email.emailPreviewMenu',
                cls   : 'com-conjoon-groupware-email-EmailPanel-toolbar-previewMenu',
                items : [{
                itemId       : 'menuItemBottom',
                iconCls      : 'com-conjoon-groupware-email-EmailPanel-toolbar-previewBottomButton-icon',
                text         : com.conjoon.Gettext.gettext("bottom"),
                // is being reset in appylState of the owning splitbutton
                checked      : true,
                group        : 'com.conjoon.groupware.email.emailPreviewGroup',
                checkHandler : this.hidePreview,
                scope        : this
              }, {
                itemId       : 'menuItemRight',
                iconCls      : 'com-conjoon-groupware-email-EmailPanel-toolbar-previewRightButton-icon',
                text         : com.conjoon.Gettext.gettext("right"),
                checked      : false,
                group        : 'com.conjoon.groupware.email.emailPreviewGroup',
                checkHandler : this.hidePreview,
                scope        : this
              }, {
                itemId       : 'menuItemHide',
                iconCls      : 'com-conjoon-groupware-email-EmailPanel-toolbar-previewHideButton-icon',
                text         : com.conjoon.Gettext.gettext("hide"),
                checked      : false,
                group        : 'com.conjoon.groupware.email.emailPreviewGroup',
                checkHandler : this.hidePreview,
                scope        : this
            }]}
        });

        this.controlBar = new Ext.Toolbar([
            new com.conjoon.groupware.email.FetchMenuButton(),
            this.newButton ,
            '-',
            this.sendNowButton,
            this.replyButton,
            this.replyAllButton,
            this.forwardButton,
            '-',
            this.deleteButton,
            this.spamButton,
            this.noSpamButton,
            this.editDraftButton,
            '->',
            this.previewButton
        ]);

        this.tbarManager = com.conjoon.groupware.workbench.ToolbarController;
        this.tbarManager.register('com.conjoon.groupware.email.Toolbar', this.controlBar);

        Ext.apply(this,  {
            title          : com.conjoon.Gettext.gettext("Emails"),
            iconCls        : 'com-conjoon-groupware-email-EmailPanel-icon',
            closable       : true,
            id             : 'DOM:com.conjoon.groupware.EmailPanel',
            autoScroll     : false,
            deferredRender : true,
            layout         : 'border',
            hideMode       : 'offsets'
        });

        this.on('render',  this.onPanelRender, this, {single : true});

        com.conjoon.groupware.email.EmailPanel.superclass.initComponent.call(this);
    },

    initEvents : function()
    {
        // install the listeners. As the controller of the emailapp, this class should
        // be responsible for listening to almost all events and delegating actions
        // to the various components.

        /**
         * Subscribe to com.conjoon.groupware.email.view.onEmailLoad
         */
        Ext.ux.util.MessageBus.subscribe(
            'com.conjoon.groupware.email.view.onEmailLoad',
            this.onEmailLoad,
            this
        );

        this.mon(this.gridPanel, 'rowdblclick', this.openEmailView, this);

        this.mon(this.preview, 'emailloadfailure', this.onEmailLoadFailure, this);
        this.mon(this.preview, 'show',             this._onPreviewShow, this);

        var letterman = com.conjoon.groupware.email.Letterman;
        this.mon(letterman, 'load', this.newEmailsAvailable, this);

        var gs = this.gridPanel.store;
        this.mon(gs, 'beforeload',           this.onGridStoreBeforeLoad,  this);
        this.mon(gs, 'clear',                this.onGridStoreClear,       this);
        this.mon(gs, 'beforeselectionsload', this.onBeforeSelectionsLoad, this);
        this.mon(gs, 'selectionsload',       this.onSelectionsLoad,       this);
        this.mon(gs, 'load',                 this.onStoreLoad,            this);

        var gm = this.gridPanel.selModel;
        this.mon(gm, 'rowselect', this.onRowSelect,     this, {buffer : 100});
        this.mon(gm, 'rowdeselect', this.onRowDeselect, this, {buffer : 100});

        var gv = this.gridPanel.view;
        this.mon(gv, 'rowremoved',   this.onRowRemoved,         this);
        this.mon(gv, 'beforebuffer', this.onBeforeBuffer,       this);
        this.mon(gv, 'buffer',       this.onStoreBuffer,        this);
        this.mon(gv, 'reset',        this.onGridPanelViewReset, this);

        var tp = this.treePanel;
        this.mon(tp, 'movenode', this.onMoveNode, this);
        tp.on('render', function(){
            var sm = this.treePanel.getSelectionModel();
            sm.on('selectionchange', this.onNodeSelectionChange, this);
            sm.on('selectionchange', this.introductionPanel._onNodeSelectionChange, this.introductionPanel);
        }, this, {single : true});

        this.mon(tp, 'nodedrop', this.onNodeDrop, this);
        this.mon(tp, 'remove', this.onNodeRemove, this);
        this.mon(tp.pendingItemStore, 'add', this.onPendingStoreAdd, this);

        this.on('hide', function(){this.tbarManager.hide('com.conjoon.groupware.email.Toolbar');}, this);

        this.on('destroy', function(){
            var sub = com.conjoon.util.Registry.get('com.conjoon.groupware.email.QuickPanel');
            if (sub) {
                sub.store.un('update', this.onQuickPanelUpdate, this);
            }
            this.tbarManager.destroy('com.conjoon.groupware.email.Toolbar');
        }, this);
        this.on('show',    function(){this.tbarManager.show('com.conjoon.groupware.email.Toolbar');}, this);

        com.conjoon.util.Registry.register('com.conjoon.groupware.email.EmailPanel', this, true);

        // register listener for MessageBus message
        // 'com.conjoon.groupware.email.Smtp.emailSent'
        Ext.ux.util.MessageBus.subscribe(
            'com.conjoon.groupware.email.Smtp.emailSent',
            this._onSendEmail,
            this
        );

        // register listener for MessageBus message
        // 'com.conjoon.groupware.email.Smtp.beforeBulkSent'
        Ext.ux.util.MessageBus.subscribe(
            'com.conjoon.groupware.email.Smtp.beforeBulkSent',
            this._onBeforeBulkSent,
            this
        );

        // register listener for MessageBus message
        // 'com.conjoon.groupware.email.Smtp.bulkSent'
        Ext.ux.util.MessageBus.subscribe(
            'com.conjoon.groupware.email.Smtp.bulkSentFailure',
            this._onBulkSentFailure,
            this
        );

        // register listener for MessageBus message
        // 'com.conjoon.groupware.email.Smtp.bulkSent'
        Ext.ux.util.MessageBus.subscribe(
            'com.conjoon.groupware.email.Smtp.bulkSent',
            this._onBulkSent,
            this
        );

        // register listener for MessageBus message
        // 'com.conjoon.groupware.email.editor.draftSaved'
        Ext.ux.util.MessageBus.subscribe(
            'com.conjoon.groupware.email.editor.draftSave',
            this._onSaveDraft,
            this
        );

        // register listener for MessageBus message
        // 'com.conjoon.groupware.email.LatestEmailCache.clear'
        Ext.ux.util.MessageBus.subscribe(
            'com.conjoon.groupware.email.LatestEmailCache.clear',
            this._onLatestCacheClear,
            this
        );

        // register listener for MessageBus message
        // 'com.conjoon.groupware.email.outbox.emailMoved'
        Ext.ux.util.MessageBus.subscribe(
            'com.conjoon.groupware.email.outbox.emailMove',
            this._onMoveOutbox,
            this
        );
        com.conjoon.groupware.email.EmailPanel.superclass.initEvents.call(this);
    },


// ------------------------------- Methods -------------------------------------

    /**
     * Moves the specified records either to the trashbin or deletes them, if they
     * are already in the trashbin.
     *
     * Triggers a message if the trashbin is still part of a proxy subtree.
     *
     * @thrws {conjoon.mail.folder.base.FolderNotFoundException} if the folder
     * representing the trash bin was not found
     *
     */
    deleteEmails : function(records)
    {
        var me = this,
            treePanel = me.treePanel,
            trashNode = null,
            currentNode = me.clkNodeId ? treePanel.getNodeById(me.clkNodeId) : null,
            currentPath = null;

        if (currentNode && currentNode.getPath('type').indexOf('trash') != -1) {

            currentPath = currentNode.getPathAsArray('idForPath');
            trashNode = treePanel.getNodeForPath(treePanel.findPathFor(
                currentPath[1], 'trash'
            ));

            if (!trashNode) {
                throw new conjoon.mail.folder.base.FolderNotFoundException(
                    "Folder representing the trash bin was not found!"
                );
            }

            if (me.showInfoAboutTrashIsProxy(treePanel, trashNode)) {
                return;
            }

            var unread = 0;

            var max_i = records.length

            if (max_i == 0) {
                return;
            }

            var gs = this.gridPanel.store;
            var requestArray = [];
            for (var i = 0; i < max_i; i++) {
                unread += (records[i].data.isRead ? 0 : 1);
                requestArray.push({ id : records[i].id });
            }

            this.fireEvent('emailsdeleted', records);

            var pendingStore  = this.treePanel.pendingItemStore;
            var pendingRecord = pendingStore.getById(
                this.treePanel.getNodeById(this.clkNodeId).getPath('idForPath')
            );
            if (pendingRecord) {
                pendingRecord.set('pending', pendingRecord.data.pending-unread);
            }

            Ext.Ajax.request({
                url: './groupware/email.item/delete.items/format/json',
                params: {
                    path          : Ext.encode(this.treePanel.getNodeById(this.clkNodeId)
                                    .getPathAsArray('idForPath')),
                    itemsToDelete : Ext.encode(requestArray)
                }
            });

            gs.bulkRemove(records);

        } else {

            var currP = this.treePanel.getNodeById(this.clkNodeId)
                .getPathAsArray('idForPath')[1];

            var trashId = this.treePanel.findPathFor(currP, 'trash');

            if (!trashId) {
                conjoon.SystemMessage.warn({
                    title : com.conjoon.Gettext.gettext("No trashbin found"),
                    text : com.conjoon.Gettext.gettext("No trashbin for the current account found. Have you configured the account's folder mappings?")
                });

                return;
            }

            // if trashId available, we have a loaded node OR a mapping
            // if the trashNode is not found, the trash was not loaded yet,
            // which is okay if the folder maps a remote mailbox
            var trashNode = treePanel.getNodeForPath(trashId);
            if (trashNode) {
                // if available in the tree, i.e. rendered, check if proxy,
                // / show message and exit!
                if (me.showInfoAboutTrashIsProxy(treePanel, trashNode)) {
                    return;
                }
            }

            this.moveEmails(records, trashId);
        }
    },

    /**
     * Moves the number of specified records virtually into the folder with
     * the specified folder-id. If allowNodePendingUpdate returns false, the total
     * number of records will be subtracted from the specified pending node, not just
     * the records that were unread.
     *
     * This methode does nothing if the specified folder is part of a proxy subtree.
     *
     * @throws {conjoon.mail.folder.base.FolderNotFoundException} if the folder
     * represented by folderId was not found
     */
    moveEmails : function(records, folderId)
    {
        var me = this,
            tp = me.treePanel,
            folderNode = null;

        if ((typeof folderId).toLowerCase() != "object") {
            folderNode = tp.getNodeById(folderId);
            folderId = tp.getNodeById(folderId).getPathAsArray('idForPath');
        } else {
            folderNode = tp.getNodeForPath(folderId);
        }

        if (!folderNode) {
            throw new conjoon.mail.folder.base.FolderNotFoundException(
                "Folder with ID " + folderId + "was not found"
            );
        }

        if (tp.getFolderService().isPartOfProxySubtree(folderNode)) {
            return;
        }

        var currFolderId  = this.clkNodeId;

        if (tp.idEqualsPath(currFolderId, folderId)) {
            return;
        }

        var unread = 0;

        var gs = this.gridPanel.getStore(),
            requestArray = [],
            plainIds = [];

        for (var i = 0, max_i = records.length; i < max_i; i++) {
            unread += (records[i].get('isRead') ? 0 : 1);
            plainIds.push(records[i].id);
            requestArray.push({
                id                      : records[i].id,
                groupwareEmailFoldersId : folderId[folderId.length - 1]
            });
        }

        if (max_i == 0) {
            return;
        }

        var allowPendingUpdate = this.allowNodePendingUpdate(currFolderId),
            updatePendingCount = allowPendingUpdate ? 0 : i,
            fromPath = tp.getNodeById(currFolderId).getPathAsArray('idForPath');


        Ext.Ajax.request({
            url: './groupware/email.item/move.items/format/json',
            params: {
                toPath      : Ext.encode(folderId),
                fromPath    : Ext.encode(fromPath),
                itemsToMove : Ext.encode(requestArray)
            }
        });

        gs.bulkRemove(records);


        var pendingStore  = this.treePanel.pendingItemStore;
        var pendingRecord = pendingStore.getById('/' + folderId.join('/'));
        if (pendingRecord) {
            pendingRecord.set('pending', pendingRecord.data.pending+unread);
        }

        pendingRecord = pendingStore.getById(
            this.treePanel.getNodeById(currFolderId).getPath('idForPath')
        );
        if (pendingRecord) {
            pendingRecord.set('pending', pendingRecord.data.pending -
                (allowPendingUpdate ? unread : updatePendingCount));
        }

        Ext.ux.util.MessageBus.publish('conjoon.mail.publicMessage.mailMove', {
            path   : {to : folderId, from : fromPath},
            ids      : plainIds
        });
    },


    /**
     * Shows an informal message if the specified node - which is assumed to be
     * the folder representing the trash bin - is part of a proxy subtree
     *
     * @param {Ext.tree.TreePanel} treePanel The owning treepanel of the node
     * @param {Ext.tree.TreeNode} trashNode the node itself
     *
     * @return {Boolean} true if any info was shown, otherwise false
     *
     * @protected
     */
    showInfoAboutTrashIsProxy : function(treePanel, trashNode) {

        var me = this;

        if (treePanel.getFolderService().isPartOfProxySubtree(trashNode)) {

            if (treePanel.getFolderService().isAnyParentProxyNodeLoading(trashNode)) {
                conjoon.SystemMessage.info({
                    title : com.conjoon.Gettext.gettext("Synchronizing..."),
                    text  : com.conjoon.Gettext.gettext("The mail folders are currently being synchronized. Please wait until this operation has finished.")
                });
            } else {
                conjoon.SystemMessage.confirm({
                    title : com.conjoon.Gettext.gettext("Trash Bin not synchronized yet"),
                    text  : com.conjoon.Gettext.gettext("Before you can delete messages you need to synchronize the trash bin. Do you want to do so now?")
                }, {
                    fn : function(btn) {
                        if (btn === 'yes') {
                            treePanel.getFolderService().loadNextParentProxyNode(trashNode);
                        }
                    },
                    scope : me
                });
            }

            return true;
        }

        return false;
    },

    /**
     * Flags email items as read, collects the neccessary data and sends it to
     * the server for changing the data in the underlying data store.
     *
     */
    setItemsAsRead : function(records, read)
    {
        var currFolderId  = this.clkNodeId;
        var store         = this.gridPanel.getStore();
        var unread        = 0;

        store.suspendEvents();

        var requestArray = new Array();

        var noChange = false;
        for (var i = 0, max_i = records.length; i < max_i; i++) {
            noChange = (records[i].data.isRead == read);
            unread += (noChange
                      ? 0
                      : !read ? 1 : -1);
            records[i].set('isRead', read);

            if (!noChange) {
                requestArray.push({
                    'id'     : records[i].id,
                    'isRead' : read
                });
            }
        }


        if (currFolderId && this.allowNodePendingUpdate(currFolderId)) {
            var pendingStore  = this.treePanel.pendingItemStore;
            var pendingRecord = pendingStore.getById(
                this.treePanel.getNodeById(currFolderId).getPath('idForPath')
            );
            if (pendingRecord) {
                pendingRecord.set('pending', pendingRecord.data.pending+unread);
             }
        }
        if (requestArray.length > 0) {
            Ext.Ajax.request({
                url: './groupware/email.item/set.email.flag/format/json',
                params: {
                    type : 'read',
                    json : Ext.encode(requestArray),
                    path : this.treePanel.getNodeById(currFolderId)
                           .getPathAsJson('idForPath')

                }
            });
        }

        store.resumeEvents();

        store.commitChanges();
    },

    /**
     * Tells wether an update of the pending records entry is needed
     * when an email record was marked read/unread.
     * The method returns false for the draft and outbox folder, as their pending
     * node store usually substitutes the state "pending" for items currently being
     * stored in them.
     *
     */
    allowNodePendingUpdate : function(nodeId)
    {
        var tp = this.treePanel;

        if (tp.isFolderOfType(nodeId, 'draft')) {
            return false;
        }

        if (tp.isFolderOfType(nodeId, 'outbox')) {
            return false;
        }

        return true;
    },

    /**
     * Marks the passed records as spam/nospam based on the second argument and
     * sends out an ajax request to update the corresponding data in the
     * underlying data storage.
     *
     * @param {Array}  records An array of records to change the
     * "isSpam" value for
     * @param {Boolean} spam "true" for being marked as spam, otherwise
     * "false"
     */
    setItemsAsSpam : function(records, spam)
    {
        var requestArray  = [],
            store         = this.gridPanel.getStore(),
            currFolderId  = this.clkNodeId;

        store.suspendEvents();

        for (var i = 0, max_i = records.length; i < max_i; i++) {
            records[i].set('isSpam', spam);
            requestArray.push({
                id     : records[i].id,
                isSpam : spam
            });
        }

        Ext.Ajax.request({
            url: './groupware/email.item/set.email.flag/format/json',
            params: {
                type : 'spam',
                json : Ext.encode(requestArray),
                path : this.treePanel.getNodeById(currFolderId)
                       .getPathAsJson('idForPath')
            }
        });

        store.resumeEvents();

        store.commitChanges();
        this.switchButtonState(max_i, records[0]);
    },

    /**
     * Clears any pending insert operation that will be active when a large
     * amount of new records get added to the folders.
     *
     */
    clearPending : function()
    {
        this.queue = null;
        this.gridPanel.view.un('rowsinserted', this.processQueue, this);

        var pendingStore  = this.treePanel.pendingItemStore;
        var pendingRecord = null;

        for (var i in this.pendingRecords) {
            var pendingRecord = pendingStore.getById(i);
            if (pendingRecord) {
                pendingRecord.set('pending', pendingRecord.data.pending+this.pendingRecords[i]);
            }
        }

        this.pendingRecords = {};
    },

    /**
     * Enables / disables toolbar buttons based on the current selection in the
     * grid panel and the active folder we are in.
     *
     * SpamButton
     * ----------
     * It will be deactivated if the current selected folder is the
     * spam folder.
     * It will be deactivated if no records are selected.
     * It will be deactivated if "1" record is selected and this record is marked
     * as "spam".
     * It will be deactivated if the grid panel's store loads selections
     * and the current selected folder is not the spam folder.
     * It will be activated if the current number of selection is "1"
     * and if the selected record is marked as "no spam" and if the current selected
     * folder is not the spam folder.
     * It will be activated if the current number of selection is
     * larger than "1" and if the grid panel's store does not load selection
     * and if the current selected folder is not the spam folder.
     *
     * NoSpamButton
     * ------------
     * It will be deactivated if no records are selected.
     * It will be deactivated if "1" record is selected and the record is marked
     * as "no spam".
     * It will be deactivated if the selected record(s) need(s) to be requested
     * from the server.
     * It will be activated if "1" record is selected and the record is marked as
     * "spam".
     * It will be activated if more than "1" records are selected and the grid panels
     * store does not load selections.
     *
     *
     */
    switchButtonState : function(count, record)
    {
        if (this.buttonsLocked) {
            return;
        }

        if (count == 0 || record == null) {
            this.sendNowButton.setDisabled(true);
            this.forwardButton.setDisabled(true);
            this.replyButton.setDisabled(true);
            this.replyAllButton.setDisabled(true);
            this.deleteButton.setDisabled(true);
            this.spamButton.setDisabled(true);
            this.noSpamButton.setDisabled(true);
            this.spamButton.setVisible(true);
            this.noSpamButton.setVisible(false);
            this.editDraftButton.setDisabled(true);
        }  else if (count == 1) {
            this.sendNowButton.setDisabled(false);
            this.forwardButton.setDisabled(false);
            this.replyButton.setDisabled(false);
            this.replyAllButton.setDisabled(false);
            this.editDraftButton.setDisabled(false);
        } else if (count > 1) {
            this.sendNowButton.setDisabled(true);
            this.forwardButton.setDisabled(true);
            this.replyButton.setDisabled(true);
            this.replyAllButton.setDisabled(true);
            this.editDraftButton.setDisabled(true);
        }

        var tp = this.treePanel;

        /**
         * @todo better check if folders are loaded
         */
        if (!tp.folderDraft) {
            //return;
        }

        var isDrafts = tp.isFolderOfType(this.clkNodeId, 'draft');

        this.editDraftButton.setVisible(isDrafts);

        var isSpam = record ? record.data.isSpam : false;

        var isSendable = tp.isFolderOfType(this.clkNodeId, 'outbox');

        var isNotSpammable = (!record) || (isDrafts) ||
                             (tp.isFolderOfType(this.clkNodeId, 'sent')) ||
                             (isSendable);

        this.spamButton.setDisabled(isNotSpammable || isSpam);
        this.noSpamButton.setDisabled(isNotSpammable || !isSpam);
        this.spamButton.setVisible(!isSpam);
        this.noSpamButton.setVisible(isSpam);

        this.sendNowButton.setVisible(isSendable);

        this.deleteButton.setDisabled((record == null));
    },


    requestEmailRecord : function(record)
    {
        if (!this.preview.isVisible()) {
            return;
        }

        if (!record) {
            this.preview.setEmailItem(null);
        }

        var emailRecord = this.preview.emailRecord;

        if (emailRecord && record.id == emailRecord.id) {
            this.preview.renderView();
            return;
        }

        this.preview.setEmailItem(record);
    },

    hidePreview : function(btn, evt)
    {
        var right  = Ext.getCmp('com-conjoon-groupware-email-rightPreview');
        var bot    = Ext.getCmp('com-conjoon-groupware-email-bottomPreview');
        var button = this.previewButton;

        // trigger state save for the button since it will take care of checked menu items
        button.saveState();

        if (btn instanceof Ext.Toolbar.SplitButton) {
            if (btn.pressed) {
                var previewMenu = Ext.menu.MenuMgr.get('com.conjoon.groupware.email.emailPreviewMenu');
                var items = previewMenu.items.items;
                var b = items[0], r = items[1], h = items[2];

                if (b.checked){
                    b.setChecked(false, true);
                    b.setChecked(true);
                    button.setIconClass(
                        'com-conjoon-groupware-email-EmailPanel-toolbar-previewBottomButton-icon'
                    );
                } else if(r.checked){
                    r.setChecked(false, true);
                    r.setChecked(true);
                    button.setIconClass(
                        'com-conjoon-groupware-email-EmailPanel-toolbar-previewRightButton-icon'
                    );
                } else if(h.checked){
                    //h.setChecked(false, true);
                    b.setChecked(true);
                    button.setIconClass(
                        'com-conjoon-groupware-email-EmailPanel-toolbar-previewBottomButton-icon'
                    );
                }
                return;

            } else {
                bot.hide();
                right.hide();
                this.preview.hide();
                this.ownerCt.doLayout();
                button.setIconClass(
                    'com-conjoon-groupware-email-EmailPanel-toolbar-previewHideButton-icon'
                );
                return;
            }
        } else {
            var previewMenu = Ext.menu.MenuMgr.get('com.conjoon.groupware.email.emailPreviewMenu');
            var items = previewMenu.items.items;
            var b = items[0], r = items[1], h = items[2];
            if (b.checked) {
                button.toggle(true);
                right.hide();
                this.preview.hide();
                bot.add(this.preview);
                bot.show();
                this.ownerCt.doLayout();
                this.preview.show();
                button.setIconClass(
                    'com-conjoon-groupware-email-EmailPanel-toolbar-previewBottomButton-icon'
                );
                return;
            } else if (r.checked) {
                button.toggle(true);
                bot.hide();
                this.preview.hide();
                right.add(this.preview);
                right.show();
                this.ownerCt.doLayout();
                button.setIconClass(
                    'com-conjoon-groupware-email-EmailPanel-toolbar-previewRightButton-icon'
                );
                this.preview.show();
                return;
            } else if (h.checked) {
                button.toggle(false);
                bot.hide();
                right.hide();
                this.preview.hide();
                button.setIconClass(
                    'com-conjoon-groupware-email-EmailPanel-toolbar-previewHideButton-icon'
                );
                this.ownerCt.doLayout();
                return;
            }
        }
    },

// ------------------------------ Listeners ------------------------------------

    /**
     * Listens to the grid view's reset event and adds the
     * groupwareEmailFoldersId to the list of params to be passed to the store.
     * If, and only if forceReload is not set to false and params is a
     * configuration object.
     *
     * @param {Ext.ux.grid.livegrid.GridView} view
     * @param {Boolean} forceReload
     * @param {Object} params
     */
    onGridPanelViewReset : function(view, forceReload, params)
    {
        if (!forceReload || !params) {
            return;
        }

        var obj = this.clkNodeId
                  ? {
                    groupwareEmailFoldersId : this.clkNodeId,
                    path                    : this.treePanel.getNodeById(
                                                  this.clkNodeId
                                              ).getPathAsJson('idForPath')
                  }
                  : {};

        Ext.applyIf(params, obj);
    },

    /**
     * Listener for a rowdblclick in the grid. Will open an new tab containing
     * the message, uneditable.
     *
     */
    openEmailView : function()
    {
        var sm = this.gridPanel.selModel;
        var c  = sm.getCount();

        if (c != 1) {
            return;
        }

        var record = sm.getSelected();

        //var id = record.id;

        com.conjoon.groupware.email.EmailViewBaton.showEmail(record);
    },

    /**
     * Calls to this function are being made from the toolbar buttons, the contextmenu
     * or any other control when an email has to be send that is currently pending
     * in the outbox folder
     *
     */
    sendPendingItems : function()
    {
        var sm = this.gridPanel.selModel;
        var c  = sm.getCount();

        var record = sm.getSelected();
        if (c != 1 || !record) {
            return;
        }

        com.conjoon.groupware.email.Dispatcher.sendPendingEmails(
            [record],
            Math.floor(((new Date()).getTime()/1000))
        );
    },

    /**
     * Calls to this function are being made from the toolbar buttons, the contextmenu
     * or any other control when an email has to be created, edited or replied to.
     * if loadDraft equals to true, the selected record in the grid will be
     * fetched, the id read and then passed to the EmailEditorManager's function
     * createEditor.
     * The type argument tells what kind of edit mode this is. Valid values are
     * new - a new email has to be created
     * edit - a draft has to be edited
     * reply - a reply of an email has to be created
     * reply_all - a reply-all to an email has to be created
     * forward - the selected email needs to be forwarded
     *
     */
    openEmailEditPanel : function(loadDraft, type)
    {
        var sm = this.gridPanel.selModel;
        var c  = sm.getCount();

        if (!loadDraft) {
            com.conjoon.groupware.email.EmailEditorManager.createEditor();
        } else {
            var record = sm.getSelected();
            if (c != 1 || !record) {
                return;
            }
            com.conjoon.groupware.email.EmailEditorManager.createEditor(record, type);
        }
    },

    onQuickPanelUpdate : function(store, record, operation)
    {
        if (operation == 'commit') {
            var myStore = this.gridPanel.getStore();
            myStore.suspendEvents();
            var rec     = myStore.getById(record.id);
            var read    = record.data.isRead;
            var up      = read ? -1 : 1;
            if (rec) {
                up = rec.data.isRead == read
                      ? 0
                      : !read ? 1 : -1;
                rec.set('isRead', read);
            }

            var pendingStore  = this.treePanel.pendingItemStore;
            var pendingRecord = pendingStore.getById(
                this.treePanel.assemblePath(record.data.path)
            );
            if (pendingRecord) {
                pendingRecord.set('pending', pendingRecord.data.pending+up);
            }
            myStore.resumeEvents();
            myStore.commitChanges();
        }
    },

    /**
     * Listener for a load failure of the email panel
     */
    onEmailLoadFailure : function(response, options)
    {
        com.conjoon.groupware.ResponseInspector.handleFailure(response, {
            onLogin: {
                fn : function(){
                    this.preview.load();
                },
                scope : this
            }
        });
    },

    /**
     * Listener for the message with the subject
     * 'com.conjoon.groupware.email.view.onEmailLoad'
     *
     * @param {String} subject
     * @param {Object} message
     *
     */
    onEmailLoad : function(subject, message)
    {
        var emailRecord = message.emailRecord,
            id          = emailRecord.id,
            store       = this.gridPanel.store,
            rec         = store.getById(id),
            rec         = rec
                          ? rec
                          : store.getById(emailRecord.get('uId'));

        if (rec) {
            this.setItemsAsRead([rec], true);
        }
    },

    /**
     * Called when the Ext.ux.util.MessageBus publishes the
     * com.conjoon.groupware.email.LatestEmailCache.clear message.
     *
     * @param {String} subject
     * @param {Object} message
     *
     */
    _onLatestCacheClear : function(subject, message)
    {
        var itemIds = message.itemIds;
        var len     = itemIds.length;

        if(len == 0) {
            return;
        }

        var store = this.gridPanel.getStore();
        var view  = this.gridPanel.getView();
        var rec   = null;

        for (var i = 0; i < len; i++) {
            rec = store.getById(itemIds[i]);
            if (rec) {
                view.refreshRow(rec);
            }
        }
    },

    /**
     * Callback if an email was moved to the outbox folder.
     * The moved email was either created from a new draft or loaded from an existing
     * draft.
     * In either way, the pending record count from the outbox-folder has to be
     * updated.
     * If the currently opened folder is the outbox folder, create a new record based
     * on the passed data and add it to the store.
     *
     * @param {String} subject The subject of the message
     * @param {Object} data The message's data.
     */
    _onMoveOutbox : function(subject, message)
    {
        var tp = this.treePanel;

        /**
         * @todo check if the tree is visible. Return if that is not the case.
         * This needs to be enhanced
         */
        if (!tp.folderDraft) {
            //return;
        }

        var draft        = message.draft;
        var itemRecord   = message.itemRecord;
        var currFolderId = this.clkNodeId;
        var store        = this.gridPanel.getStore();
        var pendingStore = tp.pendingItemStore;
        var pathDecode   = Ext.decode(draft.get('path'));

        // update pending count of drafts if the message was in the draft folder
        if (tp.isPathOfType(pathDecode, 'draft')) {

            var pendingRecord = pendingStore.getById(tp.assemblePath(pathDecode));
            if (pendingRecord) {
                pendingRecord.set('pending', pendingRecord.data.pending-1);
            }

            // remove record if draft was currently visible
            // the referenced item will be in any case the itemrecord from
            // the draft folder, since the draft itself was from the
            // draftFolder
            if (tp.isFolderOfType(currFolderId, 'draft')) {
                store.remove(message.referencedItem);
            }
        }

        // pending count will be updated in every case
        var pendingRecord = pendingStore.getById(tp.assemblePath(itemRecord.get('path')));
        if (pendingRecord) {
            pendingRecord.set('pending', pendingRecord.data.pending+1);
        }

        // create a record for the current grid if and only if
        // the currently opened folder equals to the folder the email
        // was moved to
        if (tp.isFolderOfType(currFolderId, 'outbox')) {
            var index = store.findInsertIndex(itemRecord);
            store.insert(index, itemRecord.copy());
        }
    },

    /**
     * Observer for the message com.conjoon.groupware.email.Smtp.bulkSent.
     *
     * @param {String} Subject
     * @param {Object} message
     */
    _onBulkSent : function(subject, message)
    {
        var emailItems   = message.emailItems;
        var contextReferencedItems = message.contextReferencedItems;
        var sentItems    = message.sentItems;
        var currFolderId = this.clkNodeId;
        var tp           = this.treePanel;
        var pendingStore = tp.pendingItemStore;
        var store        = this.gridPanel.getStore();
        var newVersions  = message.newVersions;

        var notSent = [];

        var found = false;
        for (var a = 0, lena = emailItems.length; a < lena; a++) {
            found = false;
            for (var i = 0, len = sentItems.length; i < len; i++) {
                if (sentItems[i].get('id') == emailItems[a].get('id')) {
                    found = true;
                    break;
                }
            }
            if (!found && !newVersions[emailItems[a].get('id')]) {
                notSent.push(emailItems[a]);
            }
        }

        // update pending count in any case
        // outbox
        var pendingRecord;
        if (notSent[0]) {
            pendingRecord = pendingStore.getById(tp.assemblePath(notSent[0].get('path')));
        }

        if (pendingRecord) {
            pendingRecord.set('pending', pendingRecord.data.pending+notSent.length);
        }

        // check which folder is currently visible and insert records
        var ind = 0;
        if (tp.isFolderOfType(currFolderId, 'sent')) {
            for (var i = 0, len = sentItems.length; i < len; i++) {
                ind = store.findInsertIndex(sentItems[i]);
                store.insert(ind, sentItems[i].copy());
            }
        } else if (tp.isFolderOfType(currFolderId, 'outbox')) {
            for (var i = 0, len = notSent.length; i < len; i++) {
                ind = store.findInsertIndex(notSent[i]);
                store.insert(ind, notSent[i].copy());
            }
        }

        // update the context referenced items
        if (contextReferencedItems) {
            store.suspendEvents();
            var contextReferencedItem = null;
            for (var i = 0, len = contextReferencedItems.length; i < len; i++) {
                contextReferencedItem = contextReferencedItems[i];
                var refRecord = store.getById(contextReferencedItem.id);
                if (refRecord) {
                    refRecord.set('referencedAsTypes', '');
                    refRecord.set('referencedAsTypes', contextReferencedItem.get('referencedAsTypes'));
                }
            }
            store.resumeEvents();
            store.commitChanges();
        }

    },

    /**
     * Observer for the message com.conjoon.groupware.email.Smtp.bulkSentFailure.
     *
     * @param {String} Subject
     * @param {Object} message
     */
    _onBulkSentFailure : function(subject, message)
    {
        var currFolderId = this.clkNodeId;
        var tp           = this.treePanel;
        var pendingStore = tp.pendingItemStore;
        var store        = this.gridPanel.getStore();

        var emailItems = message.emailItems;
        var length     = emailItems.length;

        if (tp.isFolderOfType(currFolderId, 'outbox')) {
            var ind = 0;
            for (var i = 0; i < length; i++) {
                ind = store.findInsertIndex(emailItems[i]);
                store.insert(ind, emailItems[i].copy());
            }
        }

        // update pending count in any case
        var pendingRecord = pendingStore.getById(tp.assemblePath(emailItems[0].get('path')));
        if (pendingRecord) {
            pendingRecord.set('pending', pendingRecord.data.pending+length);
        }
    },

    /**
     * Observer for the message com.conjoon.groupware.email.Smtp.beforeBulkSent.
     *
     * @param {String} Subject
     * @param {Object} message
     */
    _onBeforeBulkSent : function(subject, message)
    {
        var currFolderId = this.clkNodeId;
        var tp           = this.treePanel;
        var pendingStore = tp.pendingItemStore;
        var store        = this.gridPanel.getStore();

        var emailItems = message.emailItems;
        var length     = emailItems.length;

        if (tp.isFolderOfType(currFolderId, 'outbox')) {
            store.bulkRemove(emailItems);
        }

        // update pending count in any case
        var pendingRecord = pendingStore.getById(tp.assemblePath(emailItems[0].get('path')));
        if (pendingRecord) {
            pendingRecord.set('pending', pendingRecord.data.pending-length);
        }
    },

    /**
     * Callback if an email was successfully send. Listens to messages with the subject
     * "com.conjoon.groupware.email.Smtp.emailSent" as published by Ext.ux.util.MessageBus.
     *
     * @param {String} subject The subject of the message
     * @param {Object} data The message's data. For the event this listener observes, the
     * object will provide the following properties:
     * draft - com.conjoon.groupware.email.data.Draft
     * itemRecord - com.conjoon.groupware.email.EmailItemRecord
     * referencedItem - com.conjoon.groupware.email.EmailItemRecord
     * options - Object
     */
    _onSendEmail : function(subject, message)
    {
        var referencedRecord      = message.referencedItem;
        var contextReferencedItem = message.contextReferencedItem;

        var emailRecord = message.itemRecord;
        var draft       = message.draft;
        var oldId       = -1;
        var oldFolderPath = -1;
        var type        = draft.get('type');

        var tp           = this.treePanel;
        var currFolderId = this.clkNodeId;
        var store        = this.gridPanel.getStore();
        var pendingStore = tp.pendingItemStore;

        if (referencedRecord) {
            oldId         = referencedRecord.get('id');
            oldFolderPath = referencedRecord.get('path');
        }

        // check if the grid with the record for old id is open. Update the specific record
        // with the reference type, if message.type equals to forward, reply or reply_all
        if (contextReferencedItem) {
            // check if the referenced item's path equals to the opened folder
            var path = contextReferencedItem.get('path'),
                refPath = path.join('/');
            if (path[0] != 'root') {
                refPath = '/root/' + refPath;
            }
            var node = this.treePanel.getNodeById(this.clkNodeId);

            if (node && node.getPath('idForPath') == refPath) {
                // currently viewing the referenced item's folder
                var refRecord = store.getById(contextReferencedItem.id);
                if (refRecord) {
                    store.suspendEvents();
                    refRecord.set('referencedAsTypes', '');
                    refRecord.set(
                        'referencedAsTypes',
                        contextReferencedItem.get('referencedAsTypes')
                    );
                    store.resumeEvents();
                    store.commitChanges();
                }
            }
        }

        // if the email was loaded from outbox/draft and sent, update pending nodes
        // minus 1, but only if the id of the itemRecord equals to the id of the draft,
        // which will basically tell that an email pending in the outbox folder was sent
        if (referencedRecord
            && (
            (tp.isPathOfType(oldFolderPath, 'outbox') || tp.isPathOfType(oldFolderPath, 'draft'))
            &&
            ((!message.newVersion
               && emailRecord
               && emailRecord.get('id') == referencedRecord.get('id'))
             ||
             (message.newVersion
              && message.previousId == referencedRecord.get('id'))
            )

            )) {
            // if grid is visible, remove the record with the specified id!
            if (tp.idEqualsPath(currFolderId, oldFolderPath)) {
                store.remove(referencedRecord);
            }
            // update pending count in any case
            var pendingRecord = pendingStore.getById(tp.assemblePath(referencedRecord.get('path')));
            if (pendingRecord) {
                pendingRecord.set('pending', pendingRecord.data.pending-1);
            }
        }

        // if the visible grid is the grid for sent items, add the recod to the store
        // use emailfolders id first, then path
        if (emailRecord && emailRecord.get('groupwareEmailFoldersId') > -1 &&
            emailRecord.get('groupwareEmailFoldersId') == currFolderId) {
            var index = store.findInsertIndex(emailRecord);
            store.insert(index, emailRecord.copy());
        } else if (emailRecord && emailRecord.get('path') && emailRecord.get('path').length > 0) {
            var parts = emailRecord.get('path'),
                newPath = [], i = 0, len = parts.length,
                node, path, index, newRecord;
            for (; i < len; i++) {
                newPath.push(parts[i]);
            }
            if (newPath[0] == 'root') {
                newPath.shift();
            }

            path = '/root/' + newPath.join('/');

            node = this.treePanel.getNodeById(this.clkNodeId);

            if (node && node.getPath('idForPath') == path) {
                index = store.findInsertIndex(emailRecord);
                newRecord = emailRecord.copy();
                newRecord.set('path', newPath);
                store.insert(index, newRecord);
            }
        }
    },

    /**
     * Callback if a draft was successfully saved. Listens to messages with the subject
     * "com.conjoon.groupware.email.editor.draftSaved" as published by Ext.ux.util.MessageBus.
     *
     * @param {String} subject The subject of the message
     * @param {Object} data The message's data.
     */
    _onSaveDraft : function(subject, message)
    {
        var tp = this.treePanel;

        /**
         * @todo check if the tree is visible. Return if that is not the case.
         * This needs to be enhanced
         */
        if (!tp.folderDraft) {
            //return;
        }

        var referencedRecord = message.referencedItem,
            oldDraftId    = message.draft.get('id'),
            newVersion    = message.newVersion,
            previousId     = message.previousId,
            oldFolderPath = message.path,
            itemRecord    = message.itemRecord,
            newFolderPath = itemRecord.get('path'),
            currFolderId  = this.clkNodeId,
            store         = this.gridPanel.getStore(),
            pendingStore  = tp.pendingItemStore;

        // the draft was moved while saving from one draft folder to another
        // if and only if the oldDraftId does not equal to anything but -1
        // first off, check whether the folders are the same or not.
        // if they differ, try to remove the record out of oldFolderId
        // and add the new record to the new folder, but only if the oldDraftId
        // was anything but <= 0
        if (tp.pathEqualsPath(oldFolderPath, newFolderPath) && oldDraftId > 0) {
            // check if visible
            // remove the record and update pending nodes
            if (tp.idEqualsPath(currFolderId, oldFolderPath)) {
                if (referencedRecord) {
                    store.remove(referencedRecord);
                }
            }

            var pendingRecord = pendingStore.getById(tp.assemblePath(oldFolderPath));
            if (pendingRecord) {
                pendingRecord.set('pending', pendingRecord.data.pending-1);
            }
        }

        // if the oldDraftId is equal to the new draft id, we do not need to update
        // the pending count
        if (!newVersion && oldDraftId != itemRecord.id) {
            var pendingRecord = pendingStore.getById(tp.assemblePath(itemRecord.get('path')));
            if (pendingRecord) {
                pendingRecord.set('pending', pendingRecord.data.pending+1);
            }
        }

        if (tp.idEqualsPath(currFolderId, newFolderPath)) {
            this._replaceAndRefreshIfNeeded(referencedRecord, itemRecord, newVersion);
        }
    },

    onPanelRender : function()
    {
        com.conjoon.groupware.workbench.ToolbarController.show('com.conjoon.groupware.email.Toolbar');

        var sub = com.conjoon.util.Registry.get('com.conjoon.groupware.email.QuickPanel');

        if (sub) {
            sub.store.un('update', this.onQuickPanelUpdate, this);
            sub.store.on('update', this.onQuickPanelUpdate, this);
        }
    },

    /**
     *
     * tree - The TreePanel
     * target - The node being targeted for the drop
     * data - The drag data from the drag source
     * point - The point of the drop - append, above or below
     * source - The drag source
     * rawEvent - Raw mouse event
     * dropNode - Dropped node(s).
     */
    onNodeDrop : function(dropEvent)
    {
        var source = dropEvent.source;
        if (source instanceof Ext.ux.grid.livegrid.DragZone) {
            this.moveEmails(dropEvent.data.selections, dropEvent.target.id);
        }
    },

    /**
     *
     */
    onBeforeSelectionsLoad : function()
    {
        this.buttonsLocked = true;

        this.deleteButton.setDisabled(true);
        this.spamButton.setDisabled(true);
        this.noSpamButton.setDisabled(true);

        this.deleteButton.setIconClass('com-conjoon-groupware-selectionsLoading');
        this.spamButton.setIconClass('com-conjoon-groupware-selectionsLoading');
        this.noSpamButton.setIconClass('com-conjoon-groupware-selectionsLoading');
    },

    /**
     *
     */
    onSelectionsLoad : function()
    {
        this.buttonsLocked = false;

        this.deleteButton.setIconClass('com-conjoon-groupware-email-EmailPanel-toolbar-deleteButton-icon');
        this.spamButton.setIconClass('com-conjoon-groupware-email-EmailPanel-toolbar-spamButton-icon');
        this.noSpamButton.setIconClass('com-conjoon-groupware-email-EmailPanel-toolbar-noSpamButton-icon');

        this.enableActionButtonsBasedOnSelections();
    },

    /**
     * Callback for the "show" event of the preview panel.
     * Will load the current selected record into the Panel if it gets shown,
     *
     * @param {Ext.Panel} panel
     *
     */
    _onPreviewShow : function(panel)
    {
        var sm = this.gridPanel.getSelectionModel();

        var count = sm.getCount();

        if (count == 0 || count > 1) {
            this.requestEmailRecord(null);
            return;
        }

        var rec = sm.getSelected();

        if (rec) {
            this.requestEmailRecord(rec);
        }
    },

    /**
     * Callback when a record in the grid panel is selected. Will update the
     * email view accordingly and the grid's toolbar.
     *
     */
    onRowSelect : function(sm, index, record)
    {
        var count = sm.getCount();

        if (count > 1) {
            this.switchButtonState(count, record == null ? sm.getSelected() : record);
            this.preview.clearView();
            return;
        }

        this.switchButtonState(count, record);
        this.requestEmailRecord(record);
    },

    onRowDeselect : function(sm, index, record)
    {
        var count = sm.getCount();

        if (count == 0) {
            this.switchButtonState(0, null);
            this.requestEmailRecord(null);
            return;
        }

        if (count == 1) {
            var rec = sm.getSelected();
            this.switchButtonState(1, rec);
            this.requestEmailRecord(rec);
        }
    },

    onRowRemoved : function(view, index, record)
    {
        this.switchButtonState(0, null);

        if (!record) {
            return;
        }

        var rec = this.preview.emailItem;

        if (rec && rec.id == record.id) {
            this.requestEmailRecord(null);
        }
    },


    onGridStoreClear : function()
    {
        this.switchButtonState(0, null);
        this.requestEmailRecord(null);
    },

    onBeforeBuffer : function()
    {
        this.disableActionButtons();

        this.clearPending();
    },

    /**
     * Forces all buttons related to actions depending on some sort of selection
     * in the grid to be disabled. To enable the buttons again, a call to
     * enableActionButtonsBasedOnSelections should be made which automatically
     * anables the buttons based on the folder being viewed and the type of
     * messages selected.
     *
     * @see enableActionButtonsBasedOnSelections
     */
    disableActionButtons : function()
    {
        this.sendNowButton.setDisabled(true);
        this.replyButton.setDisabled(true);
        this.replyAllButton.setDisabled(true);
        this.forwardButton.setDisabled(true);
        this.deleteButton.setDisabled(true);
        this.spamButton.setDisabled(true);
        this.noSpamButton.setDisabled(true);
        this.editDraftButton.setDisabled(true);
    },

    /**
     *
     */
    enableActionButtonsBasedOnSelections : function()
    {
        var sm = this.gridPanel.selModel;
        var c  = sm.getCount();
        this.switchButtonState(c, sm.getSelected());
    },

    onGridStoreBeforeLoad : function(store, options)
    {
        this.requestEmailRecord(null);

        this.clearPending();

        if (this.clkNodeId == null) {
            return false;
        } else if (!this.treePanel.getNodeById(this.clkNodeId).attributes.isSelectable) {
            return false;
        }

        this.disableActionButtons();
    },


    onNodeSelectionChange : function(selModel, node)
    {
        this.switchButtonState(0, null);

        var me = this,
            attr = node && node.attributes
                   ? node.attributes
                   : false,
            gridPanel = me.gridPanel;

        if (attr !== false
            && (attr.type != 'root_remote'
                && attr.type != 'root'
                && attr.type != 'accounts_root')) {
            this.clkNodeId = node.id;

            this.previewButton.show();
            this.centerPanel.getLayout().setActiveItem(1);

            if (this.clkNodeId != this.lastClkNodeId) {
                var proxy = gridPanel.store.proxy;
                var ar    = proxy.activeRequest[Ext.data.Api.actions.read];
                if (ar) {
                    proxy.getConnection().abort(ar);
                }
                gridPanel.selModel.clearSelections(true);
                gridPanel.store.removeAll();

                if (!attr.isSelectable && ar) {
                    gridPanel.loadMask.hide();
                }

                if (attr.isSelectable) {

                    gridPanel.installStateEvents(false);
                    gridPanel.applyState(
                        Ext.state.Manager.get(gridPanel.stateId),
                        this.clkNodeId
                    );
                    gridPanel.installStateEvents(true);
                }

                gridPanel.view.reset((attr.isSelectable ? true : false));

                this.lastClkNodeId = this.clkNodeId;
            }

            return;
        }

        this.lastClkNodeId = this.clkNodeId;
        this.clkNodeId = null;

        this.previewButton.hide();
        this.centerPanel.getLayout().setActiveItem(0);
    },

    onNodeRemove : function(tree, parent, node)
    {
        if (node.id == this.clkNodeId) {
            this.clkNodeId = null;
            this.gridPanel.loadMask.hide();
            var proxy = this.gridPanel.store.proxy;
            if (proxy.activeRequest[Ext.data.Api.actions.read]) {
                proxy.getConnection().abort(proxy.activeRequest[Ext.data.Api.actions.read]);
            }
            this.gridPanel.store.removeAll();
        }
    },

    onMoveNode : function()
    {
        if (this.loadingMask) {
            this.loadingMask.hide();
        }

        this.preview.clearView();
    },


    proxyInsert : function(index, record)
    {
        if (this.queue == null) {
            return;
        }
        var ds    = this.gridPanel.store;
        var index = ds.findInsertIndex(record);

        ds.insert(index, record);
    },

    processQueue : function()
    {
        if (!this.queue) {
            return;
        }
        var record   = this.queue.shift();

        if (!record) {
            this.queue = null;
            this.gridPanel.view.un('rowsinserted', this.processQueue, this);
            return;
        }

        var folderId = record.data.groupwareEmailFoldersId;
        var folderPath = record.data.path;
        var tp = this.treePanel;

        if (this.lastClkNodeId && (tp.idEqualsPath(this.lastClkNodeId, folderPath)
            && !this.gridPanel.store.proxy.activeRequest[Ext.data.Api.actions.read])) {
            var pendingStore  = tp.pendingItemStore;
            var pendingRecord = pendingStore.getById(tp.assemblePath(folderPath));
            if (pendingRecord) {
                pendingRecord.set('pending', pendingRecord.data.pending+1);
                this.pendingRecords[tp.getNodeById(this.lastClkNodeId).getPath('idForPath')]--;
            }
            var ds = this.gridPanel.store;
            var index = 0;
            this.proxyInsert.defer(0.00001, this, [index, record]);
        } else {
            this.processQueue.defer(0.00001, this);
        }


    },

    /**
     * Updates the pendning node of a folder only if the current timestamp is
     * greater than the timestamp stored within the according record of the
     * pending store.
     *
     * @param {Ext.data.Store} store
     * @param {Object} options
     */
    _updatePendingFromLoad : function(store, options)
    {
        var pendingItems = store.pendingItems;

        if (pendingItems == -1) {
            return;
        }

        var tp = this.treePanel;

        // only update pending records if the last updated timestamp is less than
        // the actual timestamp
        var ts = (new Date()).getTime();

        var folderPath = tp.assemblePath(Ext.decode(options.params.path));

        if (this.pendingRecordsDate[folderPath] > ts) {
            return;
        }

        this.pendingRecordsDate[folderPath] = ts;

        var pendingStore  = this.treePanel.pendingItemStore;
        var pendingRecord = pendingStore.getById(folderPath);

        if (pendingRecord) {
            pendingRecord.set('pending', pendingItems);
        }
    },

    onStoreLoad : function(store, records, options)
    {
        this._updatePendingFromLoad(store, options);
        /**
         * @note this is not needed since reloading the grid will also refresh
         * the grid with a call to the GridView's refresh() method, which in
         * turn triggers the refresh event. The default implementation for the
         * refresh listener of the RowSelectionModel in Ext 3.4 will re-select
         * the rows and then call switchButtonState()
         * Also note, that this event will only fire on those rows selected
         * which are in the store once the grid is refreshed. If those
         * selections are not in the store, the buttons' state will not be
         * reset and instead kept disabled
         */
        //this.enableActionButtonsBasedOnSelections();
    },

    onStoreBuffer : function(view, store, rowIndex, visibleRows, totalCount, options)
    {
        this._updatePendingFromLoad(store, options);
        this.enableActionButtonsBasedOnSelections();
    },

    /**
     * Called when a pending record has been added to the treepanel's pending
     * item's store.
     * Inits the date-object with the date-property of the added records.
     *
     * @param {Object} store
     * @param {Array} records
     * @param {number} index
     */
    onPendingStoreAdd : function(store, records, index)
    {
        var tp = this.treePanel, id;

        for (var i = 0, len = records.length; i < len; i++) {

            id = records[i].id;

            this.pendingRecordsDate[id] = (new Date()).getTime();
        }
    },

    /**
     * Called by the letterman when new emails have arrived.
     * The letetrman will be responsible for
     *
     */
    newEmailsAvailable : function (store, records, options)
    {
        if (!this.queue) {
            this.queue = [];
        }

        this.pendingRecords = {};

        var folderId = null;
        var tp = this.treePanel;

        this.gridPanel.view.un('rowsinserted', this.processQueue, this);
        this.gridPanel.view.on('rowsinserted', this.processQueue, this);

        var ts = (new Date()).getTime();
        for (var i = 0, max_i = records.length; i < max_i; i++) {

            folderId = tp.assemblePath(records[i].data.path);

            if (this.pendingRecordsDate[folderId] < store.lastLoadingDate) {
                if (!this.pendingRecords[folderId]) {
                    this.pendingRecords[folderId] = 1;
                } else {
                    this.pendingRecords[folderId]++;
                }
            }

            this.queue.push(records[i].copy());
        }

        var pendingStore  = this.treePanel.pendingItemStore;
        var pendingRecord = null;

        for (var folderId in this.pendingRecords) {
            this.pendingRecordsDate[folderId] = ts;
            if (!tp.idEqualsPath(this.lastClkNodeId, folderId)) {
                pendingRecord = pendingStore.getById(folderId);
                if (pendingRecord) {
                    pendingRecord.set('pending', pendingRecord.data.pending+this.pendingRecords[folderId]);
                    this.pendingRecords[folderId] = 0;
                }
            }
        }

        if (this.gridPanel.store.proxy.activeRequest[Ext.data.Api.actions.read]) {
            this.clearPending();
            return;
        }

        this.processQueue();
    },

    /**
     * Helper function for the varios editor-messages published by the MessageBus.
     * The method will basically change an old record from th grid with a new one,
     * such as after a draft has been edited and changed again. The Grid's view will
     * only be refreshed if one of the records is in the visible rect.
     *
     * @param {Ext.data.Record} referencedRecord The record thazt was used for editing
     * and is not up to date anymore due to changes made.
     * @param {Ext.data.Record} itemRecord The new version of referencedRecord, after
     * editing
     * @param {Boolean} newVersion whether the itemRecord is a new version of the
     *                  referencedRecord, with a new id
     *
     * @private
     */
    _replaceAndRefreshIfNeeded : function(referencedRecord, itemRecord, newVersion)
    {
        var store    = this.gridPanel.getStore();
        var itemCopy = itemRecord.copy();
        var ind      = store.findInsertIndex(itemCopy);

        // silently remove the old record, then insert the new one.
        // Afterwards, refresh the grid
        // we need to repaint the visible rect of the grid if the
        // referencedRecord is currently in the visible rect
        // only use the referenced record if the referencedRecord id equals to the
        // id of the itemRecord. Then the method has to update an existing item in the grid
        if (referencedRecord && (referencedRecord.id == itemCopy.id || newVersion)) {
            var wasSelected = false,
                view        = this.gridPanel.getView(),
                refresh     = view.isRecordRendered(referencedRecord),
                selModel    = this.gridPanel.getSelectionModel();

            // programmatic call to switchButtonState since the store's event are
            // suspended
            if (selModel.isSelected(referencedRecord)) {
                selModel.suspendEvents();
                selModel.deselectRecord(referencedRecord);
                selModel.resumeEvents();
                wasSelected = true;
            }

            store.suspendEvents();
            store.remove(referencedRecord);
            store.insert(ind, itemCopy);
            store.resumeEvents();

            if (refresh || view.isRecordRendered(itemCopy)) {
                view.refresh(false);
            }

            if (wasSelected) {
                if (ind != Number.MIN_VALUE && ind != Number.MAX_VALUE) {
                    selModel.selectRow(ind+store.bufferRange[0], false);
                } else {
                    this.switchButtonState(0, null);
                }
            }
        } else {
            store.insert(ind, itemCopy);
        }
    }


});
