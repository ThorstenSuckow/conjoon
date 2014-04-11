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

com.conjoon.groupware.email.EmailGrid = Ext.extend(Ext.ux.grid.livegrid.GridPanel, {

    /**
     * @cfg {Ext.Panel} controller The panel that owns this grid
     */
    controller : null,

    /**
     * Saves the state of the nodes so that each folder has its own
     * grid state.
     * @type {Object}
     */
    folderStates : null,

    /**
     * The currently used node id for the state
     * @type {Mixed}
     */
    stateNodeId : null,

    initComponent : function()
    {
        var me = this;

        me.folderStates = {};

    // ------------------------- set up buffered grid ------------------------------

        var reader = new Ext.ux.grid.livegrid.JsonReader({
            root            : 'items',
            totalProperty   : 'totalCount',
            versionProperty : 'version',
            id              : 'id'
        }, com.conjoon.groupware.email.EmailItemRecord);

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
        var MyStore = Ext.extend(Ext.ux.grid.livegrid.Store, {

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
                    Ext.ux.util.MessageBus.publish('com.conjoon.groupware.email.EmailGrid.store.remove', {
                        items : [record]
                    });
                },
                bulkremove : function (store, items) {
                    var records = [];
                    for (var i = 0, len = items.length; i < len; i++) {
                        records.push(items[i][0]);
                    }

                    Ext.ux.util.MessageBus.publish('com.conjoon.groupware.email.EmailGrid.store.remove', {
                        items : records
                    });
                },
                update : function (store, record, operation) {
                    Ext.ux.util.MessageBus.publish('com.conjoon.groupware.email.EmailGrid.store.update', {
                        item      : record,
                        operation : operation
                    });
                }
            },
            url : './groupware/email.item/get.email.items/format/json'
        });


        this.view = new Ext.ux.grid.livegrid.GridView({
            nearLimit   : 100,
            scrollDelay : 0,
            loadMask    : {
                msg : com.conjoon.Gettext.gettext("Please wait...")
            },
            getRowClass : function(record, rowIndex, p, ds){
                if (record.data.isRead) {
                    return 'com-conjoon-groupware-email-EmailGrid-itemRead';
                } else {
                    return 'com-conjoon-groupware-email-EmailGrid-itemUnread';
                }
            }
        });

        this.selModel = new Ext.ux.grid.livegrid.RowSelectionModel();

        /**
         * You can use an instance of BufferedGridToolbar for keeping track of the
         * current scroll position. It also gives you a refresh button and a loading
         * image that gets activated when the store buffers.
         * ...Yeah, I pretty much stole this one from the PagingToolbar!
         */
        this.bbar = new Ext.ux.grid.livegrid.Toolbar({
            view        : this.view,
            displayInfo : true,
            displayMsg  : com.conjoon.Gettext.gettext("Emails {0} - {1} of {2}"),
            emptyMsg    : com.conjoon.Gettext.gettext("No emails available")
        });

    // ------------------------- ^^ EO set up buffered grid ------------------------

        this.colModel = new com.conjoon.groupware.email.data.DefaultColumnModel();

        Ext.apply(this, {
            stateful : true,
            stateId : conjoon.state.base.Identifiers.emailModule.contentPanel.mailItemsGrid,
            loadMask       : {msg: com.conjoon.Gettext.gettext("Loading...")},
            autoScroll     : true,
            plugins     : [
                new com.conjoon.groupware.email.view.GridViewMenuPlugin()
            ],
            stripeRows     : true,
            cls            : 'com-conjoon-groupware-email-EmailGrid',
            trackMouseOver : false,
            enableDragDrop : true,
            ddGroup        : 'com.conjoon.groupware-email-Email',
            ddText         : com.conjoon.Gettext.gettext("{0} selected email(s)")
        });

        com.conjoon.groupware.email.EmailGrid.superclass.initComponent.call(this);
    },

    /**
     * Returns the state for the currently selected folder.
     *
     * @return {Object}
     */
    getState : function() {

        var me = this,
            clkNodeId = me.controller.clkNodeId;

        if (me.stateful !== true || !me.stateId || me.stateNodeId != clkNodeId) {
            return;
        }

        if (me.stateNodeId != clkNodeId) {
            return;
        }

        me.folderStates[clkNodeId] =
            com.conjoon.groupware.email.EmailGrid.superclass.getState.apply(me);

        return me.folderStates;

    },

    /**
     * Applies the state for this grid based on the specified node id.
     * If the node id is not submitted, the state will be assigned to this
     * folderStates, which will then serve as the "state" container.
     * This method gets called from outside whenever the selection in the
     * folderTreePanel changes.
     *
     * @param {Object} state The state object to assign to this.folderStates, or
     * to the grid representing the contents of the specified clkNodeId
     * @param {String} clkNodeId The id of the tree node representing the mail folder
     * currently being investigated
     */
    applyState : function(state, clkNodeId) {

        var me = this;

        if (me.stateful !== true || !me.stateId) {
            return;
        }

        if (!clkNodeId) {
            me.folderStates = state;
            return;
        }

        me.stateNodeId = clkNodeId;

        state = state[clkNodeId];

        if (!state) {
            return;
        }
        com.conjoon.groupware.email.EmailGrid.superclass.applyState.call(me, state);

    },

    initEvents : function()
    {
        com.conjoon.groupware.email.EmailGrid.superclass.initEvents.call(this);

        this.on('contextmenu',    this.onContextClick, this);
        this.on('rowcontextmenu', this.onRowContextClick, this);

        this.mon(this.store, 'beforeselectionsload', this.onBeforeSelectionsLoad, this);
        this.mon(this.store, 'selectionsload',       this.onSelectionsLoad,       this);
        this.mon(this.store, 'insertindexfound',     this.onInsertIndexFound,     this);

        this.mon(this.store, 'exception', this._onException, this);

        if (this.loadMask) {
            this.store.un('beforeload', this.loadMask.onBeforeLoad, this.loadMask);
            this.store.on('beforeload', this.loadMask.onBeforeLoad, this.loadMask, {delay : 1});
        }
    },

    /**
     *
     * @param {Ext.data.Proxy} proxy The proxy that sent the request
     * @param {String} type The value of this parameter will be either 'response'
     * or 'remote'.
     *  - 'response': An invalid response from the server was returned: either 404,
     *                500 or the response meta-data does not match that defined in
     *                the DataReader (e.g.: root, idProperty, successProperty).
     *   - 'remote':  A valid response was returned from the server having
     *                successProperty === false. This response might contain an
     *                error-message sent from the server. For example, the user may have
     *                failed authentication/authorization or a database validation error
     *                occurred.
     * @param {String} action Name of the action (see Ext.data.Api.actions)
     * @param {Object} options The options for the action that were specified in the
     * request.
     * @param {Object} response The value of this parameter depends on the value of the
     * type parameter:
     *   - 'response': The raw browser response object (e.g.: XMLHttpRequest)
     *   - 'remote': The decoded response object sent from the server.
     * @param {Mixed} arg The type and value of this parameter depends on the value of
     * the type parameter:
     *   - 'response': Error The JavaScript Error object caught if the configured Reader
     *                 could not read the data. If the remote request returns
     *                 success===false, this parameter will be null.
     *   - 'remote': Record/Record[] This parameter will only exist if the action was a
     *               write action (Ext.data.Api.actions.create|update|destroy).
     *
     */
    _onException : function(proxy, type, action, options, response, arg)
    {
        com.conjoon.groupware.ResponseInspector.handleFailure(response, {
            onLogin: {
                fn : function(){
                    this.view.reset(true);
                },
                scope : this
            }
        });
    },

    /**
     * Listener for the livegrid store's insertindexfound event.
     * Adjusts the index so records can be appended to the view if the last set
     * of records is loaded.
     *
     * @param {Ext.ux.grid.livegrid.Store} store
     * @param {Number} index
     *
     */
    onInsertIndexFound : function(store, indexObj)
    {
        var view  = this.view,
            index = indexObj.insertIndex;

        if (index == Number.MAX_VALUE
            && (view.visibleRows - 1) + view.rowIndex == store.getTotalCount()) {
            indexObj.insertIndex = store.bufferSize;
        }

    },

    onBeforeSelectionsLoad : function()
    {
        this.createContextMenu();

        this.menu.items.get(10).setDisabled(true);
        this.menu.items.get(10).setIconClass('com-conjoon-groupware-selectionsLoading');

        var subItems  = this.menu.items.get(8).menu.items;
        subItems.get(0).setDisabled(true);
        subItems.get(0).setIconClass('com-conjoon-groupware-selectionsLoading');
        subItems.get(1).setDisabled(true);
        subItems.get(1).setIconClass('com-conjoon-groupware-selectionsLoading');


        subItems.get(3).setDisabled(true);
        subItems.get(3).setIconClass('com-conjoon-groupware-selectionsLoading');
        subItems.get(4).setDisabled(true);
        subItems.get(4).setIconClass('com-conjoon-groupware-selectionsLoading');



    },

    onSelectionsLoad : function()
    {
        this.createContextMenu();

        this.menu.items.get(10).setDisabled(false);
        this.menu.items.get(10).setIconClass('');

        var subItems  = this.menu.items.get(8).menu.items;
        subItems.get(0).setDisabled(false);
        subItems.get(0).setIconClass('');
        subItems.get(1).setDisabled(false);
        subItems.get(1).setIconClass('');
        subItems.get(3).setIconClass('');
        subItems.get(4).setIconClass('');

        if (this.controller.treePanel.isFolderOfType(this.controller.clkNodeId, 'junk')) {
        //if (this.controller.clkNodeId == this.controller.treePanel.folderSpam.id) {
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
        var subItems  = menuItems.get(8).menu.items;

        // mark as spam has to be disabled in outbox, drafts and sent
        var tp = this.controller.treePanel;
        var clkNodeId = this.controller.clkNodeId;

        var isDrafts = tp.isFolderOfType(clkNodeId, 'draft');
        var isOutbox = tp.isFolderOfType(clkNodeId, 'outbox');

        var disableSpamItems = isDrafts
                               || tp.isFolderOfType(clkNodeId, 'draft')
                               || tp.isFolderOfType(clkNodeId, 'sent');

        var sendNowItem = menuItems.get(1);
        var editDraft   = menuItems.get(2);
        var openView    = menuItems.get(0);

        var replyItem    = menuItems.get(4);
        var replyAllItem = menuItems.get(5);
        var forwardItem  = menuItems.get(6);

        editDraft.setVisible(isDrafts);
        sendNowItem.setVisible(isOutbox);

        if (selModel.getCount() == 1) {
            var ctxRecord = selModel.getSelected().data;
            openView.setDisabled(false);
            editDraft.setDisabled(false);
            sendNowItem.setDisabled(false);
            replyItem.setDisabled(false);
            replyAllItem.setDisabled(false);
            forwardItem.setDisabled(false);
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
            sendNowItem.setDisabled(true);
            replyItem.setDisabled(true);
            replyAllItem.setDisabled(true);
            forwardItem.setDisabled(true);
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

            var decorateAccountRelatedClk = com.conjoon.groupware.email.decorator.AccountActionComp.decorate;

            this.menu = new Ext.menu.Menu({
                enableScrolling : false,
                items           : [{
                    text  : com.conjoon.Gettext.gettext("Open in new tab"),
                    handler : this.controller.openEmailView,
                    scope : this.controller
                  },
                    decorateAccountRelatedClk(new Ext.menu.Item({
                    text    : com.conjoon.Gettext.gettext("Send now"),
                    handler : function(){this.sendPendingItems();},
                    scope   : this.controller
                  })),
                  decorateAccountRelatedClk(new Ext.menu.Item({
                    text    : com.conjoon.Gettext.gettext("Edit draft"),
                    handler : function(){this.openEmailEditPanel(true, 'edit');},
                    scope   : this.controller
                  })),
                  '-' ,
                  decorateAccountRelatedClk(new Ext.menu.Item({
                    text  : com.conjoon.Gettext.gettext("Reply"),
                    handler : function(){this.openEmailEditPanel(true, 'reply');},
                    scope : this.controller
                  })),

                  decorateAccountRelatedClk(new Ext.menu.Item({
                    text  : com.conjoon.Gettext.gettext("Reply all"),
                    handler : function(){this.openEmailEditPanel(true, 'reply_all');},
                    scope : this.controller
                  })),
                  decorateAccountRelatedClk(new Ext.menu.Item({
                    text  : com.conjoon.Gettext.gettext("Forward"),
                    handler : function(){this.openEmailEditPanel(true, 'forward');},
                    scope : this.controller
                  })), '-', {
                      text : com.conjoon.Gettext.gettext("Mark email"),
                      menu : {
                          items : [{
                                text    : com.conjoon.Gettext.gettext("as read"),
                                scope   : this,
                                handler : function(){this.controller.setItemsAsRead(this.selModel.getSelections(), true);}
                              },{
                                text    : com.conjoon.Gettext.gettext("as unread"),
                                scope   : this,
                                handler : function(){this.controller.setItemsAsRead(this.selModel.getSelections(), false);}
                              },
                              '-',{
                                text    : com.conjoon.Gettext.gettext("as spam"),
                                scope   : this,
                                handler : function(){this.controller.setItemsAsSpam(this.selModel.getSelections(), true);}
                              },{
                                text    : com.conjoon.Gettext.gettext("as \"no spam\""),
                                scope   : this,
                                handler : function(){this.controller.setItemsAsSpam(this.selModel.getSelections(), false);}
                          }]
                      }
                  },
                  '-',{
                    text    : com.conjoon.Gettext.gettext("Delete"),
                    scope   : this,
                    handler : function(){this.controller.deleteEmails(this.selModel.getSelections());}
                }]
            });
        }
    }

});
