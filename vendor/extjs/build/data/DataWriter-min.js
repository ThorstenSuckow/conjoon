/*
 * Ext JS Library 3.0 Pre-alpha
 * Copyright(c) 2006-2008, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://extjs.com/license
 */


Ext.data.DataWriter=function(config){Ext.apply(this,config);};Ext.data.DataWriter.prototype={meta:{},writeAllFields:false,write:function(action,params,rs){var data=null;switch(action){case Ext.data.Api.CREATE:data=this.create(rs);break;case Ext.data.Api.UPDATE:data=this.update(rs);break;case Ext.data.Api.DESTROY:data=this.destroy(rs);break;}
this.render(action,rs,params,data);},render:Ext.emptyFn,update:function(rs){var params={};if(Ext.isArray(rs)){var data=[];var ids=[];for(var n=0,len=rs.length;n<len;n++){ids.push(rs[n].id);data.push(this.updateRecord(rs[n]));}
params[this.meta.idProperty]=ids;params[this.meta.root]=data;}
else if(rs instanceof Ext.data.Record){params[this.meta.idProperty]=rs.id;params[this.meta.root]=this.updateRecord(rs);}
return params;},updateRecord:Ext.emptyFn,create:function(rs){var params={};if(Ext.isArray(rs)){var data=[];for(var n=0,len=rs.length;n<len;n++){data.push(this.createRecord(rs[n]));}
params[this.meta.root]=data;}
else if(rs instanceof Ext.data.Record){params[this.meta.root]=this.createRecord(rs);}
return params;},createRecord:Ext.emptyFn,destroy:function(rs){var params={};if(Ext.isArray(rs)){var data=[];var ids=[];for(var i=0,len=rs.length;i<len;i++){data.push(this.destroyRecord(rs[i]));}
params[this.meta.root]=data;}else if(rs instanceof Ext.data.Record){params[this.meta.root]=this.destroyRecord(rs);}
return params;},destroyRecord:Ext.emptyFn,toHash:function(rec){var map=rec.fields.map;var data={};var raw=(this.writeAllFields===false&&rec.phantom===false)?rec.getChanges():rec.data;for(var k in raw){data[(map[k].mapping)?map[k].mapping:map[k].name]=raw[k];}
data[this.meta.idProperty]=rec.id;return data;}};