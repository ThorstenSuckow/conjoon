/*
 * Ext JS Library 3.0 Pre-alpha
 * Copyright(c) 2006-2008, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://extjs.com/license
 */


Ext.data.MemoryProxy=function(data){Ext.data.MemoryProxy.superclass.constructor.call(this);this.data=data;this.api={load:true};};Ext.extend(Ext.data.MemoryProxy,Ext.data.DataProxy,{doRequest:function(action,rs,params,reader,writer,callback,scope,arg){params=params||{};var result;try{result=reader.readRecords(this.data);}catch(e){this.fireEvent("loadexception",this,arg,null,e);callback.call(scope,null,arg,false);return;}
callback.call(scope,result,arg,true);}});