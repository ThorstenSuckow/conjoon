/*
 * Ext JS Library 3.0 Pre-alpha
 * Copyright(c) 2006-2008, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://extjs.com/license
 */


Ext.data.DataReader=function(meta,recordType){this.meta=meta;this.recordType=Ext.isArray(recordType)?Ext.data.Record.create(recordType):recordType;};Ext.data.DataReader.prototype={realize:function(rs,data){if(Ext.isArray(rs)){for(var i=rs.length-1;i>=0;i--){if(Ext.isArray(data)){this.realize(rs.splice(i,1).shift(),data.splice(i,1).shift());}
else{this.realize(rs.splice(i,1).shift(),data);}}}
else{if(!this.isData(data)){rs.commit();throw new Error("DataReader#realize was called with invalid remote-data.  Please see the docs for DataReader#realize and review your DataReader configuration.");}
var values=this.extractValues(data,rs.fields.items,rs.fields.items.length);rs.phantom=false;rs.id=data[this.meta.idProperty];rs.data=values;rs.commit();}},update:function(rs,data){if(Ext.isArray(rs)){for(var i=rs.length-1;i>=0;i--){if(Ext.isArray(data)){this.update(rs.splice(i,1).shift(),data.splice(i,1).shift());}
else{this.update(rs.splice(i,1).shift(),data);}}}
else{if(!this.isData(data)){rs.commit();throw new Error("DataReader#update received invalid data from server.  Please see docs for DataReader#update");}
rs.data=this.extractValues(data,rs.fields.items,rs.fields.items.length);rs.commit();}},isData:function(data){return(data&&typeof(data)=='object'&&!Ext.isEmpty(data[this.meta.idProperty]))?true:false}};