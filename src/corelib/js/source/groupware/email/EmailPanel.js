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


            var gs = this.gridPanel.store;
            var requestArray = [];
            for (var i = 0, max_i = records.length; i < max_i; i++) {
                unread += (records[i].data.isRead ? 0 : 1);
                requestArray.push({ id : records[i].id });
            }

            gs.bulkRemove(records);

            this.fireEvent('emailsdeleted', records);

            var pendingStore  = this.treePanel.pendingItemStore;
            var pendingRecord = pendingStore.getById(this.clkNodeId);
            if (pendingRecord) {
                pendingRecord.set('pending', pendingRecord.data.pending-unread);
            }

            if (requestArray.length > 0) {
                Ext.Ajax.request({
                    url: '/groupware/email/delete.items/format/json',
                    params: {
                        json : Ext.encode(requestArray)
                    }
                });
            }
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

        var gs = this.gridPanel.store;
        var requestArray = [];

        for (var i = 0, max_i = records.length; i < max_i; i++) {
            unread += (records[i].data.isRead ? 0 : 1);
            records[i].set('groupwareEmailFoldersId', folderId);
            requestArray.push({
                id                      : records[i].id,
                groupwareEmailFoldersId : folderId
            });
        }

        var allowPendingUpdate = this.allowNodePendingUpdate(currFolderId);
        var updatePendingCount = allowPendingUpdate ? 0 : i;

        gs.commitChanges();

        gs.bulkRemove(records);

        if (requestArray.length > 0) {
            Ext.Ajax.request({
                url: '/groupware/email/move.items/format/json',
                params: {
                    json : Ext.encode(requestArray)
                }
            });
        }

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

        var unread     = 0;

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

        this.gridPanel.store.commitChanges();
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

        this.gridPanel.store.commitChanges();
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
        	this.forwardButton.setDisabled(true);
            this.replyButton.setDisabled(true);
            this.replyAllButton.setDisabled(true);
            this.deleteButton.setDisabled(true);
            this.spamButton.setDisabled(true);
            this.noSpamButton.setDisabled(true);
            this.spamButton.setVisible(true);
            this.noSpamButton.setVisible(false);
            this.editDraftButton.setDisabled(true);
            return;
        }  else if (count == 1) {
            this.forwardButton.setDisabled(false);
            this.replyButton.setDisabled(false);
            this.replyAllButton.setDisabled(false);
            this.editDraftButton.setDisabled(false);
        } else if (count > 1) {
            this.forwardButton.setDisabled(true);
            this.replyButton.setDisabled(true);
            this.replyAllButton.setDisabled(true);
            this.editDraftButton.setDisabled(true);
        }

        var tp = this.treePanel;
        var isDrafts = this.clkNodeId == tp.folderDraft.id;

        this.editDraftButton.setVisible(isDrafts);

        var isSpam = record.data.isSpam;

        var isNotSpammable = (isDrafts) ||
        				  	 (this.clkNodeId == tp.folderSent.id) ||
        				  	 (this.clkNodeId == tp.folderOutbox.id);

        this.spamButton.setDisabled(isNotSpammable || isSpam);
        this.noSpamButton.setDisabled(isNotSpammable || !isSpam);
        this.spamButton.setVisible(!isSpam);
        this.noSpamButton.setVisible(isSpam);

        this.deleteButton.setDisabled(false);
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
    		de.intrabuild.groupware.email.EmailEditorManager.createEditor(record.id, type);
    	}
    },

    onQuickPanelUpdate : function(store, record, operation)
    {
    	if (operation == 'commit') {
            var myStore = this.gridPanel.store;
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
     * Callback if an email was moved to the outbox folder.
     * The moved email was either created from a new draft or loaded from an existing
     * draft.
     * In either way, the pending record count from the outbox-folder has to be
     * updated.
     * Secondly, the pending record count has to be updated from the draft folder,
     * if the moved email was loaded from there.
     * Additionally, the view of the grid has to be updated, depending on the currently
     * opened folder.
     * If the currently opened folder is the drafts folder and the email has been opened
     * from it, search for the according record and remove it from there.
     * If the currently opened folder is the outbox folder, create a new record based
     * on the passed data and add it to the store.
     *
     *
     */
    onMoveOutbox : function(data, savedId)
    {
    	var currFolderId = this.clkNodeId;
    	var tp = this.treePanel;
    	var draftId = tp.folderDraft.id;
    	var outboxId  = tp.folderOutbox.id;
    	var messageId = data.id;
    	var folderId = data.folderId;

    	// first off, update the pending records of the outbox,
    	// which has to be done in any case
    	var pendingStore  = tp.pendingItemStore;
        var pendingRecord = pendingStore.getById(outboxId);
        if (pendingRecord) {
            pendingRecord.set('pending', pendingRecord.data.pending+1);
        }

        // check if the drafts folder is open. If the email was opened from
        // drafts, remove the record from the grid.
        if (folderId == draftId) {

        	var pendingRecord = pendingStore.getById(draftId);
	        if (pendingRecord) {
	            pendingRecord.set('pending', pendingRecord.data.pending-1);
	        }

        	// remove the record
        	if (currFolderId == draftId) {
        		var record = store.getById(messageId);
				if (record) {
					store.remove(record);
				}
        	}

    	} else if (currFolderId == outboxId) {
        	// add a new record
        	var store = this.gridPanel.store;
			var nRecord = new de.intrabuild.groupware.email.EmailItemRecord({
				'id'		               : savedId,
				'isAttachment'               : data.isAttachment,
			    'isRead'                     : true,
			    'subject'                  : Ext.util.Format.htmlEncode(data.subject),
			    'from'                     : Ext.util.Format.htmlEncode(data.from),
			    'date'                     : (new Date()).format('m/d/Y H:i:s'),
			    'isSpam'                     : false,
			    'groupwareEmailFoldersId'  : outboxId
			});

			var index = store.findInsertIndex(nRecord);
			store.insert(index, nRecord);
        }
    },

    /**
     * Callback if an email was successfully send. Listens to messages with the subject
     * "de.intrabuild.groupware.email.Smtp.emailSent" as published by Ext.ux.util.MessageBus.
     *
     * @param {String} subject The subject of the message
	 * @param {Object} data The message's data. For the event this listener observes, the
	 * object will provide the following properties:
	 * itemRecord - de.intrabuild.groupware.email.EmailItemRecord
	 * id - the id of the message before it was saved
	 * groupwareEmailFoldersId - id of the folder before it was saved.
	 *
     */
    onSendEmail : function(subject, message)
    {
        var emailRecord = message.itemRecord;
        var oldId       = message.id;
        var oldFolderId = message.groupwareEmailFoldersId;

        var tp           = this.treePanel;
        var currFolderId = this.clkNodeId;
        var store        = this.gridPanel.store;
        var pendingStore = tp.pendingItemStore;

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
        // minus 1
        if (oldFolderId == tp.folderOutbox.id) {
            // if grid is visible, remove the record with the specified id!
            if (currFolderId == oldFolderId) {
                var record = store.getById(oldId);
			    if (record) {
				    store.remove(record);
			    }
            } else {
                // grid for outbox is not visible, simply update pending count
                var pendingRecord = pendingStore.getById(oldFolderId);
                if (pendingRecord) {
                    pendingRecord.set('pending', pendingRecord.data.pending-1);
                }
            }
        }

        // if the email was loaded from drafts, nothing will happen, as a draft
        // will not be deleted, thus can be reused after an email was sent from it

        // if the visible grid is the grid for sent items, add the recod to the store
        if (emailRecord.get('groupwareEmailFoldersId') == currFolderId) {
            var index = store.findInsertIndex(emailRecord);
			store.insert(index, emailRecord);
        }


    },

	/**
	 * Callback for a successfull save of an email draft.
	 *
	 * @param {Object} data An object containing all needed information as
	 * available in EmailItemRecord
	 * @param {Number} savedId The id of the draft that was generated while
	 * being saved as a unique identifer for this draft
	 */
	onSaveDraft : function(data, savedId)
	{
		var messageId = data.id;

		var store = this.gridPanel.store;

        // means that the email we were working on was already saved before.
        // do not update the pending records of the folder then, but update the
        // record data.
        if (savedId == messageId) {

        	// update the record with the edited values.
			// fetch the record with the id out of the store if the actual viewed
			// folder is the "drafts" folder.
			if (this.clkNodeId && this.treePanel.folderDraft.id == this.clkNodeId) {

				var rec = store.getById(messageId);

				if (rec) {
					rec.set('subject', data.subject);
					rec.set('date', (new Date()).format('m/d/Y H:i:s'));
					rec.set('from', Ext.util.Format.htmlEncode(data.from));
					var recips = data.recipients;
					var parts = null;
					var cleared = [];
					for (var i = 0, len = recips.length; i < len; i++) {
						if (recips[i][0] == 'to') {
							parts = recips[i][1].split(',').concat(recips[i][1].split(';'));

							for (var a = 0, lena = parts.length; a < lena; a++) {
								if (parts[a].trim() != "") {
									cleared.push(Ext.util.Format.htmlEncode(parts[a]));
								}
							}
						}
					}
					rec.set('recipients', cleared.join(', '));
					store.commitChanges();
					this.gridPanel.selModel.clearSelections();
				} else {
					// if we are in here, the record was not found. This could be
					// because the record was opened for editing but deleted in the meantime.
					// simply append a new record then (the id was updated serverside, too)
					var nRecord = new de.intrabuild.groupware.email.EmailItemRecord({
						'id'		              : savedId,
						'isAttachment'              : data.isAttachment,
					    'isRead'                    : false,
					    'subject'                 : Ext.util.Format.htmlEncode(data.subject),
					    'from'                    : Ext.util.Format.htmlEncode(data.from),
					    'date'                    : (new Date()).format('m/d/Y H:i:s'),
					    'isSpam'                    : false,
					    'groupwareEmailFoldersId' : this.treePanel.folderDraft.id
					});

					var index = store.findInsertIndex(nRecord);
					store.insert(index, nRecord);
				}

			}

            return;
        } else {
			// update pending records and add a new record to the grid if the folder
			// currently being watched equals to the drafts folder!
			// this basically means that a new record was created so we have to insert that record
			// into the store, too
			var tp = this.treePanel;
            var pendingStore  = tp.pendingItemStore;
            var pendingRecord = pendingStore.getById(tp.folderDraft.id);
            if (pendingRecord) {
                pendingRecord.set('pending', pendingRecord.data.pending+1);
            }
            var nRecord = new de.intrabuild.groupware.email.EmailItemRecord({
				'id'		              : savedId,
				'isAttachment'              : data.isAttachment,
			    'isRead'                    : false,
			    'subject'                 : Ext.util.Format.htmlEncode(data.subject),
			    'from'                    : Ext.util.Format.htmlEncode(data.from),
			    'date'                    : (new Date()).format('m/d/Y H:i:s'),
			    'isSpam'                    : false,
			    'groupwareEmailFoldersId' : this.treePanel.folderDraft.id
			});

			var index = store.findInsertIndex(nRecord);

			store.insert(index, nRecord);
        }
	},

    onRegister : function(name, object)
    {
    	if (name == 'de.intrabuild.groupware.email.EmailForm') {
    		var form = de.intrabuild.util.Registry.get('de.intrabuild.groupware.email.EmailForm')
    		form.un('savedraft', this.onSaveDraft, this);
    		form.on('savedraft', this.onSaveDraft, this);

    		form.un('movedtooutbox', this.onMoveOutbox, this);
    		form.on('movedtooutbox', this.onMoveOutbox, this);
    	}
        /*
        listener gets added in onPanelRender, so we might not need this
        if (name != 'de.intrabuild.groupware.email.QuickPanel') {
            return;
        }

        var sub = de.intrabuild.util.Registry.get('de.intrabuild.groupware.email.QuickPanel');

        sub.store.un('update', this.onQuickPanelUpdate, this);
        sub.store.on('update', this.onQuickPanelUpdate, this);*/
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
        if (source instanceof Ext.ux.grid.BufferedGridDragZone) {
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
            this.preview.clearView();//body.update("");

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

            this.preview.clearView();//body.update("");
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

        var rec   = this.preview.emailItem;

        if ((rec && rec.id == record.id) || this.loadingRecord == record.id) {
            this.preview.clearView();
            this.loadingRecord = null;
        }
    },


    onGridStoreClear : function()
    {
        this.switchButtonState(0, null);

        this.preview.clearView();
        this.loadingRecord = null;
    },

    onBeforeBuffer : function()
    {
        this.clearPending();
    },


    onGridStoreBeforeLoad : function(store, options)
    {
        this.preview.clearView();

        this.clearPending();

        if (this.clkNodeId == null) {
            return false;
        }

        (options.params = options.params || {}).groupwareEmailFoldersId = this.clkNodeId;

    },


    onNodeSelectionChange : function(selModel, node)
    {
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
    }


});