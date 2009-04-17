/*
 * Ext JS Library 3.0 RC1
 * Copyright(c) 2006-2009, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://extjs.com/license
 */


Ext.data.DataWriter=function(config){Ext.apply(this,config);};Ext.data.DataWriter.prototype={meta:{},dataProperty:'data',writeAllFields:false,save:function(p,rs){if(Ext.isArray(rs)){var data=[];var ids=[];for(var n=0,len=rs.length;n<len;n++){ids.push(rs[n].id);data.push(this.saveRecord(rs[n]));}
p[this.meta.idProperty]=ids;p[this.dataProperty]=data;}
else if(rs instanceof Ext.data.Record){p[this.meta.idProperty]=rs.id;p[this.dataProperty]=this.saveRecord(rs);}
return false;},saveRecord:Ext.emptyFn,create:function(p,rec){return p[this.dataProperty]=this.createRecord(rec);},createRecord:Ext.emptyFn,destroy:function(p,rs){if(Ext.isArray(rs)){var data=[];var ids=[];for(var i=0,len=rs.length;i<len;i++){data.push(this.destroyRecord(rs[i]));}
p[this.dataProperty]=data;}else if(rs instanceof Ext.data.Record){p[this.dataProperty]=this.destroyRecord(rs);}
return false;},destroyRecord:Ext.emptyFn,toHash:function(rec){var map=rec.fields.map;var data={};var raw=(this.writeAllFields===false&&rec.phantom===false)?rec.getChanges():rec.data;for(var k in raw){data[(map[k].mapping)?map[k].mapping:map[k].name]=raw[k];}
data[this.meta.idProperty]=rec.id;return data;}};