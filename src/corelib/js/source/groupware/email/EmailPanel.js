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
        this.treePanel = new com.conjoon.groupware.email.EmailTree({
            region            : 'west',
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
                    region   : 'east',
                    split    : true,
                    hidden   : true,
                    listeners : {
                        render : {
                            fn : function() {
                                var w = Math.round((this.centerPanel.el.getWidth()
                                        - this.treePanel.el.getWidth())/2);
                                Ext.getCmp('com-conjoon-groupware-email-rightPreview')
                                    .width = w;
                            }
                        },
                        scope : this
                    }
            }), new Ext.Container({
                id        : 'com-conjoon-groupware-email-bottomPreview',
                layout    : 'fit',
                items     : this.preview,
                hideMode  : 'offsets',
                split     : true,
                region    : 'south',
                listeners : {
                    render : {
                        fn : function() {
                            var h = Math.round(this.ownerCt.body.getHeight()/2);
                            Ext.getCmp('com-conjoon-groupware-email-bottomPreview')
                                .height = h;
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
                iconCls      : 'com-conjoon-groupware-email-EmailPanel-toolbar-previewBottomButton-icon',
                text         : com.conjoon.Gettext.gettext("bottom"),
                checked      : true,
                group        : 'com.conjoon.groupware.email.emailPreviewGroup',
                checkHandler : this.hidePreview,
                scope        : this
              },{
                iconCls      : 'com-conjoon-groupware-email-EmailPanel-toolbar-previewRightButton-icon',
                text         : com.conjoon.Gettext.gettext("right"),
                checked      : false,
                group        : 'com.conjoon.groupware.email.emailPreviewGroup',
                checkHandler : this.hidePreview,
                scope        : this
              },{
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
     */
    deleteEmails : function(records)
    {

        if (this.clkNodeId && this.treePanel.getNodeById(this.clkNodeId).getPath('type').indexOf('trash') != -1) {
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
            var pendingRecord = pendingStore.getById(this.clkNodeId);
            if (pendingRecord) {
                pendingRecord.set('pending', pendingRecord.data.pending-unread);
            }

            Ext.Ajax.request({
                url: './groupware/email.item/delete.items/format/json',
                params: {
                    itemsToDelete : Ext.encode(requestArray)
                }
            });

            gs.bulkRemove(records);

        } else {
            this.moveEmails(records, this.treePanel.folderTrash.id);
        }
    },

    /**
     * Moves the number of specified records virtually into the folder with
     * the specified fodler-id. If allowNodePendingUpdate returns false, the total
     * number of records will be subtracted from the specified pending node, not just
     * the records that were unread.
     */
    moveEmails : function(records, folderId)
    {
        var currFolderId  = this.clkNodeId;

        if (currFolderId == folderId) {
            return;
        }

        var unread = 0;

        var gs = this.gridPanel.getStore();
        var requestArray = [];

        for (var i = 0, max_i = records.length; i < max_i; i++) {
            unread += (records[i].get('isRead') ? 0 : 1);
            requestArray.push({
                id                      : records[i].id,
                groupwareEmailFoldersId : folderId
            });
        }

        if (max_i == 0) {
            return;
        }

        var allowPendingUpdate = this.allowNodePendingUpdate(currFolderId);
        var updatePendingCount = allowPendingUpdate ? 0 : i;

        Ext.Ajax.request({
            url: './groupware/email.item/move.items/format/json',
            params: {
                itemsToMove : Ext.encode(requestArray)
            }
        });

        gs.bulkRemove(records);

        var pendingStore  = this.treePanel.pendingItemStore;
        var pendingRecord = pendingStore.getById(folderId);
        if (pendingRecord) {
            pendingRecord.set('pending', pendingRecord.data.pending+unread);
        }

        pendingRecord = pendingStore.getById(currFolderId);
        if (pendingRecord) {
            pendingRecord.set('pending', pendingRecord.data.pending - (allowPendingUpdate ? unread : updatePendingCount));
        }
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
            var pendingRecord = pendingStore.getById(currFolderId);
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
        switch (nodeId) {
            case tp.folderDraft.id:
                return false;
            case tp.folderOutbox.id:
                return false;
            default:
                return true;
        }
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
            return;
        }

        var isDrafts = this.clkNodeId == tp.folderDraft.id;

        this.editDraftButton.setVisible(isDrafts);

        var isSpam = record ? record.data.isSpam : false;

        var isSendable = (this.clkNodeId == tp.folderOutbox.id);

        var isNotSpammable = (!record) || (isDrafts) ||
                             (this.clkNodeId == tp.folderSent.id) ||
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

        params = Ext.applyIf(params, obj);
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
            var pendingRecord = pendingStore.getById(record.data.groupwareEmailFoldersId);
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
                          : store.getById(emailRecord.get('messageId'));

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
            return;
        }

        var draft        = message.draft;
        var itemRecord   = message.itemRecord;
        var currFolderId = this.clkNodeId;
        var store        = this.gridPanel.getStore();
        var pendingStore = tp.pendingItemStore;

        // update pending count of drafts if the message was in the draft folder
        if (draft.get('groupwareEmailFoldersId') == tp.folderDraft.id) {
            var pendingRecord = pendingStore.getById(tp.folderDraft.id);
            if (pendingRecord) {
                pendingRecord.set('pending', pendingRecord.data.pending-1);
            }

            // remove record if draft was currently visible
            // the referenced item will be in any case the itemrecord from
            // the draft folder, since the draft itself was from the
            // draftFolder
            if (tp.folderDraft.id == currFolderId) {
                store.remove(message.referencedItem);
            }
        }

        // pending count will be updated in every case
        var pendingRecord = pendingStore.getById(tp.folderOutbox.id);
        if (pendingRecord) {
            pendingRecord.set('pending', pendingRecord.data.pending+1);
        }

        // create a record for the current grid if and only if
        // the currently opened folder equals to the folder the email
        // was moved to
        if (tp.folderOutbox.id == currFolderId) {
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
            if (!found) {
                notSent.push(emailItems[a]);
            }
        }

        // update pending count in any case
        // outbox
        var pendingRecord = pendingStore.getById(tp.folderOutbox.id);
        if (pendingRecord) {
            pendingRecord.set('pending', pendingRecord.data.pending+notSent.length);
        }

        // check which folder is currently visible and insert records
        var ind = 0;
        if (currFolderId == tp.folderSent.id) {
            for (var i = 0, len = sentItems.length; i < len; i++) {
                ind = store.findInsertIndex(sentItems[i]);
                store.insert(ind, sentItems[i].copy());
            }
        } else if (currFolderId == tp.folderOutbox.id) {
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

        if (currFolderId == tp.folderOutbox.id) {
            var ind = 0;
            for (var i = 0; i < length; i++) {
                ind = store.findInsertIndex(emailItems[i]);
                store.insert(ind, emailItems[i].copy());
            }
        }

        // update pending count in any case
        var pendingRecord = pendingStore.getById(tp.folderOutbox.id);
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

        if (currFolderId == tp.folderOutbox.id) {
            store.bulkRemove(emailItems);
        }

        // update pending count in any case
        var pendingRecord = pendingStore.getById(tp.folderOutbox.id);
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
        var oldFolderId = -1;
        var type        = draft.get('type');

        var tp           = this.treePanel;
        var currFolderId = this.clkNodeId;
        var store        = this.gridPanel.getStore();
        var pendingStore = tp.pendingItemStore;

        if (referencedRecord) {
            oldId       = referencedRecord.get('id');
            oldFolderId = referencedRecord.get('groupwareEmailFoldersId');

        }

        // check if the grid with the record for old id is open. Update the specific record
        // with the reference type, if message.type equals to forward, reply or reply_all
        if (contextReferencedItem) {
            var refRecord = store.getById(contextReferencedItem.id);
            if (refRecord) {
                store.suspendEvents();
                refRecord.set('referencedAsTypes', '');
                refRecord.set('referencedAsTypes', contextReferencedItem.get('referencedAsTypes'));
                store.resumeEvents();
                store.commitChanges();
            }
        }

        /**
         * @todo  it is possible that the tree is not fully loaded yet. Thus we
         * check if the folderOutbox.id is available and exit if that is not the
         * case. This should be enhancened when support for one tree per account
         * is available
         */
        if (!tp.folderOutbox) {
            return;
        }

        // if the email was loaded from outbox/draft and sent, update pending nodes
        // minus 1, but only if the id of the itemRecord equals to the id of the draft,
        // which will basically tell that an email pending in the outbox folder was sent
        if (referencedRecord && ((oldFolderId == tp.folderOutbox.id || oldFolderId == tp.folderDraft.id)
            && emailRecord.get('id') == referencedRecord.get('id'))) {
            // if grid is visible, remove the record with the specified id!
            if (currFolderId == oldFolderId) {
                store.remove(referencedRecord);
            }
            // update pending count in any case
            var pendingRecord = pendingStore.getById(oldFolderId);
            if (pendingRecord) {
                pendingRecord.set('pending', pendingRecord.data.pending-1);
            }
        }

        // if the visible grid is the grid for sent items, add the recod to the store
        if (emailRecord.get('groupwareEmailFoldersId') == currFolderId) {
            var index = store.findInsertIndex(emailRecord);
            store.insert(index, emailRecord.copy());
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
            return;
        }

        var referencedRecord = message.referencedItem;

        var oldDraftId   = message.draft.get('id');
        var oldFolderId  = message.groupwareEmailFoldersId;
        var itemRecord   = message.itemRecord;
        var newFolderId  = itemRecord.get('groupwareEmailFoldersId');
        var currFolderId = this.clkNodeId;
        var store        = this.gridPanel.getStore();
        var pendingStore  = tp.pendingItemStore;

        // the draft was moved while saving from one draft folder to another
        // if and only if the oldDraftId does not equal to anything but -1
        // first off, check whether the folders are the same or not.
        // if they differ, try to remove the record out of oldFolderId
        // and add the new record to the new folder, but only if the oldDraftId
        // was anything but <= 0
        if (oldFolderId != newFolderId && oldDraftId > 0) {
            // check if visible
            // remove the record and update pending nodes
            if (currFolderId == oldFolderId) {
                if (referencedRecord) {
                    store.remove(referencedRecord);
                }
            }

            var pendingRecord = pendingStore.getById(oldFolderId);
            if (pendingRecord) {
                pendingRecord.set('pending', pendingRecord.data.pending-1);
            }
        }

        // if the oldDraftId is equal to the new draft id, we do not need to update
        // the pending count
        if (oldDraftId != itemRecord.id) {
            var pendingRecord = pendingStore.getById(newFolderId);
            if (pendingRecord) {
                pendingRecord.set('pending', pendingRecord.data.pending+1);
            }
        }

        if (currFolderId == newFolderId) {
            this._replaceAndRefreshIfNeeded(referencedRecord, itemRecord);
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

        var attr = node && node.attributes
                   ? node && node.attributes
                   : false;

        if (attr !== false
            && (attr.type != 'root_remote'
                && attr.type != 'root'
                && attr.type != 'accounts_root')) {
            this.clkNodeId = node.id;

            this.previewButton.show();
            this.centerPanel.getLayout().setActiveItem(1);

            if (this.clkNodeId != this.lastClkNodeId) {
                var proxy = this.gridPanel.store.proxy;
                var ar    = proxy.activeRequest[Ext.data.Api.actions.read];
                if (ar) {
                    proxy.getConnection().abort(ar);
                }
                this.gridPanel.store.removeAll();

                if (!attr.isSelectable && ar) {
                    this.gridPanel.loadMask.hide();
                }

                this.gridPanel.view.reset((attr.isSelectable ? true : false));

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
        this.clkNodeId = null;
        this.gridPanel.loadMask.hide();
        var proxy = this.gridPanel.store.proxy;
        if (proxy.activeRequest[Ext.data.Api.actions.read]) {
            proxy.getConnection().abort(proxy.activeRequest[Ext.data.Api.actions.read]);
        }
        this.gridPanel.store.removeAll();
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

        if (this.lastClkNodeId && (this.lastClkNodeId == folderId
            && !this.gridPanel.store.proxy.activeRequest[Ext.data.Api.actions.read])) {
            var pendingStore  = this.treePanel.pendingItemStore;
            var pendingRecord = pendingStore.getById(folderId);
            if (pendingRecord) {
                pendingRecord.set('pending', pendingRecord.data.pending+1);
                this.pendingRecords[this.lastClkNodeId]--;
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
        // only update pending records if the last updated timestamp is less than
        // the actual timestamp
        var ts = (new Date()).getTime();
        var folderId = options.params.groupwareEmailFoldersId;

        if (this.pendingRecordsDate[folderId] > ts) {
            return;
        }

        this.pendingRecordsDate[folderId] = ts;

        var pendingStore  = this.treePanel.pendingItemStore;
        var pendingRecord = pendingStore.getById(folderId);

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
        for (var i = 0, len = records.length; i < len; i++) {
            this.pendingRecordsDate[records[i].id] = (new Date()).getTime();
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

        this.gridPanel.view.un('rowsinserted', this.processQueue, this);
        this.gridPanel.view.on('rowsinserted', this.processQueue, this);

        var ts = (new Date()).getTime();
        for (var i = 0, max_i = records.length; i < max_i; i++) {
            folderId = records[i].data.groupwareEmailFoldersId;
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
            if (folderId != this.lastClkNodeId) {
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
     *
     * @private
     */
    _replaceAndRefreshIfNeeded : function(referencedRecord, itemRecord)
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
        if (referencedRecord && referencedRecord.id == itemCopy.id) {
            var wasSelected = false;
            var view        = this.gridPanel.getView();
            var refresh     = view.isRecordRendered(referencedRecord);
            var selModel    = this.gridPanel.getSelectionModel();

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