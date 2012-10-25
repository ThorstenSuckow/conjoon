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

Ext.namespace('com.conjoon.groupware.feeds');

com.conjoon.groupware.feeds.FeedGrid = Ext.extend(Ext.grid.GridPanel, {

    clkRow          : null,
    clkRecord       : null,
    cellClickActive : false,

    initComponent : function()
    {
        this.store = com.conjoon.groupware.feeds.FeedStore.getInstance();

        this.store.setDefaultSort('pubDate', "DESC");

        this.columns = [{
            id:'name',
            header: com.conjoon.Gettext.gettext("Feed"),
            hidden:true,
            width: 120,
            sortable: true,
            dataIndex: 'name'
          },{
            id:'title',
            header: com.conjoon.Gettext.gettext("Title"),
            width: 220,
            sortable: true,
            dataIndex: 'title',
            renderer : function(value, metadata, record){
                metadata.attr = 'qtip="'+value.replace(/"/g, '&quot;')+'"';
                return value;
            }
          },{
            header: com.conjoon.Gettext.gettext("Description"),
            hidden:true,
            width: 180,
            sortable: true,
            dataIndex: 'description'
          },{
            header: com.conjoon.Gettext.gettext("Date"),
            width: 120,
            hidden:true,
            sortable: true,
            renderer: Ext.util.Format.dateRenderer('d.m.Y H:i'),
            dataIndex: 'pubDate'
          }
        ];

        var groupTextTpl  = '{text} ({[values.rs.length]}/{[function(){var b = 0;for (var i = 0, rs = values.rs, max_i = rs.length; i < max_i; i++) {if (!rs[i].data.isRead) {b++;}}return b;}()]})';
        this.groupTextTpl = new Ext.XTemplate(groupTextTpl);
        this.groupTextTpl.compile();

        this.view = new Ext.grid.GroupingView({
            forceFit      : false,
            showGroupName : false,
            groupTextTpl  : groupTextTpl,
            getRowClass   : this.applyRowClass
        });

        var displayOptionsMenu = new Ext.menu.Menu({
            items: [{
                id : 'groupFeeds',
                text: com.conjoon.Gettext.gettext("group after feeds"),
                checked: true,
                //group: 'com.conjoon.groupware.FeedGrid.display',
                checkHandler: this.toggleGroupView,
                scope: this
              },
              "-",{
                iconCls : 'com-conjoon-groupware-feeds-FeedGrid-optionsMenu-configureItem-icon',
                text    : com.conjoon.Gettext.gettext("Settings..."),
                scope   : this,
                handler : function() {
                    var optDialog = new com.conjoon.groupware.feeds.FeedOptionsDialog();
                    optDialog.show();
                }

              }]
        });


        this.tbar = new Ext.Toolbar([{
            iconCls : 'com-conjoon-groupware-feeds-FeedGrid-toolbar-addFeedButton-icon',
            handler : function(){
                var dialog = new com.conjoon.groupware.feeds.AddFeedDialog({
                    animateTarget : this.getTopToolbar().items.get(0).el.dom.id
                });
                dialog.show();
            },
            scope : this
          },{
            iconCls : 'com-conjoon-groupware-feeds-FeedGrid-toolbar-refreshFeedsButton-icon',
            handler : function(){
                this.clkRow    = null;
                this.clkRecord = null;
                com.conjoon.groupware.feeds.AccountStore.getInstance().reload();
                this.store.reload();},
            scope: this
          },{
            iconCls : 'com-conjoon-groupware-feeds-FeedGrid-toolbar-configureItem-icon',
            handler : function() {
                var optDialog = new com.conjoon.groupware.feeds.FeedOptionsDialog();
                optDialog.show();
            }
          }
        ]);


        Ext.apply(this, {
            loadMask    : {msg: com.conjoon.Gettext.gettext("Loading feeds...")},
            autoScroll  : true,
            style       : 'cursor:default',
            title       : com.conjoon.Gettext.gettext("Feeds"),
            iconCls     : 'com-conjoon-groupware-feeds-Icon',
            hideBorders : true,
            plugins     : [
                new Ext.ux.grid.GridViewMenuPlugin()
            ]
        });

        com.conjoon.groupware.feeds.FeedGrid.superclass.initComponent.call(this);
    },

    initEvents : function()
    {
        Ext.ux.util.MessageBus.subscribe(
           'com.conjoon.groupware.feeds.FeedViewBaton.onFeedLoadSuccess',
           this.onFeedItemLoaded,
           this
        );

        Ext.ux.util.MessageBus.subscribe(
           'com.conjoon.groupware.feeds.FeedPreview.onLoadSuccess',
           this.onFeedItemLoaded,
           this
        );

        (new com.conjoon.groupware.feeds.FeedGridPreviewListener).init(
            this, com.conjoon.groupware.feeds.FeedPreview
        );

        this.on('contextmenu', this.onContextClick, this);
        this.on('rowcontextmenu', this.onRowContextClick, this);

        com.conjoon.groupware.feeds.FeedGrid.superclass.initEvents.call(this);
    },


    // within this function "this" is actually the GridView
    applyRowClass: function(record, rowIndex, p, ds)
    {
        if (record.data.isRead) {
            return 'com-conjoon-groupware-feeds-FeedGrid-itemRead';
        } else {
            return 'com-conjoon-groupware-feeds-FeedGrid-itemUnread';
        }
    },

    // private
    commitRecords : function()
    {
        var m = this.store.getModifiedRecords();

        var l = m.length;

        if (l == 0) {
            return;
        }

        var requestArray = {
            read   : [],
            unread : []
        };

        for (var i = 0; i < l; i++) {
            if (m[i].get('isRead')) {
                requestArray['read'].push(m[i].id);
            } else {
                requestArray['unread'].push(m[i].id);
            }
        }

        Ext.Ajax.request({
            url: './groupware/feeds.item/set.item.read/format/json',
            params: {
                read   : Ext.encode(requestArray['read']),
                unread : Ext.encode(requestArray['unread'])
            }
        });

        this.store.commitChanges();
        this._updateGroupTemplates();
    },

    onFeedItemLoaded : function(subject, message)
    {
        if (message.id) {
            this.markItemsRead(true, message.id)
        }
    },


    _updateGroupTemplates : function()
    {
        if (!this.store.groupField) {
            return;
        }

        var view = this.view;
        var rs   = this.store.getRange();
        var len  = rs.length;
        if (len < 1) {
            return [];
        }

        var groupField = view.getGroupField();

        var r;
        var gvalue;
        var indexes = {};
        var groups = [];

        for (var i = 0; i < len; i++) {
            r      = rs[i];
            gvalue = r.data[groupField];

            if (!indexes[gvalue]) {
                indexes[gvalue] = [];
            }

            indexes[gvalue].push(r);
        }

        var a  = 0;
        var gr = view.getGroups();
        for (var i in indexes) {
            gr[a++].firstChild.firstChild.innerHTML = this.groupTextTpl.apply({
                text : i,
                rs : indexes[i]
            });
        }
    },

    markItemsRead : function(bRead, id)
    {
        var feedIds = new Array();

        if (!id) {
            var selection = this.selModel.getSelections();
            for (var i = 0; i < selection.length; i++) {
                selection[i].set('isRead', bRead);
            }
        } else {
            var rec = this.store.getById(id);
            if (rec) {
                rec.set('isRead', bRead);
            }
        }

        this.commitRecords();
    },

    // private
    /**
     *
     * @access private
     */
    createContextMenu : function()
    {
        if(!this.menu){ // create context menu on first right click
            this.menu = new Ext.menu.Menu({
                items: [{
                    text: com.conjoon.Gettext.gettext("Refresh"),
                    scope:this,
                    handler: function(){
                        this.clkRow    = null;
                        this.clkRecord = null;
                        com.conjoon.groupware.feeds.AccountStore.getInstance().reload();
                        this.store.reload();}
                  },
                 '-',{
                    text: com.conjoon.Gettext.gettext("mark as read"),
                    scope:this,
                    handler: function(){
                        this.markItemsRead(true);}
                  },{
                    text: com.conjoon.Gettext.gettext("mark as unread"),
                    scope:this,
                    handler: function(){
                        this.markItemsRead(false);}
                  },
                  '-',{
                    text : com.conjoon.Gettext.gettext("Settings..."),
                    scope:this,
                    handler: function(){
                        var dialog = new com.conjoon.groupware.feeds.FeedOptionsDialog();
                        dialog.show();
                    }
                }]
            });
            this.menu.on('hide', this.onContextHide, this);
        }
    },

    onContextClick : function(e)
    {
        e.stopEvent();

        // row was clicked, we wont handle this
        if (e.getTarget().id == "") {
            return;
        }

        this.createContextMenu();

        this.selModel.clearSelections();

        this.clkRow    = null;
        this.clkRecord = null;

        this.menu.items.get(2).setDisabled(true);
        this.menu.items.get(3).setDisabled(true);

        this.menu.showAt(e.getXY());
    },

    onRowContextClick : function(grid, index, e)
    {
        var selModel = this.selModel;

        this.createContextMenu();

        e.stopEvent();
        if(this.ctxRow){
            //Ext.fly(this.ctxRow).removeClass('x-node-ctx');
            this.ctxRow = null;
        }

        this.ctxRow    = this.view.getRow(index);
        this.ctxRecord = this.store.getAt(index);

        if (!selModel.isSelected(index)) {
            selModel.selectRow(index, false);
        }

        if (selModel.getCount() == 1) {
            this.menu.items.get(2).setDisabled((this.ctxRecord.data.isRead == true));
            this.menu.items.get(3).setDisabled(!(this.ctxRecord.data.isRead == true));
        } else {
            this.menu.items.get(2).setDisabled(false);
            this.menu.items.get(3).setDisabled(false);
        }

        //Ext.fly(this.ctxRow).addClass('x-node-ctx');
        this.menu.showAt(e.getXY());
    },

    onContextHide : function()
    {
        if(this.ctxRow){
           // Ext.fly(this.ctxRow).removeClass('x-node-ctx');
            this.ctxRow = null;
        }
    }

});