Ext.namespace('de.intrabuild.groupware.email');

/**
* Controller for the emailpanels tree, preview and grid.
*
*/
de.intrabuild.groupware.email.EmailPanel = function(config) {

    Ext.apply(this, config);

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
    this.treePanel = new de.intrabuild.groupware.email.EmailTree({
        region            : 'west',
		anonymousNodeText : de.intrabuild.Gettext.gettext("New folder"),
        width             : 200,
        split             : true
    }, this);


    /**
     * Preview panel for the emails
     */

    this.preview = new de.intrabuild.groupware.email.EmailViewPanel({
		autoLoad     : false,
		border	     : false,
		hideMode     : 'offsets',
		refreshFrame : true
	});

    this.preview.on('emailload', this.onEmailLoad, this);
	this.preview.on('emailloadfailure', this.onEmailLoadFailure, this);

    /**
     * The grid that shows the email items.
     */
    this.gridPanel = new de.intrabuild.groupware.email.EmailGrid({
        region : 'center',
        split: true
    }, this);

    this.gridPanel.on('rowdblclick', this.openEmailView, this);

    this.items = [
        this.treePanel
      ,{
        region : 'center',
        layout : 'border',
        border:false,
        hideMode : 'offsets',
        items:[
            this.gridPanel
            ,{
            id:'de.intrabuild.groupware.email.rightPreview',
            layout:'fit',
			hideMode : 'offsets',
            region:'east',
            width:350,
            split: true,
            hidden:true
          },{
            id:'de.intrabuild.groupware.email.bottomPreview',
            layout:'fit',
            items:this.preview,
			hideMode : 'offsets',
            height: 250,
            split: true,
            region:'south'
          }]
    }];

    /**
     * Top toolbar
     * @param {Ext.Toolbar}
     */
    var decorateAccountRelatedClk = de.intrabuild.groupware.email.decorator.AccountActionComp.decorate;

    this.sendNowButton = decorateAccountRelatedClk(new Ext.Toolbar.Button({
        id      : 'de.intrabuild.groupware.email.toolbar.SendButton',
        iconCls : 'de-intrabuild-groupware-email-EmailPanel-toolbar-sendNowButton-icon',
        cls     : 'x-btn-text-icon',
        text    : de.intrabuild.Gettext.gettext("Send now"),
        hidden  : true,
        handler : function(){this.sendPendingItems();},
        scope   : this
    }));
    this.forwardButton = decorateAccountRelatedClk(new Ext.Toolbar.Button({
        id       : 'de.intrabuild.groupware.email.toolbar.ForwardButton',
        cls      : 'x-btn-text-icon',
        iconCls  : 'de-intrabuild-groupware-email-EmailPanel-toolbar-forwardButton-icon',
        text     : '&#160;'+de.intrabuild.Gettext.gettext("Forward"),
        handler  : function(){this.openEmailEditPanel(true, 'forward');},
        disabled : true,
        scope    : this
    }));
    this.replyButton = decorateAccountRelatedClk(new Ext.Toolbar.Button({
        id       : 'de.intrabuild.groupware.email.toolbar.ReplyButton',
        cls      : 'x-btn-text-icon',
        iconCls  : 'de-intrabuild-groupware-email-EmailPanel-toolbar-replyButton-icon',
        text     : '&#160;'+de.intrabuild.Gettext.gettext("Reply"),
        handler  : function(){this.openEmailEditPanel(true, 'reply');},
        disabled : true,
        scope    : this
    }));
    this.replyAllButton = decorateAccountRelatedClk(new Ext.Toolbar.Button({
        id       : 'de.intrabuild.groupware.email.toolbar.ReplyAllButton',
        cls      : 'x-btn-text-icon',
        iconCls  : 'de-intrabuild-groupware-email-EmailPanel-toolbar-replyAllButton-icon',
        text     : '&#160;'+de.intrabuild.Gettext.gettext("Reply all"),
        handler  : function(){this.openEmailEditPanel(true, 'reply_all');},
        disabled : true,
        scope    : this
    }));
    this.deleteButton = new Ext.Toolbar.Button({
        id       : 'de.intrabuild.groupware.email.toolbar.DeleteButton',
        cls      : 'x-btn-text-icon',
        iconCls  : 'de-intrabuild-groupware-email-EmailPanel-toolbar-deleteButton-icon',
        text     : '&#160;'+de.intrabuild.Gettext.gettext("Delete"),
        disabled : true,
        handler  : function(){this.deleteEmails(this.gridPanel.selModel.getSelections());},
        scope    : this
    });
    this.spamButton = new Ext.Toolbar.Button({
        id       : 'de.intrabuild.groupware.email.toolbar.SpamButton',
        cls      : 'x-btn-text-icon',
        iconCls  : 'de-intrabuild-groupware-email-EmailPanel-toolbar-spamButton-icon',
        text     : '&#160;'+de.intrabuild.Gettext.gettext("Spam"),
        disabled : true,
        handler  : function(){this.setItemsAsSpam(this.gridPanel.selModel.getSelections(), true);},
        scope    : this
    });
    this.noSpamButton = new Ext.Toolbar.Button({
        id       : 'de.intrabuild.groupware.email.toolbar.NoSpamButton',
        cls      : 'x-btn-text-icon',
        iconCls  : 'de-intrabuild-groupware-email-EmailPanel-toolbar-noSpamButton-icon',
        text     : '&#160;'+de.intrabuild.Gettext.gettext("No spam"),
        disabled : true,
        hidden   : true,
        handler  : function(){this.setItemsAsSpam(this.gridPanel.selModel.getSelections(), false);},
        scope    : this
    });
    this.editDraftButton = new Ext.Toolbar.Button({
        id       : 'de.intrabuild.groupware.email.toolbar.EditDraftButton',
        cls      : 'x-btn-text-icon',
        iconCls  : 'de-intrabuild-groupware-email-EmailPanel-toolbar-editDraftButton-icon',
        text     : '&#160;'+de.intrabuild.Gettext.gettext("Edit draft"),
        disabled : true,
        hidden   : true,
        handler  : function(){this.openEmailEditPanel(true, 'edit');},
        scope    : this
    });
    this.newButton = decorateAccountRelatedClk(new Ext.Toolbar.Button({
        cls  	 : 'x-btn-text-icon',
        iconCls  : 'de-intrabuild-groupware-email-EmailPanel-toolbar-newButton-icon',
        text 	 : '&#160;'+de.intrabuild.Gettext.gettext("New email"),
        handler  : function(){this.openEmailEditPanel(false, 'new');},
        scope 	 : this
    }));

    this.controlBar = new Ext.Toolbar([
        new de.intrabuild.groupware.email.FetchMenuButton(),
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
        '->', {
        id           : 'de.intrabuild.groupware.email.previewButton',
        split        : true,
        enableToggle : true,
        pressed      : true,
        handler      : this.hidePreview,
        scope        : this,
        cls          : 'x-btn-text-icon',
        iconCls      : 'de-intrabuild-groupware-email-EmailPanel-toolbar-previewButton-icon',
        text         : de.intrabuild.Gettext.gettext("Preview"),
        menu         : {
            id    : 'de.intrabuild.groupware.email.emailPreviewMenu',
            cls   : 'de-intrabuild-groupware-email-EmailPanel-toolbar-previewMenu',
            items : [{
            iconCls      : 'de-intrabuild-groupware-email-EmailPanel-toolbar-previewBottomButton-icon',
            text         : de.intrabuild.Gettext.gettext("bottom"),
            checked      : true,
            group        : 'de.intrabuild.groupware.email.emailPreviewGroup',
            checkHandler : this.hidePreview,
            scope        : this
          },{
            iconCls      : 'de-intrabuild-groupware-email-EmailPanel-toolbar-previewRightButton-icon',
            text         : de.intrabuild.Gettext.gettext("right"),
            checked      : false,
            group        : 'de.intrabuild.groupware.email.emailPreviewGroup',
            checkHandler : this.hidePreview,
            scope        : this
          },{
            iconCls      : 'de-intrabuild-groupware-email-EmailPanel-toolbar-previewHideButton-icon',
            text         : de.intrabuild.Gettext.gettext("hide"),
            checked      : false,
            group        : 'de.intrabuild.groupware.email.emailPreviewGroup',
            checkHandler : this.hidePreview,
            scope        : this
        }]}
      }

    ]);

    var tbarManager = de.intrabuild.groupware.ToolbarManager;
    tbarManager.register('de.intrabuild.groupware.email.Toolbar', this.controlBar);


    /**
    * Constructor call.
    */
    de.intrabuild.groupware.email.EmailPanel.superclass.constructor.call(this,  {
        title          : de.intrabuild.Gettext.gettext("Emails"),
        iconCls        : 'de-intrabuild-groupware-email-EmailPanel-icon',
        closable       : true,
        autoScroll     : false,
        deferredRender : true,
        layout         : 'border',
        hideMode       : 'offsets'
    });

    // install the listeners. As the controller of the emailapp, this class should
    // be responsible for listening to almost all events and delegating actions
    // to the various components.

    var letterman = de.intrabuild.groupware.email.Letterman;
    letterman.on('load', this.newEmailsAvailable, this);

    var gs = this.gridPanel.store;
    gs.on('beforeload',              this.onGridStoreBeforeLoad,  this);
    gs.on('clear',                   this.onGridStoreClear,       this);
    gs.on('beforeselectionsload',    this.onBeforeSelectionsLoad, this);
    gs.on('selectionsload',          this.onSelectionsLoad,       this);
	gs.on('load',                    this.onStoreLoad,            this);
    this.gridPanel.view.on('buffer', this.onStoreBuffer,          this);


    var gm = this.gridPanel.selModel;
    gm.on('rowselect', this.onRowSelect,     this, {buffer : 100});
    gm.on('rowdeselect', this.onRowDeselect, this, {buffer : 100});

    var gv = this.gridPanel.view;
    gv.on('rowremoved', this.onRowRemoved, this);
    gv.on('beforebuffer', this.onBeforeBuffer, this);

    var tp = this.treePanel;
    tp.on('movenode', this.onMoveNode, this);
    tp.on('render', function(){this.treePanel.getSelectionModel().on('selectionchange', this.onNodeSelectionChange, this);}, this);
    tp.on('nodedrop', this.onNodeDrop, this);
    tp.on('remove', this.onNodeRemove, this);
	tp.pendingItemStore.on('add', this.onPendingStoreAdd, this);

    this.on('render',  this.onPanelRender, this);
    this.on('hide',    function(){tbarManager.hide('de.intrabuild.groupware.email.Toolbar');}, this);

    this.on('destroy', function(){
        var sub = de.intrabuild.util.Registry.get('de.intrabuild.groupware.email.QuickPanel');
        if (sub) {
            sub.store.un('update', this.onQuickPanelUpdate, this);
        }
        letterman.un('load', this.newEmailsAvailable, this);
        tbarManager.destroy('de.intrabuild.groupware.email.Toolbar');
    }, this);
    this.on('show',    function(){tbarManager.show('de.intrabuild.groupware.email.Toolbar');}, this);

    de.intrabuild.util.Registry.register('de.intrabuild.groupware.email.EmailPanel', this, true);

    // register listener for MessageBus message
    // 'de.intrabuild.groupware.email.Smtp.emailSent'
    Ext.ux.util.MessageBus.subscribe(
        'de.intrabuild.groupware.email.Smtp.emailSent',
        this.onSendEmail,
        this
    );

    // register listener for MessageBus message
    // 'de.intrabuild.groupware.email.Smtp.beforeBulkSent'
    Ext.ux.util.MessageBus.subscribe(
        'de.intrabuild.groupware.email.Smtp.beforeBulkSent',
        this._onBeforeBulkSent,
        this
    );

    // register listener for MessageBus message
    // 'de.intrabuild.groupware.email.Smtp.bulkSent'
    Ext.ux.util.MessageBus.subscribe(
        'de.intrabuild.groupware.email.Smtp.bulkSentFailure',
        this._onBulkSentFailure,
        this
    );

    // register listener for MessageBus message
    // 'de.intrabuild.groupware.email.Smtp.bulkSent'
    Ext.ux.util.MessageBus.subscribe(
        'de.intrabuild.groupware.email.Smtp.bulkSent',
        this._onBulkSent,
        this
    );

    // register listener for MessageBus message
    // 'de.intrabuild.groupware.email.editor.draftSaved'
    Ext.ux.util.MessageBus.subscribe(
        'de.intrabuild.groupware.email.editor.draftSave',
        this._onSaveDraft,
        this
    );

    // register listener for MessageBus message
    // 'de.intrabuild.groupware.email.LatestEmailCache.clear'
    Ext.ux.util.MessageBus.subscribe(
        'de.intrabuild.groupware.email.LatestEmailCache.clear',
        this._onLatestCacheClear,
        this
    );

    // register listener for MessageBus message
    // 'de.intrabuild.groupware.email.outbox.emailMoved'
    Ext.ux.util.MessageBus.subscribe(
        'de.intrabuild.groupware.email.outbox.emailMove',
        this._onMoveOutbox,
        this
    );
};



Ext.extend(de.intrabuild.groupware.email.EmailPanel, Ext.Panel, {

    buttonsLocked : false,

    pendingRecords : null,

	pendingRecordsDate : {},

    queue : null,

    loadingRecord : null,

    clkNodeId : null,

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
                url: '/groupware/email/delete.items/format/json',
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
            url: '/groupware/email/move.items/format/json',
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
                url: '/groupware/email/set.email.flag/format/json',
                params: {
                    type : 'read',
                    json : Ext.encode(requestArray)
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
        var requestArray = [];
        var store = this.gridPanel.getStore();

        store.suspendEvents();

        for (var i = 0, max_i = records.length; i < max_i; i++) {
            records[i].set('isSpam', spam);
            requestArray.push({
                id     : records[i].id,
                isSpam : spam
            });
        }

        Ext.Ajax.request({
            url: '/groupware/email/set.email.flag/format/json',
            params: {
                type : 'spam',
                json : Ext.encode(requestArray)
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
        if (!record) {
            return;
        }

        var emailRecord = this.preview.emailRecord;

     	if (emailRecord && record.id == emailRecord.id) {
     		this.preview.renderView();
     		return;
     	}

        if (this.loadingRecord && this.loadingRecord.id == record.id) {
        	return;
        }

     	this.loadingRecord = record;
        this.preview.abortRequest();
        this.preview.setEmailItem(record);

    },

    hidePreview : function(btn, evt)
    {
        var right  = Ext.getCmp('de.intrabuild.groupware.email.rightPreview');
        var bot    = Ext.getCmp('de.intrabuild.groupware.email.bottomPreview');

        if (btn instanceof Ext.Toolbar.SplitButton) {
            if (btn.pressed) {
                var previewMenu = Ext.menu.MenuMgr.get('de.intrabuild.groupware.email.emailPreviewMenu');
                previewMenu.render();
                var items = previewMenu.items.items;
                var b = items[0], r = items[1], h = items[2];

                if (b.checked){
                    b.setChecked(false, true);
                    b.setChecked(true);
                } else if(r.checked){
                    r.setChecked(false, true);
                    r.setChecked(true);
                } else if(h.checked){
                    //h.setChecked(false, true);
                    b.setChecked(true);
                }
                return;

            } else {
                bot.hide();
                right.hide();
				this.preview.hide();
                this.ownerCt.doLayout();
                return;
            }
        } else {
            var button      = Ext.getCmp('de.intrabuild.groupware.email.previewButton');
            var previewMenu = Ext.menu.MenuMgr.get('de.intrabuild.groupware.email.emailPreviewMenu');
            previewMenu.render();
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
                return;
            } else if (r.checked) {
                button.toggle(true);
                bot.hide();
				this.preview.hide();
                right.add(this.preview);
                right.show();
                this.ownerCt.doLayout();
				this.preview.show();
                return;
            } else if (h.checked) {
                button.toggle(false);
                bot.hide();
                right.hide();
				this.preview.hide();
                this.ownerCt.doLayout();
                return;
            }
        }
    },

// ------------------------------ Listeners ------------------------------------
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

    	de.intrabuild.groupware.email.EmailViewBaton.showEmail(record);
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

    	de.intrabuild.groupware.email.Dispatcher.sendPendingEmails(
    	    [record],
    	    ((new Date()).getTime()/1000)
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
    		de.intrabuild.groupware.email.EmailEditorManager.createEditor();
    	} else {
    		var record = sm.getSelected();
    		if (c != 1 || !record) {
    			return;
    		}
    		de.intrabuild.groupware.email.EmailEditorManager.createEditor(record, type);
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
        de.intrabuild.groupware.ResponseInspector.handleFailure(response, {
            onLogin: {
                fn : function(){
                    this.preview.load();
                },
                scope : this
            }
        });
	},

    /**
     * Listener for the preview panel when an email message was fully loaded.
     * Sets the according email message to read = true.
     */
    onEmailLoad : function(record)
    {
    	var id = record.id;

    	var store = this.gridPanel.store;

    	var rec = store.getById(id);

    	if (rec) {
			this.setItemsAsRead([rec], true);
    	}
    },

    /**
     * Called when the Ext.ux.util.MessageBus publishes the
     * de.intrabuild.groupware.email.LatestEmailCache.clear message.
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

        var itemRecord   = message.itemRecord;
        var currFolderId = this.clkNodeId;
        var store        = this.gridPanel.getStore();
	    var pendingStore = tp.pendingItemStore;

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
     * Observer for the message de.intrabuild.groupware.email.Smtp.bulkSent.
     *
     * @param {String} Subject
     * @param {Object} message
     */
    _onBulkSent : function(subject, message)
    {
        var emailItems   = message.emailItems;
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

    },

    /**
     * Observer for the message de.intrabuild.groupware.email.Smtp.bulkSentFailure.
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
     * Observer for the message de.intrabuild.groupware.email.Smtp.beforeBulkSent.
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
     * "de.intrabuild.groupware.email.Smtp.emailSent" as published by Ext.ux.util.MessageBus.
     *
     * @param {String} subject The subject of the message
	 * @param {Object} data The message's data. For the event this listener observes, the
	 * object will provide the following properties:
	 * draft - de.intrabuild.groupware.email.data.Draft
	 * itemRecord - de.intrabuild.groupware.email.EmailItemRecord
	 * referencedItem - de.intrabuild.groupware.email.EmailItemRecord
	 * options - Object
     */
    onSendEmail : function(subject, message)
    {
        var referencedRecord = message.referencedItem;

        var emailRecord = message.itemRecord;
        var draft       = message.draft;
        var oldId       = draft.get('id');
        var oldFolderId = draft.get('groupwareEmailFoldersId');
        var type        = draft.get('type');

        var tp           = this.treePanel;
        var currFolderId = this.clkNodeId;
        var store        = this.gridPanel.getStore();
        var pendingStore = tp.pendingItemStore;

        // check if the grid with the record for old id is open. Update the specific record
        // with the reference type, if message.type equals to forward, reply or reply_all
        if (type.indexOf('reply') != -1 || type.indexOf('forward') != -1) {
            var refRecord = store.getById(oldId);
            if (refRecord) {
                var references = refRecord.get('referencedAsTypes').slice(0);
                if (references.indexOf(type) == -1) {
                    references.push(type);
                    store.suspendEvents();
                    refRecord.set('referencedAsTypes', references);
                    store.resumeEvents();
                    store.commitChanges();
                }
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

        // if the email was loaded from outbox and sent, update pending nodes
        // minus 1, but only if the id of the itemRecord equals to the id of the draft,
        // which will basically tell that an email pending in the outbox folder was sent
        if (oldFolderId == tp.folderOutbox.id && emailRecord.get('id') == draft.get('id')) {
            // if grid is visible, remove the record with the specified id!
            if (currFolderId == oldFolderId) {
                if (referencedRecord) {
				    store.remove(referencedRecord);
			    }
            }
            // update pending count in any case
            var pendingRecord = pendingStore.getById(oldFolderId);
            if (pendingRecord) {
                pendingRecord.set('pending', pendingRecord.data.pending-1);
            }
        }

        // if the email was loaded from drafts, nothing will happen, as a draft
        // will not be deleted, thus can be reused after an email was sent from it

        // if the visible grid is the grid for sent items, add the recod to the store
        if (emailRecord.get('groupwareEmailFoldersId') == currFolderId) {
            var index = store.findInsertIndex(emailRecord);
			store.insert(index, emailRecord.copy());
        }


    },

    /**
     * Callback if a draft was successfully saved. Listens to messages with the subject
     * "de.intrabuild.groupware.email.editor.draftSaved" as published by Ext.ux.util.MessageBus.
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
        de.intrabuild.groupware.ToolbarManager.show('de.intrabuild.groupware.email.Toolbar');

        var sub = de.intrabuild.util.Registry.get('de.intrabuild.groupware.email.QuickPanel');

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

        this.deleteButton.setIconClass('de-intrabuild-groupware-selectionsLoading');
        this.spamButton.setIconClass('de-intrabuild-groupware-selectionsLoading');
        this.noSpamButton.setIconClass('de-intrabuild-groupware-selectionsLoading');
    },

    /**
     *
     */
    onSelectionsLoad : function()
    {
        this.buttonsLocked = false;

        this.deleteButton.setIconClass('de-intrabuild-groupware-email-EmailPanel-toolbar-deleteButton-icon');
        this.spamButton.setIconClass('de-intrabuild-groupware-email-EmailPanel-toolbar-spamButton-icon');
        this.noSpamButton.setIconClass('de-intrabuild-groupware-email-EmailPanel-toolbar-noSpamButton-icon');

        var sm = this.gridPanel.selModel;
        var c  = sm.getCount();
        this.switchButtonState(c, sm.getSelected());
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

            this.loadingRecord = null;
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
            this.preview.setEmailItem(null);
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

        if ((rec && rec.id == record.id) || this.loadingRecord == record.id) {
            this.preview.setEmailItem(null);
            this.loadingRecord = null;
        }
    },


    onGridStoreClear : function()
    {
        this.switchButtonState(0, null);
        this.preview.setEmailItem(null);
        this.loadingRecord = null;
    },

    onBeforeBuffer : function()
    {
        this.clearPending();
    },


    onGridStoreBeforeLoad : function(store, options)
    {
        this.preview.setEmailItem(null);

        this.clearPending();

        if (this.clkNodeId == null) {
            return false;
        }

        (options.params = options.params || {}).groupwareEmailFoldersId = this.clkNodeId;

    },


    onNodeSelectionChange : function(selModel, node)
    {
        this.switchButtonState(0, null);

        if (node && node.attributes.type && (node.attributes.type != 'root' && node.attributes.type != 'accounts_root')) {
            this.clkNodeId = node.id;
            this.gridPanel.store.removeAll();
            this.gridPanel.view.reset(true);
            return;
        }

        this.clkNodeId = null;
        this.gridPanel.loadMask.hide();
        var proxy = this.gridPanel.store.proxy;
        if (proxy.activeRequest) {
            proxy.getConnection().abort(proxy.activeRequest);
        }
        this.gridPanel.store.removeAll();
    },

    onNodeRemove : function(tree, parent, node)
    {
        this.clkNodeId = null;
        this.gridPanel.loadMask.hide();
        var proxy = this.gridPanel.store.proxy;
        if (proxy.activeRequest) {
            proxy.getConnection().abort(proxy.activeRequest);
        }
        this.gridPanel.store.removeAll();
    },

    onMoveNode : function()
    {
        if (this.loadingMask) {
            this.loadingMask.hide();
        }

        this.loadingRecord = null;

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

        if (this.clkNodeId && (this.clkNodeId == folderId
            && !this.gridPanel.store.proxy.activeRequest)) {
            var pendingStore  = this.treePanel.pendingItemStore;
            var pendingRecord = pendingStore.getById(folderId);
            if (pendingRecord) {
                pendingRecord.set('pending', pendingRecord.data.pending+1);
                this.pendingRecords[this.clkNodeId]--;
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
	},

	onStoreBuffer : function(view, store, rowIndex, visibleRows, totalCount, options)
    {
        this._updatePendingFromLoad(store, options);
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
            this.pendingRecordsDate[records[i].id] = records[i].get('date');
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
            if (folderId != this.clkNodeId) {
                pendingRecord = pendingStore.getById(folderId);
                if (pendingRecord) {
                    pendingRecord.set('pending', pendingRecord.data.pending+this.pendingRecords[folderId]);
                    this.pendingRecords[folderId] = 0;
                }
            }
        }

        if (this.gridPanel.store.proxy.activeRequest) {
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
        var ind      = store.findInsertIndex(itemRecord);
        var itemCopy = itemRecord.copy();

        // silently remove the old record, then insert the new one.
        // Afterwards, refresh the grid
        // we need to repaint the visible rect of the grid if the
        // referencedRecord is currently in the visible rect
        if (referencedRecord) {
            var view    = this.gridPanel.getView();
            var refresh = view.isRecordRendered(referencedRecord);
            store.suspendEvents();
            store.remove(referencedRecord);

            store.insert(ind, itemCopy);
            store.resumeEvents();
            if (refresh || view.isRecordRendered(itemCopy)) {
                view.refresh(false);
            }
        } else {
            store.insert(ind, itemCopy);
        }
    }


});