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



de.intrabuild.groupware.email.LatestEmailsPanel = function(config) {

    config = config || {};

    config.enableHdMenu = false;

    Ext.apply(this, config);

    Ext.ux.util.MessageBus.subscribe(
        'de.intrabuild.groupware.email.EmailViewBaton.onEmailLoad',
        this.onEmailItemLoad,
        this
    );

    Ext.ux.util.MessageBus.subscribe(
        'de.intrabuild.groupware.email.EmailPreview.onLoadSuccess',
        this.onEmailItemLoad,
        this
    );


// ------------------------- set up buffered grid ------------------------------
    this.store = new Ext.ux.grid.BufferedStore({
        bufferSize  : 100,
        autoLoad    : false,
        reader      : new Ext.ux.data.BufferedJsonReader({
                          root            : 'items',
                          totalProperty   : 'totalCount',
                          versionProperty : 'version',
                          id              : 'id'
                      },
                      de.intrabuild.groupware.email.EmailItemRecord
                      ),
        sortInfo   : {field: 'id', direction: 'DESC'},
        baseParams : {
            minDate : Math.round(new Date().getTime()/1000)
        },
        url : '/groupware/email/get.email.items/format/json'
    });

    this.view = new Ext.ux.grid.BufferedGridView({
        nearLimit : 25,
        loadMask  : {
            msg : de.intrabuild.Gettext.gettext("Please wait...")
        },
        getRowClass : function(record, rowIndex, p, ds){
            if (record.data.isRead) {
                return 'de-intrabuild-groupware-email-LatestEmailsPanel-itemRead';
            } else {
                return 'de-intrabuild-groupware-email-LatestEmailsPanel-itemUnread';
            }
        }
    });

    this.selModel = new Ext.ux.grid.BufferedRowSelectionModel({singleSelect:true});
// ------------------------- ^^ EO set up buffered grid ------------------------

    this.columns = [{
        header    : de.intrabuild.Gettext.gettext("Subject"),
        width     : 160,
        sortable  : false,
        dataIndex : 'subject'
      },{
        header    : de.intrabuild.Gettext.gettext("Sender"),
        width     : 160,
        sortable  : false,
        dataIndex : 'sender'
      }
    ];


    /**
     * Top toolbar
     * @param {Ext.Toolbar}
     */
    this.tbar = new Ext.Toolbar([
        new de.intrabuild.groupware.email.FetchMenuButton()
    ]);


    de.intrabuild.groupware.email.EmailGrid.superclass.constructor.call(this, {
        title          : de.intrabuild.Gettext.gettext("Newest Emails"),
        border         : false,
        iconCls        : 'de-intrabuild-groupware-quickpanel-EmailIcon',
        loadMask       : {
            msg : de.intrabuild.Gettext.gettext("Loading...")
        },
        autoScroll     : true//,
        //cls            : 'de-intrabuild-groupware-email-EmailGrid'
    });

    de.intrabuild.groupware.email.Letterman.on('load', this.newEmailsAvailable, this);


    this.on('contextmenu',    this.onContextClick, this);
    this.on('rowcontextmenu', this.onRowContextClick, this);
    this.on('beforedestroy',  this.onBeforeCmpDestroy, this);

    this.on('render', this.onPanelRender, this);

    de.intrabuild.util.Registry.on('register', this.onRegister, this);

    de.intrabuild.util.Registry.register('de.intrabuild.groupware.email.QuickPanel', this);

    var preview       = de.intrabuild.groupware.email.EmailPreview;
    this.emailPreview = preview;

    this.on('celldblclick',   this.onCellDblClick, this);
    this.on('cellclick',      this.onCellClick,    this, {buffer : 200});
    this.on('resize',         preview.hide.createDelegate(preview, [true]));
    this.on('beforecollapse', preview.hide.createDelegate(preview, [true, false]));
    this.on('contextmenu',    preview.hide.createDelegate(preview, [true]));

};

Ext.extend(de.intrabuild.groupware.email.LatestEmailsPanel, Ext.grid.GridPanel, {

// -------- listeners
    cellClickActive : false,

    onCellClick : function(grid, rowIndex, columnIndex, eventObject)
    {
        if (this.cellClickActive) {
            this.cellClickActive = false;
            return;
        }
        this.emailPreview.show(grid, rowIndex, columnIndex, eventObject);
    },

    onCellDblClick : function(grid, rowIndex, columnIndex, eventObject)
    {
        this.cellClickActive = true;
        var emailItem = grid.getStore().getAt(rowIndex);

        var lr = this.emailPreview.getLastRecord();

        if (lr && lr.id === emailItem.id) {
            de.intrabuild.groupware.email.EmailViewBaton.showEmail(lr, {
                autoLoad : false
            }, true);
        } else {
            de.intrabuild.groupware.email.EmailViewBaton.showEmail(emailItem);
        }

        this.emailPreview.hide(true, false);
    },

    queue : null,

    onEmailsDeleted : function(records)
    {
        var st  = this.store;
        var rec;

        var prev   = de.intrabuild.groupware.email.EmailPreview;
        var prevM  = prev.getActiveRecord;
        var prevId = null;
        for (var i = 0, max_i = records.length; i < max_i; i++) {

            rec = st.getById(records[i].id);

            if (rec) {
                st.remove(rec);
                prevId = prevM();
                if (prevId && prevId.id == rec.id) {
                    prev.hide(true, false);
                }
            }
        }

    },

    onBeforeCmpDestroy : function()
    {
        de.intrabuild.util.Registry.unregister('de.intrabuild.groupware.email.QuickPanel');
    },

    onEmailGridUpdate : function(store, record, operation)
    {
        if (operation == 'commit') {
            var myStore = this.store;
            var rec     = myStore.getById(record.id);
            var up      = 0;
            var data    = record.data;
            if (rec) {
                rec.data.groupwareEmailFoldersId = data.groupwareEmailFoldersId;
                rec.set('isRead', data.isRead);
            }
            myStore.suspendEvents();
            myStore.commitChanges();
            myStore.resumeEvents();
        }
    },

    onRegister : function(name, object)
    {
        if (name != 'de.intrabuild.groupware.email.EmailPanel') {
            return;
        }

        this.onPanelRender();
    },

    onPanelRender : function()
    {
        var sub = de.intrabuild.util.Registry.get('de.intrabuild.groupware.email.EmailPanel');

        if (sub) {
            sub.gridPanel.store.un('update', this.onEmailGridUpdate, this);
            sub.gridPanel.store.on('update', this.onEmailGridUpdate, this);
            sub.on('emailsdeleted', this.onEmailsDeleted, this);
        }
    },

    onEmailItemLoad : function(subject, message)
    {
        var rec = this.store.getById(message.id);

        if (rec) {
            this.setItemsAsRead([rec], true);
        }
    },

//------------------------- Contextmenu related --------------------------------
    processQueue : function()
    {
        var ds = this.store;

        var record = this.queue.shift();
        if (!record) {
            this.queue = null;
            this.view.un('rowsinserted', this.processQueue, this);
            return;
        }

        //var index = ds.findInsertIndex(record);
        ds.insert.defer(0.0001, ds, [0, record]);
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

        this.view.un('rowsinserted', this.processQueue, this);
        this.view.on('rowsinserted', this.processQueue, this);

        for (var i = 0, max_i = records.length; i < max_i; i++) {
            this.queue.push(records[i].copy());
        }

        this.processQueue();
    },
//-------------------------------- Helpers -------------------------------------

    setItemsAsRead : function(records, read)
    {
        var requestArray = [];
        var change = false;
        var rec = null;
        for (var i = 0, max_i = records.length; i < max_i; i++) {
            rec = records[i];
            change = rec.get('isRead') != read;

            if (change) {
                records[i].set('isRead', read);
                requestArray.push({
                    id     : rec.id,
                    isRead : read
                });
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

        this.store.commitChanges();
    },


//------------------------- Contextmenu related --------------------------------

    onContextClick : function(e)
    {
        e.stopEvent();
    },

    onRowContextClick : function(grid, index, e)
    {
        var selModel = this.selModel;

        this.createContextMenu();

        e.stopEvent();

        if (!selModel.isSelected(index)) {
            selModel.selectRow(index, false);
        }

        var subItems  = this.menu.items;

        var ctxRecord = selModel.getSelected().data;
        subItems.get(0).setDisabled((ctxRecord.isRead == true));
        subItems.get(1).setDisabled(!(ctxRecord.isRead == true));

        this.menu.showAt(e.getXY());
    },

    createContextMenu : function()
    {
        if(!this.menu){
            this.menu = new Ext.menu.Menu({
                items: [{
                    text    : de.intrabuild.Gettext.gettext("mark item as read"),
                    scope   : this,
                    handler : function(){this.setItemsAsRead(this.selModel.getSelections(), true);}
                  },{
                    text    : de.intrabuild.Gettext.gettext("mark item as unread"),
                    scope   : this,
                    handler : function(){this.setItemsAsRead(this.selModel.getSelections(), false);}
                  }]
            });
        }
    }



});