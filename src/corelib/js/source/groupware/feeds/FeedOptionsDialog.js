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
 
/**
 * The dialog uses a tree panel for showing all feeds.
 * 
 * @todo Store loading could fail. This should be intercepted in any way.
 */
de.intrabuild.groupware.feeds.FeedOptionsDialog = function(config) {
    
    Ext.apply(this, config); 
    
    this.store = new Ext.data.Store({
            storeId	    : Ext.id(),
            autoLoad    : false,
            reader      : new Ext.data.JsonReader({
                              id : 'id'
                          }, de.intrabuild.groupware.feeds.AccountRecord)
        });
    

    /**
     * The panel that holds the tree nodes. Rendered like a combo box.
     */
    this.feedPanel = new Ext.grid.GridPanel({
        cls        : 'de-intrabuild-groupware-feeds-FeedOptionsDialog-feedPanel',
        autoScroll : true,
        height     : 195,
		hideHeaders : true,
        enableColumnMove : false,
        enableHdMenu : false,
        store  : this.store,
        sortInfo   : {field: 'name', direction: "DESC"},            
        selModel   : new Ext.grid.RowSelectionModel({singleSelect:true}),
        columns    : [{
            id        : 'feed_name', 
            header    : '<b>'+de.intrabuild.Gettext.gettext("Feeds")+'</b>', 
            width     : 148, 
            sortable  : true, 
            dataIndex : 'name'
        }]
    });    
    
    var tmpStore = de.intrabuild.util.Registry.get('de.intrabuild.groupware.feeds.AccountStore');
    var records = tmpStore.getRange();
    tmpStore.on('add', this.addRecordsFromStore, this);
    for (var i = 0, len = records.length; i < len; i++) {
        this.store.add(records[i].copy());    
    }
    
    /**
     * Button for adding a feed
     */
    this.addFeedButton = new Ext.Button({
        text     : de.intrabuild.Gettext.gettext("Add feed"),
        cls      : 'de-intrabuild-margin-b-5',
        minWidth : 150,
        scope    : this,
        handler  : function(){
            var dialog = new de.intrabuild.groupware.feeds.AddFeedDialog({animateTarget : this.addFeedButton.el.dom.id});
            dialog.show();
        }
    });
    
    /**
     * Button for removing a feed
     */
    this.removeFeedButton = new Ext.Button({
        text     : de.intrabuild.Gettext.gettext("Remove feed"),
        minWidth : 150,    
        disabled : true,
        handler  : this.removeSelected,
        scope    : this
    });
    
    /**
     * Textfield for the feeds url. This is immutable.
     */
    this.feedUrl = new Ext.form.TextField({
        fieldLabel : de.intrabuild.Gettext.gettext("Address"),
        disabled   : true,
        disabledClass : ''
    });
    
    /**
     * Textfield for the feeds name.
     */
    this.feedName = new Ext.form.TextField({
        fieldLabel : de.intrabuild.Gettext.gettext("Name"),
        allowBlank : false,
        validator  : this.isFeedNameValid.createDelegate(this)
    });

    /**
     * Combobox to store the duration of how long to store the entries before
     * wiped in the DB.
     *
     * The mode is set to local, since the store gets loaded via a callback
     * after the dialog is rendered.
     */
    this.removeAfter = new Ext.form.ComboBox({
        tpl           : '<tpl for="."><div class="x-combo-list-item">{text:htmlEncode}</div></tpl>',
        fieldLabel    : de.intrabuild.Gettext.gettext("Save entries"),
        listClass     : 'de-intrabuild-smalleditor',
        displayField  : 'text',
        valueField    : 'id',
        mode          : 'local',
        editable      : false,
        triggerAction : 'all',
        store         : new Ext.data.SimpleStore({
            data   : [
                [2419200, de.intrabuild.Gettext.gettext("for 2 weeks")],
                [1209600, de.intrabuild.Gettext.gettext("for one week")],
                [432000,  de.intrabuild.Gettext.gettext("for 5 days")],
                [172800,  de.intrabuild.Gettext.gettext("for 2 days")],
                [86400,   de.intrabuild.Gettext.gettext("for one day")],
                [43200,   de.intrabuild.Gettext.gettext("for 12 hours")],
                [21600,   de.intrabuild.Gettext.gettext("for 6 hours")],
                [7200,    de.intrabuild.Gettext.gettext("for 2 hours")],
                [3600,    de.intrabuild.Gettext.gettext("for one hour")]
            ],
            fields : ['id', 'text']
        })
    });
    
    /**
     * Combobox to store update/refresh behavior.
     *
     * The mode is set to local, since the store gets loaded via a callback
     * after the dialog is rendered.
     */
    this.updateAfter = new Ext.form.ComboBox({
        tpl           : '<tpl for="."><div class="x-combo-list-item">{text:htmlEncode}</div></tpl>',
        fieldLabel    : de.intrabuild.Gettext.gettext("Check for new entries"),
        itemCls       : 'de-intrabuild-margin-b-15',
        listClass     : 'de-intrabuild-smalleditor',
        displayField  : 'text',
        valueField    : 'id',
        mode          : 'local',
        editable      : false,
        triggerAction : 'all',
        store         : new Ext.data.SimpleStore({
            data   : [
                [172800, de.intrabuild.Gettext.gettext("every 2 days")],
                [86400,  de.intrabuild.Gettext.gettext("every day")],
                [43200,  de.intrabuild.Gettext.gettext("evey 12 hours")],
                [21600,  de.intrabuild.Gettext.gettext("every 6 hours")],
                [7200,   de.intrabuild.Gettext.gettext("evey 2 hours")],
                [3600,   de.intrabuild.Gettext.gettext("every hour")],
                [1800,   de.intrabuild.Gettext.gettext("every 30 minutes")],
                [900,    de.intrabuild.Gettext.gettext("every 15 minutes")]
            ],
            fields : ['id', 'text']
        }) 
      
    });        
    
    /**
    * Items
    */
    this.items = [{
        region:'west',
        width: 150,
        border: false,
        margins:'5 5 5 5',
        bodyStyle : 'background:none',
        items : [
            this.feedPanel,
            this.addFeedButton,
            this.removeFeedButton
        ]
      }, {
        margins  : '5 5 5 5',
        hideMode : 'visibility',
        id       : 'de.intrabuild.groupware.feeds.FeedOptionsDialog.formPanel',
        region   :'center',
        border    :false,
        bodyStyle :'background:none',
        defaults  : {
            border : false
        },
        items : [
            new de.intrabuild.groupware.util.FormIntro({
                  label : de.intrabuild.Gettext.gettext("Informations"),
                  text  : ''        
            }),  
            new Ext.form.FormPanel({
                bodyStyle  : 'margin:10px 0 20px 20px;background:none',
                baseCls    : 'x-small-editor',
                labelAlign : 'left',
                labelWidth : 55,
                defaults   : {
                    labelStyle : 'width:55px;font-size:11px',
                    anchor: '100%'
                },
                items : [
                    this.feedUrl,
                    this.feedName
                ]
          }), 
		  new de.intrabuild.groupware.util.FormIntro({
			      label : de.intrabuild.Gettext.gettext("Options"),
				  text  : ''	
		  }),       
            new Ext.form.FormPanel({
                bodyStyle  : 'margin:10px 0 20px 0px;padding-left:20px;background:none',
                baseCls    : 'x-small-editor',
                labelAlign : 'top',
                defaults   : {
                    labelStyle : 'font-size:11px',
                    anchor: '100%'
                },
                items : [
                    this.updateAfter,    
                    this.removeAfter
                ]
          })                
        ]
    }];
    
    this.buttons = [{
        text    : 'OK',
        handler : function(){this.saveConfiguration(true);},
        scope   : this
      },{    
        text    : de.intrabuild.Gettext.gettext("Cancel"),
        handler : this.close,
        scope   : this              
      },{    
        text     : de.intrabuild.Gettext.gettext("Apply"),
        handler  : this.saveConfiguration,
        scope    : this,
        disabled : true  
    }];
    
    /**
    * Constructor call.
    */
    de.intrabuild.groupware.feeds.FeedOptionsDialog.superclass.constructor.call(this,  {
        iconCls   : 'de-intrabuild-groupware-feeds-Icon', 
        title     : de.intrabuild.Gettext.gettext("Feed settings"),
        bodyStyle : 'background-color:#F6F6F6',
        height    : 325,
        width     : 450,
        modal     : true,
        resizable : false,
        layout    : 'border'
    });    
    
    this.formPanel = this.getComponent('de.intrabuild.groupware.feeds.FeedOptionsDialog.formPanel');
    
    // add listener
    this.on('render', this.onDialogRendered, this);
    this.feedPanel.selModel.on('beforerowselect', this.onBeforeRowSelect, this);
    this.feedPanel.selModel.on('rowselect',       this.onRowSelect, this);
    this.feedPanel.selModel.on('rowdeselect',     this.onRowDeselect, this);
    
    this.removeAfter.on('select', this.configChanged, this);
    this.updateAfter.on('select', this.configChanged, this);
    
    this.feedName.on('render', function() {
            this.feedName.el.on('keyup',    this.configChanged, this);
            this.feedName.el.on('keydown',  this.configChanged, this);
            this.feedName.el.on('keypress', this.configChanged, this);
    }, this);
        
    this.on ('destroy', function() {
        de.intrabuild.util.Registry.get('de.intrabuild.groupware.feeds.AccountStore').un('add', this.addRecordsFromStore, this);
    }, this);    
    
  
    
};


Ext.extend(de.intrabuild.groupware.feeds.FeedOptionsDialog, Ext.Window, {

    LOADING         : 0,
    SAVING          : 1,
    
    msgLoading  : de.intrabuild.Gettext.gettext("Loading feed configurations..."),
    msgSaving   : de.intrabuild.Gettext.gettext("Saving configurationen..."),
    
    /**
     * This array holds all records that where marked as deleted. Instead of 
     * deleting them directly on request, feeds will be stored here and merged with
     * the modified recordset right before saving the configuration. Deleted records
     * will have set their property <tt>deleted</tt> to <tt>1</tt>.
     */
    deletedRecords : null,
    
    /**
     * Auto generated id from the Ajax request that saves the configuration.
     */
    requestId : null,
    
    /**
     * Tells wether to close the dialog after saving the feed configuration or
     * not. 
     */
    closeAfterSave : false,
    
    /**
     * Stores the last selected row.
     */
    clkRowIndex : -1,

    /**
     * Stores the last selected record.
     */
    clkRecord : null,
    
    /**
     * Stores the number of deleted records during this dialog lifetime
     */
    deletedRecordCount : 0,
    
    /**
     * Stores the number of modified records during this dialog lifetime
     */
    modifiedRecordCount : 0,
    

    /**
     * LoadMask configuration
     */
    loadMaskConfig : {
        msg : de.intrabuild.Gettext.gettext("Processing...")    
    },
    
    /**
     * The load mask for this dialog. Used for showing process of saving and 
     * reading data.
     */
    loadMask : null,
    
    /**
     * Gets called when a feed was added via the AddFeedDialog and this dialog 
     * was closed
     * @param {e.intrabuild.groupware.feeds-FeedRecord} The newly added feed
     */
    addRecordsFromStore : function(store, records, index)
    {
        this.feedPanel.store.add(records);      
    },
    
    
    /**
     * Removes the selected feed configuration
     */
    removeSelected : function()
    {
        var record = this.clkRecord;
        
        //just in case    
        if (record == null) {
            return;
        }
        
        var msg  = Ext.MessageBox;
        
        msg.show({
            title   : de.intrabuild.Gettext.gettext("Remove feed"),
            msg     : String.format(
			    de.intrabuild.Gettext.gettext("Do you really want to remove \"{0}\"?"),
                record.get('name')
			),
            buttons : msg.YESNO,
            fn      : function(b) {
                        if (b == 'yes') {
                            this.removeRecord();
                        }
            },
            scope   : this,
            icon    : msg.QUESTION,
            cls     :'de-intrabuild-msgbox-question',
            width   :400
        });        
    },
    
    /**
     * Removes a record from teh datastore and puts it into <tt>deltedRecords</tt>.
     */
    removeRecord : function()
    {
        var record = this.clkRecord;
        
        //just in case    
        if (record == null) {
            return;
        }
        this.feedPanel.selModel.suspendEvents();
        
        var store = this.feedPanel.store;
        
        // revert all changes to this record since the last commit, so they
        // don't get accidently written into the database
        record.reject(true);
        
        var rData  = record.data;
        var delRecord = record.copy(); 
        
        
        if (this.deletedRecords  == null) {
            this.deletedRecords = new Array();
        }
        
        this.deletedRecordCount++;
        
        this.deletedRecords.push(delRecord);
        
        store.remove(record);
        this.clkRecord   = null;
        this.clkRowIndex = -1;
        
        this.removeFeedButton.setDisabled(true);
        this.formPanel.setVisible(false);
        
        this.buttons[2].setDisabled(false);
        this.feedPanel.selModel.resumeEvents();
    },
    
    /**
     * Handler for saving the configuration. Reads out all modified records 
     * and sends a request to the server to save them.
     *
     * @param {Boolean} <tt>true</tt> to close the dialog when saving the 
     *                  configuration finishes, otherwise <tt>false</tt>
     */
    saveConfiguration : function(closeAfterwards)
    {
        this.showLoadMask(this.SAVING);
        this.closeAfterSave = closeAfterwards === true ? true : false;
        
        // make sure the last selected record gets saved
        this.saveRecord();
            
        var records = this.feedPanel.store.getModifiedRecords();
        var recordset = new Array();
        
        var a     = 0;
        var max_i = 0;
        var deleted = [];
        var updated = [];
        // merge deleted, if any
        if (this.deletedRecords) {
            for (var a = 0, max_a = this.deletedRecords.length; a < max_a; a++) {
                deleted.push(this.deletedRecords[a].id);
            }
        }
         
        for (var i = 0, max_i = records.length; i < max_i; i++) {
            updated.push(records[i].data);
        }

        // nothing to do?
        if (a == 0 && i == 0) {
            this.saved();
            return;
        }
        
        this.requestId = Ext.Ajax.request({
            url            : '/groupware/feeds/update.accounts/format/json',
            params         : {
                deleted : Ext.encode(deleted),
                updated : Ext.encode(updated)
            },
            success        : this.onSuccess, 
            failure        : this.onFailure,
            scope          : this,
            disableCaching : true
        });

    },
    
    /**
    * Method gets executed when records have been successfully saved.
    * @access private
    */
    saved : function()
    {
        this.deletedRecords = null;
        
        this.feedPanel.store.commitChanges(); 
        
        if (this.closeAfterSave == true) {
            this.close();
            return;    
        }
        
        this.resetState();
    },
    
    /**
     * Callback for the request that was send savong the feed configurations.
     */
    onSuccess : function(response, parameters)
    {
        // shorthands
        var json = de.intrabuild.util.Json;
        var msg  = Ext.MessageBox;

        if (json.isError(response.responseText)) {
            this.onFailure(response, parameters);
            return;
        }     
        
        var values = json.getResponseValues(response.responseText);
        
        if (values.success != true) {
            // Fallback. Usually, the returntype should be boolean, always 
            // set to true. If that fails, this message will be shown
            // with no further indication of what the error caused.
            // This should never been evaluated by runtime, since the
            // server will send an error back if updating the record fails.
            // If you take this to the code-wtf, I'll suspend your account ;) 
            msg.show({
                title   : de.intrabuild.Gettext.gettext("Error"),
                msg     : de.intrabuild.Gettext.gettext("Could not update the feed configurations."),
                buttons : msg.OK,
                icon    : msg.ERROR,
                cls     :'de-intrabuild-msgbox-error',
                width   : 400
            });   
            this.loadMask.hide();
            return;
        }
        
        var accountStore = de.intrabuild.util.Registry.get('de.intrabuild.groupware.feeds.AccountStore');
        var store = de.intrabuild.util.Registry.get('de.intrabuild.groupware.feeds.FeedStore');
        var updatedFailed = values.updatedFailed;
        var records = this.feedPanel.store.getModifiedRecords();
        var items = store.getRange();
        var up = null;
        for (var i = 0, len = records.length; i < len; i++) {
            if (updatedFailed.indexOf(records[i].id) == -1) {
                up = accountStore.getById(records[i].id);
                up.set('name', records[i].get('name'));
                up.set('updateInterval', records[i].get('updateInterval'));
                up.set('deleteInterval', records[i].get('deleteInterval'));
                for (var a = 0, lena = items.length; a < lena; a++) {
                    if (items[a].get('groupwareFeedsAccountsId') == records[i].id) {
                        items[a].set('name', records[i].get('name'));        
                    }    
                } 
            }        
        } 
        store.commitChanges();
        
        if (len > 0) {
            if (store.groupField !== false) {
                store.groupBy(store.groupField, true);
            }        
        }
        
        accountStore.commitChanges();
        
        var deletedFailed = values.deletedFailed;
        var feedRecords = store.getRange();
        if (this.deletedRecords && this.deletedRecords.length) {
            for (var i = 0, len = this.deletedRecords.length; i < len; i++) {
                if (deletedFailed.indexOf(this.deletedRecords[i].id) == -1) {
                    // remove accounts
                    accountStore.remove(accountStore.getById(this.deletedRecords[i].id));    
                    // remove feed items
                    for (var a = 0, lena = feedRecords.length; a < lena; a++) {
                        if (feedRecords[a].get('groupwareFeedsAccountsId') == this.deletedRecords[i].id) {
                            store.remove(feedRecords[a]);
                        }
                    }         
                } else {
                    this.feedPanel.store.add(this.deletedRecords[i]);    
                }
            }
        }
        
        
        
        this.saved();       

    },
    
    /**
     * Callback if saving the configuration fails due to network problems or 
     * else.
     */
    onFailure : function(response, parameters)
    {
        de.intrabuild.groupware.ResponseInspector.handleFailure(response);       
           
        this.feedPanel.store.rejectChanges(); 
        this.resetState();
    },
    
    /**
     * Resets listeners and compoenents to their initial state.
     */
    resetState : function()
    {
        this.loadMask.hide();
        this.requestId = null;
        this.buttons[2].disable();
        
        this.removeAfter.on('select',   this.configChanged, this);
        this.updateAfter.on('select',   this.configChanged, this);
        this.feedName.el.on('keyup',    this.configChanged, this);
        this.feedName.el.on('keydown',  this.configChanged, this);
        this.feedName.el.on('keypress', this.configChanged, this);         
        
        this.deletedRecordCount  = 0;
        this.modifiedRecordCount = 0;
    },
    
    
    /**
     * Called when any value in any of the form field changes. This is usually 
     * here to render the "apply" button enabled for the first time.
     */
    configChanged : function(box, record, index)
    {
        this.buttons[2].setDisabled(false);
       
        // detacht listeners, don't need them anymore
        this.removeAfter.un('select',   this.configChanged, this);
        this.updateAfter.un('select',   this.configChanged, this);
        this.feedName.el.un('keyup',    this.configChanged, this);
        this.feedName.el.un('keydown',  this.configChanged, this);
        this.feedName.el.un('keypress', this.configChanged, this);
    },
    
    /**
     * Validates the current value of the feed name text field.
     *
     * A valid feedname must not equal to an empty string and may only 
     * contain literals, numbers and " ", ".", ":", and "-".
     * 
     */
    isFeedNameValid : function(value)
    {
        value = value.toLowerCase().trim();
        
        var collection = this.feedPanel.store.getRange();
        
        var selRecord = this.feedPanel.selModel.getSelected();
        
        // in case the user switches to another record and the function already
        // validated an entry.
        if (selRecord == null) {
            return true;
        }
        
        var record = null;
        
        for (var i = 0, max_i = collection.length; i < max_i; i++) {
            record = collection[i];
            if (selRecord.id == record.id) {
                continue;
            }
            if (record.get('name').toLowerCase() == value) {
                return false;
            }
        }
        
        var alphanum = /^[a-zA-Z0-9_()\/ :.-]+$/;
        return value != "" && alphanum.test(value);
    },
    
    /**
     * Callback before a row is about to be selected
     *
     * @param {Ext.grid.RowSelectionModel} The current row selection model of the 
     *                                     grid.
     * @param {Number} The rowIndex that represents the record that is about to
     *                 be selected in the grid.
     * @param {Boolean}
     * @param {Ext.data.Record} The record that is mapped to the selection 
     *                          that is about to be made in the grid.
     */
    onBeforeRowSelect : function(selModel, rowIndex, keepExisting, record) 
    {
        // check current selection. We can bubble the event and allow selection
        // without additional checks if no record is currently selected 
        if (this.clkRowIndex == -1) {
            return true;
        }

        if (!this.feedName.isValid()) {
            /**
            * @ext-bug we have to defer the displaying of the window, or else it will
            *          be painted in the background and not shown as modal
            */
            this.feedInvalidMessage.defer(0.001, this);   
            return false;
        } 
        
        // before we switch to another panel, set the last selected record fields 
        // and update the grid's row according to the new field name.
        this.saveRecord();
    },
    
    /**
     * Saves the configuration for the last selected record.
     */
    saveRecord : function()
    {
        if (this.clkRecord == null) {
            return;
        }
        this.clkRecord.set('name',    this.feedName.getValue().trim());
        this.clkRecord.set('deleteInterval', this.removeAfter.getValue());
        this.clkRecord.set('updateInterval', this.updateAfter.getValue());
        this.modifiedRecordCount = this.feedPanel.store.getModifiedRecords().length;
    },
    
    /**
     * Simple helper for showing a message that the selected feed name is invalid.
     *
     */
    feedInvalidMessage : function()
    {
        var msg = Ext.MessageBox;
        
        /**
        * @ext-bug we have to defer the displaying of the window, or else it will
        *          be painted in the background and not shown as modal
        */
        var at = this.feedName.el.dom.id;
        msg.show({
            title         : de.intrabuild.Gettext.gettext("Invalid feed name"),
            msg           : de.intrabuild.Gettext.gettext("Please provide a valid name for this feed. The name must be unique."),
            buttons       : msg.OK,
            icon          : msg.WARNING,
            animateTarget : at,
            cls           :'de-intrabuild-msgbox-warning',
            width         : 400
        });           
    },
    
    /**
     * Callback for row deselection.
     * If the current data has no valid feed-name, the row won't be deselected
     * an the user will be informed that he has to input valid data.
     * The current deselected record will be passed to the onBeforeRowSelect
     * method and the current selected rowIndex will be reselected again, 
     * surpressing all events. Thus, as long as the user did not enter a valid feed
     * name, the application focus on the record that is currently selected/wants to
     * be deselected and takes care of the data integrity.
     */
    onRowDeselect : function(selModel, rowIndex, record) 
    {
        if (!this.feedName.isValid()) {
            this.feedPanel.selModel.suspendEvents();
            this.feedPanel.selModel.selectRow(rowIndex);
            this.feedPanel.selModel.resumeEvents();
            /**
             * @ext-bug we have to defer the displaying of the window, or else it will
             *          be painted in the background and not shown as modal
             */
            this.feedInvalidMessage.defer(0.001, this);   
            return false;
        }
        
        // save the last selected record before it gets deselected
        this.saveRecord();
        
        this.clkRowIndex = -1;
        this.clkRecord   = null;
        this.removeFeedButton.setDisabled(true);
        this.formPanel.setVisible(false);
        return true;
    },
    
    /**
     * Callback for the selectionmodel of the grid.
     */
    onRowSelect : function(selModel, rowIndex, record)
    {
        if (this.clkRowIndex == rowIndex) {
            return;
        }
        
        this.clkRowIndex = rowIndex;
        this.clkRecord   = record;
        
        var msg   = Ext.MessageBox;
        var record = selModel.getSelected();
        
        if (record == null) {
            this.formPanel.setVisible(false);    
            return;
        } 
        
        if (this.formPanel.hidden) {
            this.formPanel.setVisible(true);
        }
        
        this.feedUrl.setValue(record.get('uri'));
        this.feedName.setValue(record.get('name'));
        this.updateAfter.setValue(record.get('updateInterval'));
        this.removeAfter.setValue(record.get('deleteInterval'));
        
        this.removeFeedButton.setDisabled(false);
    },
    
    /**
     * Callback when the dialog was rendered.
     * Loads all datastores and selects the first row after this, if available.
     *
     */
    onDialogRendered : function()
    {
        this.formPanel.setVisible(false);
    },
    
    
    /**
     * Shows the load mask and configures it's message text based on the passed
     * userland request type that intiated the load mask.
     */
    showLoadMask : function(requestType)
    {
        if (this.loadMask == null) {
            this.loadMask = new Ext.LoadMask(this.body, this.loadMaskConfig);
        } 
        
        switch (requestType) {
            case this.LOADING:
                this.loadMask.msg = this.msgLoading;
            break;
            case this.SAVING:
                this.loadMask.msg = this.msgSaving;
            break;
        }
    
        this.loadMask.show();
        
    }
    
    
});