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

de.intrabuild.groupware.email.EmailGrid = function(config, controller) {

    Ext.apply(this, config);

    this.controller = controller;
    this.enableHdMenu = false;

// ------------------------- set up buffered grid ------------------------------

    var reader = new Ext.ux.data.BufferedJsonReader({
        root            : 'items',
        totalProperty   : 'totalCount',
        versionProperty : 'version',
        id              : 'id'
    }, de.intrabuild.groupware.email.EmailItemRecord);

    reader.read = function(response){
        var json = response.responseText;
        var o = eval("("+json+")");
        if(!o) {
            throw {message: "JsonReader.read: Json object not found"};
        }

        var z = this.readRecords(o);
        if (o && (o.pendingItems === 0 || o.pendingItems > 0)) {
            z.pendingItems = parseInt(o.pendingItems);
        }

        return z;
    };

    // we receive the pendingItems count through a property
    // in the response object. We store it in the options
    var MyStore = Ext.extend(Ext.ux.grid.BufferedStore, {

        loadRecords: function(o, options, success)
        {
            if (o && (o.pendingItems === 0 || o.pendingItems > 0)) {
                this.pendingItems = o.pendingItems;
            } else {
                this.pendingItems = -1;
            }

            MyStore.superclass.loadRecords.call(this, o, options, success);
        }
    });

    this.store = new MyStore({
        storeId     : Ext.id(),
        bufferSize  : 300,
        autoLoad    : false,
        reader      : reader,
        sortInfo   : {field: 'date', direction: 'DESC'},
        pruneModifiedRecords : true,
        remoteSort  : true,
        listeners   : {
            remove : function (store, record, index) {
                Ext.ux.util.MessageBus.publish('de.intrabuild.groupware.email.EmailGrid.store.remove', {
                    item : record
                });
            },
            update : function (store, record, operation) {
                Ext.ux.util.MessageBus.publish('de.intrabuild.groupware.email.EmailGrid.store.update', {
                    item      : record,
                    operation : operation
                });
            }
        },
        url         : '/groupware/email/get.email.items/format/json'
    });


    this.view = new Ext.ux.grid.BufferedGridView({
        nearLimit   : 100,
        scrollDelay : 0,
        loadMask    : {
            msg : de.intrabuild.Gettext.gettext("Please wait...")
        },
        getRowClass : function(record, rowIndex, p, ds){
            if (record.data.isRead) {
                return 'de-intrabuild-groupware-email-EmailGrid-itemRead';
            } else {
                return 'de-intrabuild-groupware-email-EmailGrid-itemUnread';
            }
        }
    });

    this.selModel = new Ext.ux.grid.BufferedRowSelectionModel();

    /**
     * You can use an instance of BufferedGridToolbar for keeping track of the
     * current scroll position. It also gives you a refresh button and a loading
     * image that gets activated when the store buffers.
     * ...Yeah, I pretty much stole this one from the PagingToolbar!
     */
    this.bbar = new Ext.ux.BufferedGridToolbar({
        grid        : this,
        displayInfo : true,
        displayMsg  : de.intrabuild.Gettext.gettext("Emails {0} - {1} of {2}"),
        emptyMsg    : de.intrabuild.Gettext.gettext("No emails available")
    });

// ------------------------- ^^ EO set up buffered grid ------------------------

    this.columns = [{
        header: '<div class="de-intrabuild-groupware-email-EmailGrid-backgroundContainer '
                + 'de-intrabuild-groupware-email-EmailGrid-attachmentColumn-headerBackground">&#160;</div>',
        width: 25,
        //align:'center',
        sortable: true,
        resizable : false,
        dataIndex: 'isAttachment',
        renderer : function(value, p, record){
               if (!value) {
                   return '&#160';
               }
               return '<div class="de-intrabuild-groupware-email-EmailGrid-backgroundContainer '
                      + 'de-intrabuild-groupware-email-EmailGrid-attachmentColumn-cellBackground">&#160;</div>';
           }
      },{
        header : '<div class="de-intrabuild-groupware-email-EmailGrid-backgroundContainer '
                 + 'de-intrabuild-groupware-email-EmailGrid-readColumn-headerBackground">&#160;</div>',
        width: 25,
        //align:'center',
        sortable: true,
        resizable : false,
        dataIndex: 'isRead',
        renderer : function(value, p, record){
                      return '<div class="de-intrabuild-groupware-email-EmailGrid-backgroundContainer '
                             + 'de-intrabuild-groupware-email-EmailGrid-readColumn-cellBackground '
                             + (value ? 'itemread' : 'itemunread')
                             + '">&#160;</div>';
                   }
      },{
        id : 'subject',
        header: de.intrabuild.Gettext.gettext("Subject"),
        width     : 315,
        sortable  : true,
        dataIndex : 'subject',
        renderer  : de.intrabuild.groupware.email.view.EmailGridRowRenderer.renderSubjectColumn
      },{
        header: de.intrabuild.Gettext.gettext("Sender"),
        width: 160,
        sortable: true,
        dataIndex: 'sender'
      },{
        header: de.intrabuild.Gettext.gettext("Recipients"),
        width: 160,
        sortable: true,
        dataIndex: 'recipients'
      },{
        header : '<div class="de-intrabuild-groupware-email-EmailGrid-backgroundContainer '
                 + 'de-intrabuild-groupware-email-EmailGrid-spamColumn-headerBackground">&#160;</div>',
        width: 25,
        //align:'center',
        resizable : false,
        sortable: true,
        dataIndex: 'isSpam',
        renderer : function(value, p, record){
                       return '<div class="de-intrabuild-groupware-email-EmailGrid-backgroundContainer '
                                 + 'de-intrabuild-groupware-email-EmailGrid-spamColumn-cellBackground '
                                 + (value ? 'itemspam' : 'itemnospam')
                                 + '">&#160;</div>';
           }
      },{
        header: de.intrabuild.Gettext.gettext("Date"),
        width: 100,
        sortable: true,
        dataIndex: 'date',
        renderer: de.intrabuild.groupware.email.view.EmailGridRowRenderer.renderDateColumn
      }
    ];


    de.intrabuild.groupware.email.EmailGrid.superclass.constructor.call(this, {
        loadMask       : {msg: de.intrabuild.Gettext.gettext("Loading...")},
        autoScroll     : true,
        cls            : 'de-intrabuild-groupware-email-EmailGrid',
        trackMouseOver : false,
        enableDragDrop : true,
        ddGroup        : 'de.intrabuild.groupware-email-Email',
        ddText         : de.intrabuild.Gettext.gettext("{0} selected email(s)")
    });

    this.on('contextmenu',    this.onContextClick, this);
    this.on('rowcontextmenu', this.onRowContextClick, this);

    this.store.on('beforeselectionsload', this.onBeforeSelectionsLoad, this);
    this.store.on('selectionsload',       this.onSelectionsLoad,       this);

    this.store.proxy.on('loadexception', this.onLoadException, this);

};

Ext.extend(de.intrabuild.groupware.email.EmailGrid, Ext.grid.GridPanel, {

    onLoadException : function(proxy, options, response, jsError)
    {
        de.intrabuild.groupware.ResponseInspector.handleFailure(response, {
            onLogin: {
                fn : function(){
                    this.view.reset(true);
                },
                scope : this
            }
        });
    },

    onBeforeSelectionsLoad : function()
    {
        this.createContextMenu();

        this.menu.items.get(9).setDisabled(true);
        this.menu.items.get(9).setIconClass('de-intrabuild-groupware-selectionsLoading');

        var subItems  = this.menu.items.get(7).menu.items;
        subItems.get(0).setDisabled(true);
        subItems.get(0).setIconClass('de-intrabuild-groupware-selectionsLoading');
        subItems.get(1).setDisabled(true);
        subItems.get(1).setIconClass('de-intrabuild-groupware-selectionsLoading');
        subItems.get(3).setDisabled(true);
        subItems.get(3).setIconClass('de-intrabuild-groupware-selectionsLoading');
        subItems.get(4).setDisabled(true);
        subItems.get(4).setIconClass('de-intrabuild-groupware-selectionsLoading');

    },

    onSelectionsLoad : function()
    {
        this.createContextMenu();

        this.menu.items.get(9).setDisabled(false);
        this.menu.items.get(9).setIconClass('');

        var subItems  = this.menu.items.get(7).menu.items;
        subItems.get(0).setDisabled(false);
        subItems.get(0).setIconClass('');
        subItems.get(1).setDisabled(false);
        subItems.get(1).setIconClass('');
        subItems.get(3).setIconClass('');
        subItems.get(4).setIconClass('');

        if (this.controller.clkNodeId == this.controller.treePanel.folderSpam.id) {
            subItems.get(3).setDisabled(true);
            subItems.get(4).setDisabled(false);
        } else {
            subItems.get(3).setDisabled(false);
            subItems.get(4).setDisabled(false);
        }
    },


    onContextClick : function(e)
    {
        e.stopEvent();
    },

    onRowContextClick : function(grid, index, e)
    {
        var selModel = this.selModel;

        var nodeType = this.controller.treePanel.getSelectionModel().getSelectedNode();
        if (nodeType) {
            nodeType = nodeType.attributes.type;
        }

        this.createContextMenu();

        e.stopEvent();

        if (!selModel.isSelected(index)) {
            selModel.selectRow(index, false);
        }

        var menuItems = this.menu.items;
        var subItems  = menuItems.get(7).menu.items;

        // mark as spam has to be disabled in outbox, drafts and sent
        var tp = this.controller.treePanel;
        var clkNodeId = this.controller.clkNodeId;
        var isDrafts = clkNodeId == tp.folderDraft.id;
        var disableSpamItems = isDrafts || clkNodeId == tp.folderOutbox.id  ||
                                clkNodeId == tp.folderSent.id;
        var editDraft = menuItems.get(1);
        var openView = menuItems.get(0);
        editDraft.setVisible(isDrafts);

        if (selModel.getCount() == 1) {
            var ctxRecord = selModel.getSelected().data;
            openView.setDisabled(false);
            editDraft.setDisabled(false);
            subItems.get(0).setDisabled((ctxRecord.isRead == true));
            subItems.get(1).setDisabled(!(ctxRecord.isRead == true));
            if (nodeType === 'spam') {
                subItems.get(3).setDisabled(true);
                subItems.get(4).setDisabled(false);
            } else {
                subItems.get(3).setDisabled((ctxRecord.isSpam == true) || disableSpamItems);
                subItems.get(4).setDisabled((ctxRecord.isSpam == false) || disableSpamItems);
            }

        } else {
            openView.setDisabled(true);
            editDraft.setDisabled(true);
            var pendingRanges = this.selModel.getPendingSelections(true);

            if (pendingRanges.length == 0) {
                this.onSelectionsLoad();
            } else {
                this.store.loadSelections(pendingRanges);
            }
        }

        this.menu.showAt(e.getXY());
    },

    createContextMenu : function()
    {
        if(!this.menu){

            var decorateAccountRelatedClk = de.intrabuild.groupware.email.decorator.AccountActionComp.decorate;

            this.menu = new Ext.menu.Menu({
                items: [{
                    text  : de.intrabuild.Gettext.gettext("Open in new tab"),
                    handler : this.controller.openEmailView,
                    scope : this.controller
                  },

                  decorateAccountRelatedClk(new Ext.menu.Item({
                    text    : de.intrabuild.Gettext.gettext("Edit draft"),
                    handler : function(){this.openEmailEditPanel(true, 'edit');},
                    scope   : this.controller
                  })),
                  '-' ,
                  decorateAccountRelatedClk(new Ext.menu.Item({
                    text  : de.intrabuild.Gettext.gettext("Reply"),
                    handler : function(){this.openEmailEditPanel(true, 'reply');},
                    scope : this.controller
                  })),

                  decorateAccountRelatedClk(new Ext.menu.Item({
                    text  : de.intrabuild.Gettext.gettext("Reply all"),
                    handler : function(){this.openEmailEditPanel(true, 'reply_all');},
                    scope : this.controller
                  })),
                  decorateAccountRelatedClk(new Ext.menu.Item({
                    text  : de.intrabuild.Gettext.gettext("Forward"),
                    handler : function(){this.openEmailEditPanel(true, 'forward');},
                    scope : this.controller
                  })), '-', {
                      text : de.intrabuild.Gettext.gettext("Mark email"),
                      menu : {
                          items : [{
                                text    : de.intrabuild.Gettext.gettext("as read"),
                                scope   : this,
                                handler : function(){this.controller.setItemsAsRead(this.selModel.getSelections(), true);}
                              },{
                                text    : de.intrabuild.Gettext.gettext("as unread"),
                                scope   : this,
                                handler : function(){this.controller.setItemsAsRead(this.selModel.getSelections(), false);}
                              },
                              '-',{
                                text    : de.intrabuild.Gettext.gettext("as spam"),
                                scope   : this,
                                handler : function(){this.controller.setItemsAsSpam(this.selModel.getSelections(), true);}
                              },{
                                text    : de.intrabuild.Gettext.gettext("as \"no spam\""),
                                scope   : this,
                                handler : function(){this.controller.setItemsAsSpam(this.selModel.getSelections(), false);}
                          }]
                      }
                  },
                  '-',{
                    text    : de.intrabuild.Gettext.gettext("Delete"),
                    scope   : this,
                    handler : function(){this.controller.deleteEmails(this.selModel.getSelections());}
                }]
            });
        }
    }

});