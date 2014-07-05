/**
 * conjoon
 * (c) 2007-2014 conjoon.org
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

Ext.namespace('com.conjoon.groupware.feeds');

com.conjoon.groupware.feeds.FeedGrid = Ext.extend(Ext.grid.GridPanel, {

    clkRow          : null,
    clkRecord       : null,
    cellClickActive : false,

    /**
     * The state of the groups in the view, gets set once state gets applied from
     * statemanager
     * @type {Object}
     * @ticket CN-789
     */
    groupstate : null,

    initComponent : function()
    {
        /**
         * @ticket CN-789
         */
        this.addEvents('grouptoggle');
        this.stateEvents = [
            'collapse', 'expand', 'show', 'hide',
            'resize', 'columnmove', 'columnresize',
            'sortchange', 'groupchange', 'grouptoggle'
        ];

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
                metadata.attr = 'ext:qtip="'+value.replace(/"/g, '&quot;')+'"';
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

        var groupTextTpl  = '{text} ({[values.rs.length]}/{[function(){' +
                            'var b = 0;for (var i = 0, rs = values.rs, ' +
                            'max_i = rs.length; i < max_i; i++) {' +
                            'if (!rs[i].data.isRead) {b++;}}return b;}()]})';

        this.groupTextTpl = new Ext.XTemplate(groupTextTpl);
        this.groupTextTpl.compile();

        this.view = new Ext.grid.GroupingView({
            forceFit      : false,
            showGroupName : false,
            groupTextTpl  : groupTextTpl,
            getRowClass   : this.applyRowClass,
            /**
             * @ticket CN-789
             */
            toggleGroup : function(group, expanded){
                Ext.grid.GroupingView.prototype.toggleGroup.call(this, group, expanded);
                this.grid.fireEvent('grouptoggle', this.grid, group, expanded);
            },
            /**
             * We need this override of the private method to make sure
             * we do not accidently look up elements and add css-classes
             * during expnding/collapsing when re-applying the state.
             *
             * @ticket CN-789
             * @param field
             *
             * @return {String}
             */
            getPrefix: function(field){
                return 'cn-feedGrid-gp-' + field + '-';
            },
            /**
             * Applies state (groupstate) and groups once grid was rendered.
             * grid's afterrender event does not work, since the event is fired before
             * the view rendering methods are invoked when view's deferRowRender is
             * enabled
             *
             * @ticket CN-789
             *
             * @private
             */
            afterRender : function() {

                Ext.grid.GroupingView.prototype.afterRender.call(this);

                var me = this,
                    grid = me.grid,
                    groupstate = grid.groupstate,
                    el;

                if (!groupstate || !me.state) {
                    return;
                }

                for (var i in groupstate) {
                    // view dows not seem to check for existing
                    // element in toggleGroup, so do it here
                    var el = Ext.get(i);
                    if (!el) {
                        continue;
                    }

                    me.toggleGroup(i, groupstate[i]);
                }
            }
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

    /**
     * @inheritdoc
     * @ticket CN-789
     */
    getState : function() {
        var orgState = com.conjoon.groupware.feeds.FeedGrid.superclass.getState.call(this),
            state = Ext.apply({
                collapsed  : this.collapsed,
                hidden     : !this.isVisible(),
                groupstate : this.view.state
            }, orgState);

        if (!this.collapsed && this.resizable !== false) {
            state.height = this.getHeight();
        }

        return state;
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
