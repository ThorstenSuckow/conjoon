/**
 * conjoon
 * (c) 2002-2009 siteartwork.de/conjoon.org
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

/**
 * The dialog uses a tree panel for showing all feeds.
 *
 * @todo Store loading could fail. This should be intercepted in any way.
 */
com.conjoon.groupware.feeds.FeedOptionsDialog = Ext.extend(Ext.Window, {

    LOADING         : 0,
    SAVING          : 1,

    msgLoading  : com.conjoon.Gettext.gettext("Loading feed configurations..."),
    msgSaving   : com.conjoon.Gettext.gettext("Saving configurationen..."),

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
     * @type {Boolean} ignoreConfigChange Whether the configChange method should
     * get processed. Will be temporarily set to true when the form fields get
     * filled automatically
     */
    ignoreConfigChange : false,


    /**
     * LoadMask configuration
     */
    loadMaskConfig : {
        msg : com.conjoon.Gettext.gettext("Processing...")
    },

    /**
     * The load mask for this dialog. Used for showing process of saving and
     * reading data.
     */
    loadMask : null,


    initComponent : function()
    {
        this.deletedRecords = [];

        this.store = new Ext.data.Store({
                pruneModifiedRecords : true,
                storeId              : Ext.id(),
                autoLoad             : false,
                reader               : new Ext.data.JsonReader({
                     id : 'id'
                }, com.conjoon.groupware.feeds.AccountRecord)
            });


        /**
         * The panel that holds the tree nodes. Rendered like a combo box.
         */
        this.feedPanel = new Ext.grid.GridPanel({
            cls        : 'feedPanel',
            autoScroll : true,
            height     : 240,
            hideHeaders : true,
            enableColumnMove : false,
            enableHdMenu : false,
            store  : this.store,
            sortInfo   : {field: 'name', direction: "DESC"},
            selModel   : new Ext.grid.RowSelectionModel({singleSelect:true}),
            columns    : [{
                id        : 'feed_name',
                header    : '<b>'+com.conjoon.Gettext.gettext("Feeds")+'</b>',
                width     : 148,
                sortable  : true,
                dataIndex : 'name'
            }]
        });

        var tmpStore = com.conjoon.groupware.feeds.AccountStore.getInstance();
        var records = tmpStore.getRange();
        this.mon(tmpStore, 'add', this.addRecordsFromStore, this);
        for (var i = 0, len = records.length; i < len; i++) {
            this.store.add(records[i].copy());
        }

        /**
         * Button for adding a feed
         */
        this.addFeedButton = new Ext.Button({
            text     : com.conjoon.Gettext.gettext("Add feed"),
            cls      : 'com-conjoon-margin-b-5',
            minWidth : 150,
            scope    : this,
            handler  : function(){
                var dialog = new com.conjoon.groupware.feeds.AddFeedDialog({animateTarget : this.addFeedButton.el.dom.id});
                dialog.show();
            }
        });

        /**
         * Button for removing a feed
         */
        this.removeFeedButton = new Ext.Button({
            text     : com.conjoon.Gettext.gettext("Remove feed"),
            minWidth : 150,
            disabled : true,
            handler  : this.removeSelected,
            scope    : this
        });

        /**
         * Textfield for the feeds url. This is immutable.
         */
        this.feedUrl = new Ext.form.TextField({
            fieldLabel : com.conjoon.Gettext.gettext("Address"),
            disabled   : true,
            disabledClass : ''
        });

        /**
         * Textfield for the feeds name.
         */
        this.feedName = new Ext.form.TextField({
            fieldLabel      : com.conjoon.Gettext.gettext("Name"),
            allowBlank      : false,
            validator       : this.isFeedNameValid.createDelegate(this),
            itemCls         : 'com-conjoon-margin-b-10',
            enableKeyEvents : true
        });

        /**
         * Combobox for choosing the request timeout in seconds.
         *
         * @type Ext.form.ComboBox
         */
        this.requestTimeoutComboBox = new Ext.form.ComboBox({
            tpl           : '<tpl for="."><div class="x-combo-list-item">{text:htmlEncode}</div></tpl>',
            fieldLabel    : com.conjoon.Gettext.gettext("Request timeout"),
            listClass     : 'com-conjoon-smalleditor',
            displayField  : 'text',
            valueField    : 'id',
            mode          : 'local',
            editable      : false,
            triggerAction : 'all',
            store         : new Ext.data.SimpleStore({
                data   : [
                    [30, com.conjoon.Gettext.gettext("30 seconds")],
                    [20, com.conjoon.Gettext.gettext("20 seconds")],
                    [10, com.conjoon.Gettext.gettext("10 seconds")]
                ],
                fields : ['id', 'text']
            })
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
            fieldLabel    : com.conjoon.Gettext.gettext("Save entries"),
            listClass     : 'com-conjoon-smalleditor',
            itemCls       : 'com-conjoon-margin-b-10',
            displayField  : 'text',
            valueField    : 'id',
            mode          : 'local',
            editable      : false,
            triggerAction : 'all',
            store         : new Ext.data.SimpleStore({
                data   : [
                    [2419200, com.conjoon.Gettext.gettext("for 2 weeks")],
                    [1209600, com.conjoon.Gettext.gettext("for one week")],
                    [432000,  com.conjoon.Gettext.gettext("for 5 days")],
                    [172800,  com.conjoon.Gettext.gettext("for 2 days")],
                    [86400,   com.conjoon.Gettext.gettext("for one day")],
                    [43200,   com.conjoon.Gettext.gettext("for 12 hours")],
                    [21600,   com.conjoon.Gettext.gettext("for 6 hours")],
                    [7200,    com.conjoon.Gettext.gettext("for 2 hours")],
                    [3600,    com.conjoon.Gettext.gettext("for one hour")]
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
            fieldLabel    : com.conjoon.Gettext.gettext("Check for new entries"),
            itemCls       : 'com-conjoon-margin-b-10',
            listClass     : 'com-conjoon-smalleditor',
            displayField  : 'text',
            valueField    : 'id',
            mode          : 'local',
            editable      : false,
            triggerAction : 'all',
            store         : new Ext.data.SimpleStore({
                data   : [
                    [172800, com.conjoon.Gettext.gettext("every 2 days")],
                    [86400,  com.conjoon.Gettext.gettext("every day")],
                    [43200,  com.conjoon.Gettext.gettext("every 12 hours")],
                    [21600,  com.conjoon.Gettext.gettext("every 6 hours")],
                    [7200,   com.conjoon.Gettext.gettext("every 2 hours")],
                    [3600,   com.conjoon.Gettext.gettext("every hour")],
                    [1800,   com.conjoon.Gettext.gettext("every 30 minutes")],
                    [900,    com.conjoon.Gettext.gettext("every 15 minutes")]
                ],
                fields : ['id', 'text']
            })

        });

        /**
         * @type {Ext.form.Checkbox} enableImagesCheckbox
         */
        this.enableImagesCheckbox = new Ext.form.Checkbox({
            fieldLabel : com.conjoon.Gettext.gettext("Load images into feed"),
            itemCls    : 'com-conjoon-margin-t-5'
        });

        this.formPanel = new Ext.TabPanel({
            deferredRender : false,
            activeItem     : 0,
            margins        : '5 5 5 5',
            hideMode       : 'visibility',
            id             : 'com.conjoon.groupware.feeds.FeedOptionsDialog.formPanel',
            region         : 'center',
            bodyStyle      : 'background-color:#F6F6F6;',
            defaults       : {
                border : false
            },
            cls   : 'tabPanel',

            items : [
                new Ext.FormPanel({
                    baseCls    : 'x-small-editor',
                    defaults   : {
                        labelStyle : 'font-size:11px',
                        anchor     : '100%'
                    },
                    bodyStyle  : 'margin:10px 0 20px 0px;padding:10px;background:none',
                    title : com.conjoon.Gettext.gettext("Connection"),
                    items : [
                        this.feedUrl,
                        this.feedName,
                        new Ext.form.FieldSet({
                            defaults : {
                                labelStyle : 'font-size:11px',
                                anchor     : '100%'
                            },
                            title      : com.conjoon.Gettext.gettext("Options"),
                            labelAlign : 'top',
                            items      : [
                                this.updateAfter,
                                this.removeAfter,
                                this.requestTimeoutComboBox
                            ]
                        })
                    ]
                }),
                new Ext.FormPanel({
                    baseCls    : 'x-small-editor',
                    defaults   : {
                        labelStyle : 'width:150px;font-size:11px'
                    },
                    bodyStyle  : 'margin:10px 0 20px 0px;padding:10px;background:none',
                    title  : com.conjoon.Gettext.gettext("Security"),
                    items  : [
                        new com.conjoon.groupware.util.FormIntro({
                            label : com.conjoon.Gettext.gettext("Allow cross domain resources"),
                            text  : com.conjoon.Gettext.gettext("Some feeds may be delivered with images, which pose a potential security risc: By loading this images into your browser from another domain, you may expose personal details such as your IP address and there is also a potential risc related to XSS attack (cross site scripting).<br /> You should only enable loading images from those sites that you can trust.")
                        }),
                        this.enableImagesCheckbox

                    ]
                })
            ]
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
          },
          this.formPanel
        ];

        this.buttons = [{
            text    : 'OK',
            handler : function(){this.saveConfiguration(true);},
            scope   : this
          },{
            text    : com.conjoon.Gettext.gettext("Cancel"),
            handler : this.close,
            scope   : this
          },{
            text     : com.conjoon.Gettext.gettext("Apply"),
            handler  : this.saveConfiguration,
            scope    : this,
            disabled : true
        }];

        Ext.apply(this, {
            iconCls   : 'com-conjoon-groupware-feeds-Icon',
            cls       : 'com-conjoon-groupware-feeds-FeedOptionsDialog',
            title     : com.conjoon.Gettext.gettext("Feed settings"),
            bodyStyle : 'background-color:#F6F6F6',
            height    : 375,
            width     : 550,
            modal     : true,
            resizable : false,
            layout    : 'border'
        });

        this.on('render', this.onDialogRendered, this, {single : true});

        com.conjoon.groupware.feeds.FeedOptionsDialog.superclass.initComponent.call(this);
    },

    initEvents : function()
    {
        com.conjoon.groupware.feeds.FeedOptionsDialog.superclass.initEvents.call(this);

        this.on('beforeclose', this._onBeforeClose, this);

        // add listener
        this.mon(this.feedPanel.selModel, 'beforerowselect', this.onBeforeRowSelect, this);
        this.mon(this.feedPanel.selModel, 'rowselect',       this.onRowSelect, this);
        this.mon(this.feedPanel.selModel, 'rowdeselect',     this.onRowDeselect, this);

        this.mon(this.removeAfter, 'select', this.configChanged, this);
        this.mon(this.updateAfter, 'select', this.configChanged, this);

        this.mon(this.requestTimeoutComboBox, 'select', this.configChanged, this);
        this.mon(this.enableImagesCheckbox, 'check', this.configChanged, this);

        this.mon(this.feedName, 'keyup',    this.configChanged, this);
        this.mon(this.feedName, 'keydown',  this.configChanged, this);
        this.mon(this.feedName, 'keypress', this.configChanged, this);
    },

    /**
     * Gets called when a feed was added via the AddFeedDialog and this dialog
     * was closed
     * @param {e.conjoon.groupware.feeds-FeedRecord} The newly added feed
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
            title   : com.conjoon.Gettext.gettext("Remove feed"),
            msg     : String.format(
                com.conjoon.Gettext.gettext("Do you really want to remove \"{0}\"?"),
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
            cls     :'com-conjoon-msgbox-question',
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
        for (var a = 0, max_a = this.deletedRecords.length; a < max_a; a++) {
            deleted.push(this.deletedRecords[a].id);
        }

        for (var i = 0, max_i = records.length; i < max_i; i++) {
            updated.push(records[i].data);
        }

        // nothing to do?
        if (a == 0 && i == 0) {
            this.saved();
            return;
        }

        this.buttons[0].disable();
        this.buttons[1].disable();
        this.buttons[2].disable();

        this.requestId = Ext.Ajax.request({
            url            : './groupware/feeds/update.accounts/format/json',
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
    saved : function(failed)
    {
        if (!failed && this.closeAfterSave) {
            this.close();
            return;
        }

        this.fillFormFields(this.clkRecord);

        this.resetState(failed);
    },

    /**
     * Callback for the request that was send savong the feed configurations.
     */
    onSuccess : function(response, parameters)
    {
        this.requestId = null;

        // shorthands
        var json = com.conjoon.util.Json;
        var msg  = Ext.MessageBox;

        var failed = false;

        if (json.isError(response.responseText)) {
            this.onFailure(response, parameters);
            return;
        }

        var values        = json.getResponseValues(response.responseText);
        var updatedFailed = values.updatedFailed;
        var deletedFailed = values.deletedFailed;

        failed = !updatedFailed || !deletedFailed
                 || (updatedFailed && updatedFailed.length)
                 || (deletedFailed && deletedFailed.length);

        this.syncStores(updatedFailed, deletedFailed);

        if (values.success != true) {
            failed = true;
            var updf = [];
            var delf = [];
            if (updatedFailed) {
                for (var i = 0, len = updatedFailed.length; i < len; i++) {
                    var rec = this.feedPanel.store.getById(updatedFailed[i]);
                    if (rec) {
                        updf.push(rec.get('name'));
                    }
                }
            }
            if (deletedFailed) {
                for (var i = 0, len = this.deletedRecords.length; i < len; i++) {
                    if (deletedFailed.indexOf(this.deletedRecords[i].id) != -1) {
                        delf.push(this.deletedRecords[i].get('name'));
                    }
                }
            }

            msg.show({
                title   : com.conjoon.Gettext.gettext("Error"),
                msg     : com.conjoon.Gettext.gettext("The server could not process all of the changes.") +
                          (updf.length
                          ? "<br />" +
                             String.format(
                                com.conjoon.Gettext.gettext("The following entries could not be updated: {0}") + "<br />",
                                updf.join("<br />")
                             )
                          : "") +
                          (
                          delf.length
                          ? "<br />" +
                             String.format(
                                com.conjoon.Gettext.gettext("The following entries could not be removed: {0}") + "<br />",
                                delf.join("<br />")
                             )
                           : ""),
                buttons : msg.OK,
                icon    : msg.ERROR,
                cls     :'com-conjoon-msgbox-error',
                width   : 400
            });
        }

        this.saved(failed);
    },

    /**
     * Syncs the account store with the store created for this dialog.
     * Removes all deleted records which are not found in deletedFailed, and
     * updates all records which are found in updatedFailed.
     *
     * @param {Array} updatedFailed array with ids of records which could not
     * be updated on the server
     * @param {Array} deletedFailed array with ids of records which could not
     * be removed from the server
     *
     */
    syncStores : function(updatedFailed, deletedFailed)
    {
        // assume that updating all records failed if updatedFailed is
        // not an array
        if (!updatedFailed) {
            updatedFailed = [];
            var recs = this.feedPanel.store.getModifiedRecords();
            for (var i = 0, len = recs.length; i < len; i++) {
                updatedFailed.push(recs[i].id);
            }
        }

        // assume that deleting all records failed if deletedFailed is
        // not an array
        if (!deletedFailed) {
            deletedFailed = [];
            for (var i = 0, len = this.deletedRecords.length; i < len; i++) {
                deletedFailed.push(this.deletedRecords[i].id);
            }
        }

        var accountStore = com.conjoon.groupware.feeds.AccountStore.getInstance();
        var store        = com.conjoon.groupware.feeds.FeedStore.getInstance();

        var trecords = this.feedPanel.store.getModifiedRecords();
        var items   = store.getRange();

        var up = null;

        // copy into new array, otherwise a reference to the store's
        // modified records will be created which will lead to problems
        // when we commit a single record later on.
        var records = [];
        for (var i = 0, len = trecords.length; i < len; i++) {
            records.push(trecords[i]);
        }

        for (var i = 0, len = records.length; i < len; i++) {
            if (updatedFailed.indexOf(records[i].id) == -1) {
                records[i].commit();
                up = accountStore.getById(records[i].id);
                up.set('name', records[i].get('name'));
                up.set('updateInterval', records[i].get('updateInterval'));
                up.set('deleteInterval', records[i].get('deleteInterval'));
                up.set('requestTimeout', records[i].get('requestTimeout'));
                up.set('isImageEnabled', records[i].get('isImageEnabled'));
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

        var feedRecords = store.getRange();
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
    },

    /**
     * Callback if saving the configuration fails due to network problems or
     * else.
     *
     * The method will also consider a failure due to an error returned by the server,
     * but the updatedFailed/deletedFailed properties may be set. The method will
     * then reject changes for all records with the id found in updated Failed, and add
     * again the records from deletedFailed.
     */
    onFailure : function(response, parameters)
    {
        this.requestId = null;

        com.conjoon.groupware.ResponseInspector.handleFailure(response);

        var values = {};

        try {
            values = json.getResponseValues(response.responseText);
        } catch (e) {
            // ignore
        }

        this.syncStores(values.updatedFailed, values.deletedFailed);
        this.saved(true);
    },

    fillFormFields : function(record)
    {
        this.ignoreConfigChange = true;

        if (record) {
            this.feedUrl.setValue(record.get('uri'));
            this.feedName.setValue(record.get('name'));
            this.updateAfter.setValue(record.get('updateInterval'));
            this.requestTimeoutComboBox.setValue(record.get('requestTimeout'));
            this.enableImagesCheckbox.setValue(record.get('isImageEnabled'));
            this.removeAfter.setValue(record.get('deleteInterval'));
        }

        this.ignoreConfigChange = false;
    },

    /**
     * Resets listeners and compoenents to their initial state.
     */
    resetState : function(failed)
    {
        this.deletedRecords = [];
        this.loadMask.hide();
        this.requestId = null;

        this.buttons[0].enable();
        this.buttons[1].enable();
        if (failed === true) {
            this.buttons[2].enable();
        } else {
            this.buttons[2].disable();
        }

        this.deletedRecordCount  = 0;
        this.modifiedRecordCount = 0;
    },


    /**
     * Called when any value in any of the form field changes. This is usually
     * here to render the "apply" button enabled for the first time.
     */
    configChanged : function(box, record, index)
    {
        if (this.ignoreConfigChange) {
            return;
        }

        if (!this.feedName.isValid()) {
            this.buttons[0].setDisabled(true);
            this.buttons[2].setDisabled(true);
            return;
        }
        this.buttons[0].setDisabled(false);
        this.buttons[2].setDisabled(false);
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
        this.clkRecord.set('requestTimeout', this.requestTimeoutComboBox.getValue());
        this.clkRecord.set('isImageEnabled', this.enableImagesCheckbox.getValue());
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
            title         : com.conjoon.Gettext.gettext("Invalid feed name"),
            msg           : com.conjoon.Gettext.gettext("Please provide a valid name for this feed. The name must be unique."),
            buttons       : msg.OK,
            icon          : msg.WARNING,
            animateTarget : at,
            cls           :'com-conjoon-msgbox-warning',
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

        this.fillFormFields(record);

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

    _onBeforeClose : function()
    {
        if (this.requestId != null) {
            return false;
        }

        return true;
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