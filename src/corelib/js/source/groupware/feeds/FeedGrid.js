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

Ext.namespace('de.intrabuild.groupware.feeds');

de.intrabuild.groupware.feeds.FeedGrid = function(config) {

	Ext.ux.util.MessageBus.subscribe(
	   'de.intrabuild.groupware.feeds.FeedViewbaton.onFeedLoadSuccess',
	   this.onFeedItemLoaded,
	   this
	);

    Ext.ux.util.MessageBus.subscribe(
       'de.intrabuild.groupware.feeds.FeedPreview.onLoadSuccess',
       this.onFeedItemLoaded,
       this
    );

    Ext.apply(this, config);

    this.store = de.intrabuild.groupware.feeds.FeedStore.getInstance();

    this.store.setDefaultSort('pubDate', "DESC");

    this.columns = [{
        id:'name',
        header: de.intrabuild.Gettext.gettext("Feed"),
        hidden:true,
        width: 120,
        sortable: true,
        dataIndex: 'name'
      },{
        id:'title',
        header: de.intrabuild.Gettext.gettext("Title"),
        width: 220,
        sortable: true,
        dataIndex: 'title'
      },{
        header: de.intrabuild.Gettext.gettext("Description"),
        hidden:true,
        width: 180,
        sortable: true,
        dataIndex: 'description'
      },{
        header: de.intrabuild.Gettext.gettext("Date"),
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
            text: de.intrabuild.Gettext.gettext("group after feeds"),
            checked: true,
            //group: 'de.intrabuild.groupware.FeedGrid.display',
            checkHandler: this.toggleGroupView,
            scope: this
          },
          "-",{
            iconCls : 'de-intrabuild-groupware-feeds-FeedGrid-optionsMenu-configureItem-icon',
            text    : de.intrabuild.Gettext.gettext("Settings..."),
            scope   : this,
            handler : function() {
                var optDialog = new de.intrabuild.groupware.feeds.FeedOptionsDialog();
                optDialog.show();
            }

          }]
    });


    this.tbar = new Ext.Toolbar([{
        cls     : 'x-btn-icon',
        iconCls : 'de-intrabuild-groupware-feeds-FeedGrid-toolbar-addFeedButton-icon',
        handler : function(){
			var dialog = new de.intrabuild.groupware.feeds.AddFeedDialog({
                animateTarget : this.getTopToolbar().items.get(0).el.dom.id
            });
            dialog.show();
        },
        scope : this
      },{
        cls     : 'x-btn-icon',
        iconCls : 'de-intrabuild-groupware-feeds-FeedGrid-toolbar-refreshFeedsButton-icon',
        handler : function(){
            this.clkRow    = null;
            this.clkRecord = null;
            de.intrabuild.groupware.feeds.AccountStore.getInstance().reload();
            this.store.reload();},
        scope: this
      },{
        id      : 'displayOptions',
        cls     : 'x-btn-icon',
        iconCls : 'de-intrabuild-groupware-feeds-FeedGrid-toolbar-displayOptionsButton-icon',
        menu    : displayOptionsMenu
      }
    ]);

    de.intrabuild.groupware.feeds.FeedGrid.superclass.constructor.call(this, {

        loadMask : {msg: de.intrabuild.Gettext.gettext("Loading feeds...")},

        autoScroll:true,
        style:'cursor:default',
        title: de.intrabuild.Gettext.gettext("Feeds"),
        iconCls: 'de-intrabuild-groupware-feeds-Icon',
        border:false,
        hideBorders:true
    });

    // install listeners
    this.on('cellclick',    this.onCellClick, this, {buffer : 200});
    this.on('celldblclick', this.onCellDblClick, this);

    var preview = de.intrabuild.groupware.feeds.FeedPreview;

    this.on('resize',         preview.hide.createDelegate(preview, [true]));
    this.on('beforecollapse', preview.hide.createDelegate(preview, [true, false]));
    this.on('contextmenu',    preview.hide.createDelegate(preview, [false]));

    this.on('contextmenu', this.onContextClick, this);
    this.on('rowcontextmenu', this.onRowContextClick, this);
    displayOptionsMenu.on('beforeshow', this.onBeforeShow, this);

    /**
     * @ext-bug
     * setChecked neeeds to supress the event and compare the groupfield
     * width the field the clicked column represents. This seems buggy in Ext2.0 Alpha
     */
    this.view.beforeMenuShow = function(){
        var field = this.getGroupField();
        this.hmenu.items.get('groupBy').setDisabled(this.cm.config[this.hdCtxIndex].groupable === false);
        this.hmenu.items.get('showGroups').setChecked(field == this.cm.getDataIndex(this.hdCtxIndex), true);
    }
};

Ext.extend(de.intrabuild.groupware.feeds.FeedGrid, Ext.grid.GridPanel, {

    clkRow          : null,
    clkRecord       : null,
    cellClickActive : false,

    /**
     * Checks the group state and checks/unchecks the "show feeds grouped" items
     * as needed.
     */
    onBeforeShow : function()
    {
        if (this.view.getGroupField() != 'name') {
            this.getTopToolbar().items.get('displayOptions').menu.items.get('groupFeeds').setChecked(false, true);
        }
    },

    toggleGroupView : function(item, checked)
    {
        if (!checked) {
            this.store.clearGrouping();
        } else {
            this.store.groupBy('name', true);
        }
    },

    // within this function "this" is actually the GridView
    applyRowClass: function(record, rowIndex, p, ds)
    {
        if (record.data.isRead) {
            return 'de-intrabuild-groupware-feeds-FeedGrid-itemRead';
        } else {
            return 'de-intrabuild-groupware-feeds-FeedGrid-itemUnread';
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

        var requestArray = new Array();

        for (var i = 0; i < l; i++) {
            requestArray.push({
                'id'     : m[i].id,
                'isRead' : m[i].get('isRead')
            });
        }

        Ext.Ajax.request({
            url: '/groupware/feeds/set.item.read/format/json',
            params: {
                json: Ext.encode(requestArray)
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

    onCellClick : function(grid, rowIndex, columnIndex, e)
    {
		if (this.cellClickActive) {
            this.cellClickActive = false;
            return;
        }

        if (e.shiftKey || e.ctrlKey) {
            return;
        }

        this.clkRow    = this.view.getRow(rowIndex);
        this.clkRecord = this.store.getAt(rowIndex);
		de.intrabuild.groupware.feeds.FeedPreview.show(grid, rowIndex, columnIndex, e);
    },

    onCellDblClick : function(grid, rowIndex, columnIndex, eventObject)
    {
		this.cellClickActive = true;
		var feedItem = grid.getStore().getAt(rowIndex);
		de.intrabuild.groupware.feeds.FeedPreview.hide(true, false);
        de.intrabuild.groupware.feeds.FeedViewBaton.showFeed(feedItem, true);
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
                    text: de.intrabuild.Gettext.gettext("Refresh"),
                    scope:this,
                    handler: function(){
                        this.clkRow    = null;
                        this.clkRecord = null;
                        de.intrabuild.groupware.feeds.AccountStore.getInstance().reload();
                        this.store.reload();}
                  },
                 '-',{
                    text: de.intrabuild.Gettext.gettext("mark as read"),
                    scope:this,
                    handler: function(){
                        this.markItemsRead(true);}
                  },{
                    text: de.intrabuild.Gettext.gettext("mark as unread"),
                    scope:this,
                    handler: function(){
                        this.markItemsRead(false);}
                  },
                  '-',{
                    text : de.intrabuild.Gettext.gettext("Settings..."),
                    scope:this,
                    handler: function(){
                        var dialog = new de.intrabuild.groupware.feeds.FeedOptionsDialog();
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