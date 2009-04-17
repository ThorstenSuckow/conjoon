/*
 * Ext JS Library 3.0 RC1
 * Copyright(c) 2006-2009, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://extjs.com/license
 */


Ext.data.JsonWriter=Ext.extend(Ext.data.DataWriter,{returnJson:true,writeRecord:function(rec){var data=this.toHash(rec);return(this.returnJson===true)?Ext.encode(data):data;},createRecord:function(rec){var data=this.toHash(rec);delete data[this.meta.idProperty];return(this.returnJson===true)?Ext.encode(data):data;},save:function(p,rs){Ext.data.JsonWriter.superclass.save.apply(this,arguments);if(this.returnJson){if(Ext.isArray(rs)){p[this.meta.idProperty]=Ext.encode(p[this.meta.idProperty]);}
p[this.dataProperty]=Ext.encode(p[this.dataProperty]);}},saveRecord:function(rec){return this.toHash(rec);},destroy:function(p,rs){Ext.data.JsonWriter.superclass.destroy.apply(this,arguments);if(this.returnJson){p[this.dataProperty]=Ext.encode(p[this.dataProperty]);}},destroyRecord:function(rec){return rec.id}});