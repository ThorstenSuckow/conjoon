/**
 * conjoon
 * (c) 2007-2015 conjoon.org
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


com.conjoon.groupware.email.LatestEmailsPanel = Ext.extend(Ext.ux.grid.livegrid.GridPanel, {

    cellClickActive : false,

    initComponent : function()
    {

    // ------------------------- set up buffered grid ------------------------------
        this.store = new Ext.ux.grid.livegrid.Store({
            bufferSize  : 100,
            autoLoad    : false,
            reader      : new Ext.ux.grid.livegrid.JsonReader({
                              root            : 'items',
                              totalProperty   : 'totalCount',
                              versionProperty : 'version',
                              id              : 'id'
                          },
                          com.conjoon.groupware.email.EmailItemRecord
                          ),
            sortInfo   : {field: 'id', direction: 'DESC'},
            baseParams : {
                minDate : Math.floor(new Date().getTime()/1000)
            },
            listeners   : {
                remove : function (store, record, index) {
                    Ext.ux.util.MessageBus.publish('com.conjoon.groupware.email.LatestEmailsPanel.store.remove', {
                        items : [record]
                    });
                },
                bulkremove : function (store, items) {
                    var records = [];
                    for (var i = 0, len = items.length; i < len; i++) {
                        records.push(items[i][0]);
                    }

                    Ext.ux.util.MessageBus.publish('com.conjoon.groupware.email.LatestEmailsPanel.store.remove', {
                        items : records
                    });
                },
                update : function (store, record, operation) {
                    Ext.ux.util.MessageBus.publish('com.conjoon.groupware.email.LatestEmailsPanel.store.update', {
                        item      : record,
                        operation : operation
                    });
                }
            },
            url : './groupware/email.item/get.email.items/format/json'
        });

        this.view = new Ext.ux.grid.livegrid.GridView({
            nearLimit : 25,
            loadMask  : {
                msg : com.conjoon.Gettext.gettext("Please wait...")
            },
            getRowClass : function(record, rowIndex, p, ds){
                if (record.data.isRead) {
                    return 'com-conjoon-groupware-email-LatestEmailsPanel-itemRead';
                } else {
                    return 'com-conjoon-groupware-email-LatestEmailsPanel-itemUnread';
                }
            }
        });

        // when changing from single select adjust the params being sent with the
        // set.email.flag request
        this.selModel = new Ext.ux.grid.livegrid.RowSelectionModel({singleSelect:true});
    // ------------------------- ^^ EO set up buffered grid ------------------------

        this.columns = [{
            header    : com.conjoon.Gettext.gettext("Subject"),
            width     : 160,
            sortable  : false,
            dataIndex : 'subject',
            renderer : function(value, metadata, record){
                metadata.attr = 'ext:qtip="'+value.replace(/"/g, '&quot;')+'"';
                return value;
            }
          },{
            header    : com.conjoon.Gettext.gettext("Sender"),
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
            new com.conjoon.groupware.email.FetchMenuButton()
        ]);


        Ext.apply(this, {
            enableHdMenu   : false,
            title          : com.conjoon.Gettext.gettext("Newest Emails"),
            iconCls        : 'com-conjoon-groupware-quickpanel-EmailIcon',
            loadMask       : {
                msg : com.conjoon.Gettext.gettext("Loading...")
            },
            autoScroll     : true//,
            //cls            : 'com-conjoon-groupware-email-EmailGrid'
        });


        com.conjoon.util.Registry.register('com.conjoon.groupware.email.QuickPanel', this);

        this.on('render', this.onPanelRender, this, {single : true});

        com.conjoon.groupware.email.LatestEmailsPanel.superclass.initComponent.call(this);
    },


    initEvents : function()
    {
        Ext.ux.util.MessageBus.subscribe(
            'conjoon.mail.publicMessage.mailMove',
            this.onEmailItemMove,
            this
        );

        Ext.ux.util.MessageBus.subscribe(
            'com.conjoon.groupware.email.view.onEmailLoad',
            this.onEmailItemLoad,
            this
        );

        this.mon(com.conjoon.groupware.email.Letterman, 'load', this.newEmailsAvailable, this);

        this.on('contextmenu',    this.onContextClick, this);
        this.on('rowcontextmenu', this.onRowContextClick, this);
        this.on('beforedestroy',  this.onBeforeCmpDestroy, this);

        this.mon(com.conjoon.util.Registry, 'register', this.onRegister, this);

        (new com.conjoon.groupware.email.LatestEmailsGridListener).init(
            this, com.conjoon.groupware.email.EmailPreview
        );

        com.conjoon.groupware.email.LatestEmailsPanel.superclass.initEvents.call(this);
    },


// -------- listeners

    queue : null,

    onEmailsDeleted : function(records)
    {
        var st  = this.store;
        var rec;

        var prev   = com.conjoon.groupware.email.EmailPreview;
        var prevM  = prev.getActiveRecord;
        var prevId = null;
        for (var i = 0, max_i = records.length; i < max_i; i++) {

            rec = st.getById(records[i].id);

            if (rec) {
                st.remove(rec);
                prevId = prevM();
                if (prevId && prevId.id == rec.id) {
                    prev.hide(true);
                }
            }
        }

    },

    onBeforeCmpDestroy : function()
    {
        com.conjoon.util.Registry.unregister('com.conjoon.groupware.email.QuickPanel');
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
        if (name != 'com.conjoon.groupware.email.EmailPanel') {
            return;
        }

        this.onPanelRender();
    },

    onPanelRender : function()
    {
        var sub = com.conjoon.util.Registry.get('com.conjoon.groupware.email.EmailPanel');

        if (sub) {
            sub.gridPanel.store.un('update', this.onEmailGridUpdate, this);
            sub.gridPanel.store.on('update', this.onEmailGridUpdate, this);
            sub.on('emailsdeleted', this.onEmailsDeleted, this);
        }
    },

    /**
     * Subscribed to the message with the subject
     * com.conjoon.groupware.email.view.onEmailLoad.
     *
     * @param {String} subject
     * @param {Object} message
     */
    onEmailItemLoad : function(subject, message)
    {
        var emailRecord = message.emailRecord;

        var rec = this.store.getById(emailRecord.id);

        if (rec) {
            this.setItemsAsRead([rec], true);
        }
    },

    /**
     * Subscribed to the message with the subject
     * conjoon.mail.publicMessage.mailMove
     *
     * @ticket CN-719
     *
     * @param {String} subject
     * @param {Object} message
     */
    onEmailItemMove : function(subject, message)
    {
        var me       = this,
            recs     = this.store.getRange(),
            fromPath = message.path.from,
            toPath   = message.path.to,
            ids      = message.ids;

        // discard root entry as returned from the path
        // computed by tree nodes
        if (fromPath.length && fromPath[0] === 'root') {
            fromPath.shift();
        }
        if (toPath.length && toPath[0] === 'root') {
            toPath.shift();
        }

        for (var i = 0, len = recs.length; i < len; i++) {

            if (Ext.Array.indexOf(ids, recs[i].get('id')) !== -1 &&
                Ext.util.JSON.encode(recs[i].get('path')) ===
                Ext.util.JSON.encode(fromPath)) {
                recs[i].set('path', toPath);
            }

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

        if (max_i > 1) {
            throw("unexpected value for max_i: \""+ max_i +"\"");
        }


        if (requestArray.length > 0) {
            Ext.Ajax.request({
                url: './groupware/email.item/set.email.flag/format/json',
                params: {
                    type : 'read',
                    path : Ext.encode(records[0].get('path')),
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
                    text    : com.conjoon.Gettext.gettext("mark item as read"),
                    scope   : this,
                    handler : function(){this.setItemsAsRead(this.selModel.getSelections(), true);}
                  },{
                    text    : com.conjoon.Gettext.gettext("mark item as unread"),
                    scope   : this,
                    handler : function(){this.setItemsAsRead(this.selModel.getSelections(), false);}
                  }]
            });
        }
    }



});
