/*
 * Ext JS Library 3.0 RC1
 * Copyright(c) 2006-2009, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://extjs.com/license
 */


Ext.data.Store=function(config){this.data=new Ext.util.MixedCollection(false);this.data.getKey=function(o){return o.id;};this.baseParams={};this.paramNames={"start":"start","limit":"limit","sort":"sort","dir":"dir"};if(config&&config.data){this.inlineData=config.data;delete config.data;}
Ext.apply(this,config);if(this.url&&!this.proxy){this.proxy=new Ext.data.HttpProxy({url:this.url});}
if(this.reader){if(!this.recordType){this.recordType=this.reader.recordType;}
if(this.reader.onMetaChange){this.reader.onMetaChange=this.onMetaChange.createDelegate(this);}
if(this.writer){this.writer.meta=this.reader.meta;this.pruneModifiedRecords=true;}}
if(this.recordType){this.fields=this.recordType.prototype.fields;}
this.modified=[];this.addEvents('datachanged','metachange','add','remove','update','clear','beforeload','load','loadexception','beforesave','save','saveexception','beforedestroy','destroy','destroyexception','beforecreate','create','createexception','event');if(this.proxy){this.relayEvents(this.proxy,["loadexception"]);}
if(this.writer){this.relayEvents(this.proxy,["saveexception","createexception","destroyexception"]);this.on('add',this.createRecords.createDelegate(this));this.on('remove',this.destroyRecord.createDelegate(this));this.on('update',this.updateRecord.createDelegate(this));}
this.sortToggle={};if(this.sortField){this.setDefaultSort(this.sortField,this.sortDir);}else if(this.sortInfo){this.setDefaultSort(this.sortInfo.field,this.sortInfo.direction);}
Ext.data.Store.superclass.constructor.call(this);if(this.id){this.storeId=this.id;delete this.id;}
if(this.storeId){Ext.StoreMgr.register(this);}
if(this.inlineData){this.loadData(this.inlineData);delete this.inlineData;}else if(this.autoLoad){this.load.defer(10,this,[typeof this.autoLoad=='object'?this.autoLoad:undefined]);}};Ext.extend(Ext.data.Store,Ext.util.Observable,{writer:undefined,remoteSort:false,autoDestroy:false,pruneModifiedRecords:false,lastOptions:null,batchSave:false,removed:[],destroy:function(){if(this.storeId){Ext.StoreMgr.unregister(this);}
this.data=null;this.purgeListeners();},add:function(records){records=[].concat(records);if(records.length<1){return;}
for(var i=0,len=records.length;i<len;i++){records[i].join(this);}
var index=this.data.length;this.data.addAll(records);if(this.snapshot){this.snapshot.addAll(records);}
this.fireEvent("add",this,records,index);},addSorted:function(record){var index=this.findInsertIndex(record);this.insert(index,record);},remove:function(record){var index=this.data.indexOf(record);this.data.removeAt(index);if(this.pruneModifiedRecords){this.modified.remove(record);}
if(this.snapshot){this.snapshot.remove(record);}
this.fireEvent("remove",this,record,index);},removeAt:function(index){this.remove(this.getAt(index));},removeAll:function(){this.data.clear();if(this.snapshot){this.snapshot.clear();}
if(this.pruneModifiedRecords){this.modified=[];}
this.fireEvent("clear",this);},insert:function(index,records){records=[].concat(records);for(var i=0,len=records.length;i<len;i++){this.data.insert(index,records[i]);records[i].join(this);}
this.fireEvent("add",this,records,index);},indexOf:function(record){return this.data.indexOf(record);},indexOfId:function(id){return this.data.indexOfKey(id);},getById:function(id){return this.data.key(id);},getAt:function(index){return this.data.itemAt(index);},getRange:function(start,end){return this.data.getRange(start,end);},storeOptions:function(o){o=Ext.apply({},o);delete o.callback;delete o.scope;this.lastOptions=o;},load:function(options){options=options||{};this.storeOptions(options);if(this.sortInfo&&this.remoteSort){var pn=this.paramNames;options.params=options.params||{};options.params[pn["sort"]]=this.sortInfo.field;options.params[pn["dir"]]=this.sortInfo.direction;}
return this.execute('load',null,options);},updateRecord:function(store,record,action){if(action!=Ext.data.Record.EDIT||this.batchSave){return;}
if(!record.phantom||(record.phantom&&record.isValid)){this.save(record);}},createRecords:function(store,rs,index){if(this.batchSave==false){for(var i=0,len=rs.length;i<len;i++){if(rs[i].phantom&&rs[i].isValid()){rs[i].markDirty();this.execute('create',rs[i]);}}}
else{for(var i=0,len=rs.length;i<len;i++){if(rs[i].phantom&&rs[i].isValid()){rs[i].markDirty();this.modified.push(rs[i]);}}}},destroyRecord:function(store,record,index){if(this.modified.indexOf(record)!=-1){this.modified.remove(record);}
if(record.phantom===true){return;}
record.lastIndex=index;if(!this.batchSave){this.execute('destroy',record);}
else{this.removed.push(record);}},execute:function(action,rs,options){if(action!='load'&&!this.writer){throw new Error('Store attempted to execute the remote action "'+action+'" without a DataWriter installed.');}
options=options||{};if(this.fireEvent('before'+action,this,rs||options)){var p=Ext.apply(options.params||{},this.baseParams,{xaction:action});if(this.writer&&typeof(this.writer[action])=='function'){this.writer[action](p,rs);}
this.proxy.request(action,rs,p,this.reader,this.writer,this.createCallback(action,rs),this,options);return true;}
else{return false;}},save:function(rs){rs=rs||this.getModifiedRecords();if(!rs.length&&!rs instanceof Ext.data.Record&&!this.removed.length){return false;}
var action='save';if(this.removed.length){try{this.execute('destroy',this.removed);}
catch(e){throw e;}}
try{if(Ext.isArray(rs)){for(var i=rs.length-1;i>=0;i--){if(rs[i].phantom===true){var rec=rs.splice(i,1).shift();if(rec.isValid()){this.execute('create',rec);}}}}
else if(rs.phantom){if(!rs.isValid()){return false;}
action='create';}
if(Ext.isArray(rs)&&rs.length==1){rs=rs[0];}
if(rs instanceof Ext.data.Record||rs.length>0){this.execute(action,rs);return true;}
else{return true;}}
catch(e){throw e;}
return true;},createCallback:function(action,rs){return(action=='load')?this.loadRecords:function(data,response,success){if(success===true){switch(action){case'create':this.onCreateRecord(rs,data);break;case'destroy':this.onDestroyRecords(rs,data);break;case'save':this.onSaveRecords(rs,data);break;}
this.fireEvent(action,this,data,response);}
else{switch(action){case'destroy':if(rs instanceof Ext.data.Record){rs=[rs];}
for(var i=0,len=rs.length;i<len;i++){this.insert(rs[i].lastIndex,rs[i]);}
this.removed=[];break;}}
this.fireEvent('event',this,data,response);}},onCreateRecord:function(record,data){if(record.phantom&&data[this.reader.meta.idProperty]){record.realize(data,data[this.reader.meta.idProperty]);}},onSaveRecords:function(rs,data){if(!Ext.isArray(rs)){rs=[rs];}
for(var i=rs.length-1;i>=0;i--){rs[i].commit();}},onDestroyRecords:function(rs,data){this.removed=[];},reload:function(options){this.load(Ext.applyIf(options||{},this.lastOptions));},loadRecords:function(o,options,success){if(!o||success===false){if(success!==false){this.fireEvent("load",this,[],options);}
if(options.callback){options.callback.call(options.scope||this,[],options,false);}
return;}
var r=o.records,t=o.totalRecords||r.length;if(!options||options.add!==true){if(this.pruneModifiedRecords){this.modified=[];}
for(var i=0,len=r.length;i<len;i++){r[i].join(this);}
if(this.snapshot){this.data=this.snapshot;delete this.snapshot;}
this.data.clear();this.data.addAll(r);this.totalLength=t;this.applySort();this.fireEvent("datachanged",this);}else{this.totalLength=Math.max(t,this.data.length+r.length);this.add(r);}
this.fireEvent("load",this,r,options);if(options.callback){options.callback.call(options.scope||this,r,options,true);}},loadData:function(o,append){var r=this.reader.readRecords(o);this.loadRecords(r,{add:append},true);},getCount:function(){return this.data.length||0;},getTotalCount:function(){return this.totalLength||0;},getSortState:function(){return this.sortInfo;},applySort:function(){if(this.sortInfo&&!this.remoteSort){var s=this.sortInfo,f=s.field;this.sortData(f,s.direction);}},sortData:function(f,direction){direction=direction||'ASC';var st=this.fields.get(f).sortType;var fn=function(r1,r2){var v1=st(r1.data[f]),v2=st(r2.data[f]);return v1>v2?1:(v1<v2?-1:0);};this.data.sort(direction,fn);if(this.snapshot&&this.snapshot!=this.data){this.snapshot.sort(direction,fn);}},setDefaultSort:function(field,dir){dir=dir?dir.toUpperCase():"ASC";this.sortInfo={field:field,direction:dir};this.sortToggle[field]=dir;},sort:function(fieldName,dir){var f=this.fields.get(fieldName);if(!f){return false;}
if(!dir){if(this.sortInfo&&this.sortInfo.field==f.name){dir=(this.sortToggle[f.name]||"ASC").toggle("ASC","DESC");}else{dir=f.sortDir;}}
var st=(this.sortToggle)?this.sortToggle[f.name]:null;var si=(this.sortInfo)?this.sortInfo:null;this.sortToggle[f.name]=dir;this.sortInfo={field:f.name,direction:dir};if(!this.remoteSort){this.applySort();this.fireEvent("datachanged",this);}else{if(!this.load(this.lastOptions)){if(st){this.sortToggle[f.name]=st;}
if(si){this.sortInfo=si;}}}},each:function(fn,scope){this.data.each(fn,scope);},getModifiedRecords:function(){return this.modified;},createFilterFn:function(property,value,anyMatch,caseSensitive){if(Ext.isEmpty(value,false)){return false;}
value=this.data.createValueMatcher(value,anyMatch,caseSensitive);return function(r){return value.test(r.data[property]);};},sum:function(property,start,end){var rs=this.data.items,v=0;start=start||0;end=(end||end===0)?end:rs.length-1;for(var i=start;i<=end;i++){v+=(rs[i].data[property]||0);}
return v;},filter:function(property,value,anyMatch,caseSensitive){var fn=this.createFilterFn(property,value,anyMatch,caseSensitive);return fn?this.filterBy(fn):this.clearFilter();},filterBy:function(fn,scope){this.snapshot=this.snapshot||this.data;this.data=this.queryBy(fn,scope||this);this.fireEvent("datachanged",this);},query:function(property,value,anyMatch,caseSensitive){var fn=this.createFilterFn(property,value,anyMatch,caseSensitive);return fn?this.queryBy(fn):this.data.clone();},queryBy:function(fn,scope){var data=this.snapshot||this.data;return data.filterBy(fn,scope||this);},find:function(property,value,start,anyMatch,caseSensitive){var fn=this.createFilterFn(property,value,anyMatch,caseSensitive);return fn?this.data.findIndexBy(fn,null,start):-1;},findBy:function(fn,scope,start){return this.data.findIndexBy(fn,scope,start);},collect:function(dataIndex,allowNull,bypassFilter){var d=(bypassFilter===true&&this.snapshot)?this.snapshot.items:this.data.items;var v,sv,r=[],l={};for(var i=0,len=d.length;i<len;i++){v=d[i].data[dataIndex];sv=String(v);if((allowNull||!Ext.isEmpty(v))&&!l[sv]){l[sv]=true;r[r.length]=v;}}
return r;},clearFilter:function(suppressEvent){if(this.isFiltered()){this.data=this.snapshot;delete this.snapshot;if(suppressEvent!==true){this.fireEvent("datachanged",this);}}},isFiltered:function(){return this.snapshot&&this.snapshot!=this.data;},afterEdit:function(record){if(this.modified.indexOf(record)==-1){this.modified.push(record);}
this.fireEvent("update",this,record,Ext.data.Record.EDIT);},afterReject:function(record){this.modified.remove(record);this.fireEvent("update",this,record,Ext.data.Record.REJECT);},afterCommit:function(record){this.modified.remove(record);this.fireEvent("update",this,record,Ext.data.Record.COMMIT);},commitChanges:function(){var m=this.modified.slice(0);this.modified=[];for(var i=0,len=m.length;i<len;i++){m[i].commit();}},rejectChanges:function(){var m=this.modified.slice(0);this.modified=[];for(var i=0,len=m.length;i<len;i++){m[i].reject();}},onMetaChange:function(meta,rtype,o){this.recordType=rtype;this.fields=rtype.prototype.fields;delete this.snapshot;this.sortInfo=meta.sortInfo;this.modified=[];this.fireEvent('metachange',this,this.reader.meta);},findInsertIndex:function(record){this.suspendEvents();var data=this.data.clone();this.data.add(record);this.applySort();var index=this.data.indexOf(record);this.data=data;this.resumeEvents();return index;},setBaseParam:function(name,value){this.baseParams=this.baseParams||{};this.baseParams[name]=value;}});Ext.reg('store',Ext.data.Store);