/*
 * Ext.ux.grid.GridViewMenuPlugin
 * Copyright(c) 2008-2009, Thorsten Suckow-Homberg <ts@siteartwork.de>.
 * 
 * This code is licensed under GNU LESSER GENERAL PUBLIC LICENSE.
 * 
 * This project is released as a sub project of conjoon <http://www.conjoon.org>
 */


Ext.namespace('Ext.ux.grid');Ext.ux.grid.GridViewMenuPlugin=Ext.extend(Object,{_grid:null,_view:null,_menuBtn:null,colMenu:null,cm:null,_lastGroupField:null,_groupMenu:null,_keepItems:null,init:function(grid)
{if(grid.enableHdMenu===false){return;}
this._keepItems=[];this._grid=grid;grid.enableHdMenu=false;this._view=grid.getView();this._view.initElements=this._view.initElements.createSequence(this.initElements,this);this._view.initData=this._view.initData.createSequence(this.initData,this);this._view.destroy=this._view.destroy.createInterceptor(this._destroy,this);this.colMenu=new Ext.menu.Menu({id:grid.id+"-hcols-menu",subMenuAlign:"tr-tl?"});this.colMenu.on("beforeshow",this._beforeColMenuShow,this);this.colMenu.on("beforeremove",this._beforeColMenuRemove,this);this.colMenu.on("itemclick",this._handleHdMenuClick,this);},_beforeColMenuRemove:function(menu,item)
{if(this._keepItems.indexOf(item)!=-1){return false;}},_handleHdMenuClick:function(item,e)
{return this._view.handleHdMenuClick(item,e);},_beforeColMenuShow:function(menu)
{this.colMenu.suspendEvents();for(var i=0,len=this._keepItems.length;i<len;i++){this.colMenu.remove(this._keepItems[i],false);}
this.colMenu.resumeEvents();this._view.beforeColMenuShow.call(this,menu);if(this._view.enableGroupingMenu&&this.colMenu){if(!this._groupMenu){this._groupMenu=new Ext.menu.Menu({id:this._grid.id+"-hgroupcols-menu"});this._groupMenu.on("beforeshow",this._onBeforeGroupMenuShow,this);this._groupMenu.on("itemclick",this._onGroupMenuItemClick,this);var conf={itemId:'showGroups',text:this._view.showGroupsText,menu:this._groupMenu};if(this._view.enableNoGroups){var field=this._view.getGroupField();Ext.apply(conf,{checked:!!field,checkHandler:function(menuItem,checked){if(checked){this._grid.store.groupBy(this._lastGroupField);}else{this._grid.store.clearGrouping();}},scope:this});}
var sep=new Ext.menu.Separator();var gI=this._view.enableNoGroups?new Ext.menu.CheckItem(conf):new Ext.menu.Item(conf);this._keepItems.unshift(sep,gI);}}
for(var i=0,len=this._keepItems.length;i<len;i++){this.colMenu.add(this._keepItems[i]);}
if(this._view.enableNoGroups){this._keepItems[1].setChecked(!!this._view.getGroupField(),true);}},_handleHdDown:function(e,t)
{if(Ext.fly(t).hasClass('x-grid3-hd-btn')){e.stopEvent();this.colMenu.show(t,"tr-br?");}},_onBeforeGroupMenuShow:function()
{var cm=this._view.cm,colCount=cm.getColumnCount(),field=this._view.getGroupField();this._groupMenu.removeAll();for(var i=0;i<colCount;i++){this._groupMenu.add(new Ext.menu.CheckItem({itemId:"groupcol-"+cm.getColumnId(i),text:cm.getColumnHeader(i),checked:cm.getDataIndex(i)==field,hideOnClick:true,disabled:cm.config[i].groupable===false}));}},_onGroupMenuItemClick:function(item)
{var cm=this._view.cm,index=cm.getIndexById(item.itemId.substr(9)),dIndex=cm.getDataIndex(index);if(index!=-1){if(item.checked){this._grid.store.clearGrouping();}else{this._grid.store.groupBy(dIndex);this._lastGroupField=this._view.getGroupField();}}},_getMenuButton:function()
{var a=document.createElement('a');a.className='ext-ux-grid-gridviewmenuplugin-menuBtn x-grid3-hd-btn';a.href='#';return new Ext.Element(a);},initData:function()
{this.cm=this._view.cm;if(this._view.enableGroupingMenu){this._lastGroupField=this._view.getGroupField();}},initElements:function()
{this.menuBtn=this._getMenuButton();this._view.mainHd.dom.appendChild(this.menuBtn.dom);this.menuBtn.on("click",this._handleHdDown,this);this.menuBtn.dom.style.height=(this._view.mainHd.dom.offsetHeight-1)+'px';},_destroy:function()
{if(this.colMenu){this.colMenu.un("beforeremove",this._beforeColMenuRemove,this);this.colMenu.removeAll(true);Ext.menu.MenuMgr.unregister(this.colMenu);this.colMenu.getEl().remove();delete this.colMenu;}
if(this._groupMenu){this._groupMenu.removeAll(true);Ext.menu.MenuMgr.unregister(this._groupMenu);delete this._groupMenu;}
if(this._menuBtn){this._menuBtn.remove();delete this._menuBtn;}}});
