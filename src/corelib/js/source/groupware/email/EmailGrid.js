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
    this.store = new Ext.ux.grid.BufferedStore({
		storeId     : Ext.id(),
        bufferSize  : 300,
        autoLoad    : false,
        reader      : new Ext.ux.data.BufferedJsonReader({
                          root            : 'items',
                          totalProperty   : 'totalCount',
                          versionProperty : 'version',
                          id              : 'id'
                      }, 
                      de.intrabuild.groupware.email.EmailItemRecord
                      ),
        sortInfo   : {field: 'date', direction: 'DESC'},
        pruneModifiedRecords : true,
        remoteSort  : true,              
        url         : '/groupware/email/get.email.items/format/json'
    });
    
    this.view = new Ext.ux.grid.BufferedGridView({
        nearLimit   : 100,
        scrollDelay : 0,
        loadMask    : {
            msg : 'Bitte warten...'
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
        view        : this.view,
        displayInfo : true,
        displayMsg  : 'Emails {0} - {1} of {2}',
        emptyMsg    : "Keine Emails vorhanden"
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
        header: "Betreff", 
        width: 415,
        sortable: true, 
        dataIndex: 'subject'
      },{
        header: "Absender", 
        width: 220,
        sortable: true, 
        dataIndex: 'from'
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
        header: "Datum", 
        width: 120, 
        align:'right',
        sortable: true, 
        dataIndex: 'date',
        renderer: Ext.util.Format.dateRenderer('d.m.Y H:i')
      }
    ];
    

    de.intrabuild.groupware.email.EmailGrid.superclass.constructor.call(this, {
        loadMask       : {msg:'Lade Emails...'},
        autoScroll     : true,
        cls            : 'de-intrabuild-groupware-email-EmailGrid',
        trackMouseOver : false,
        enableDragDrop : true,
        ddGroup        : 'de.intrabuild.groupware-email-Email',
        ddText         : "{0} ausgew&aumlhlte Email{1}"
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
            this.menu = new Ext.menu.Menu({
                items: [{
                	text  : 'In neuem Tab &ouml;ffnen',
                    handler : this.controller.openEmailView,
                    scope : this.controller
                  }, {
                    text  : 'Entwurf bearbeiten',
                    handler : function(){this.openEmailEditPanel(true, 'edit');},
                    scope : this.controller
                  }, '-' , {
                    text  : 'Antworten',
                    handler : function(){this.openEmailEditPanel(true, 'reply');},
                    scope : this.controller
                  },{
                    text  : 'Allen antworten',
                    handler : function(){this.openEmailEditPanel(true, 'reply_all');},
                    scope : this.controller
                  },{
                    text  : 'Weiterleiten',
                    handler : function(){this.openEmailEditPanel(true, 'forward');},
                    scope : this.controller
                  }, '-', {
                      text : 'Markieren',
                      menu : {
                          items : [{
                                text    : 'Als gelesen',
                                scope   : this,
                                handler : function(){this.controller.setItemsAsRead(this.selModel.getSelections(), true);}
                              },{
                                text    : 'Als ungelesen',
                                scope   : this,
                                handler : function(){this.controller.setItemsAsRead(this.selModel.getSelections(), false);}
                              },
                              '-',{
                                text    : 'Als Spam',
                                scope   : this,
                                handler : function(){this.controller.setItemsAsSpam(this.selModel.getSelections(), true);}
                              },{
                                text    : 'Als "kein Spam"',
                                scope   : this,
                                handler : function(){this.controller.setItemsAsSpam(this.selModel.getSelections(), false);}
                          }]
                      }
                  },
                  '-',{
                    text    : 'L&ouml;schen',
                    scope   : this,
                    handler : function(){this.controller.deleteEmails(this.selModel.getSelections());}
                }]
            });
        }        
    }
    
});