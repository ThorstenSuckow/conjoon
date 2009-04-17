/*
 * Ext JS Library 3.0 RC1
 * Copyright(c) 2006-2009, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://extjs.com/license
 */


Ext.data.MemoryProxy=function(data){Ext.data.MemoryProxy.superclass.constructor.call(this);this.data=data;this.api={load:true};};Ext.extend(Ext.data.MemoryProxy,Ext.data.DataProxy,{doRequest:function(action,rs,params,reader,writer,cb,scope,arg){params=params||{};var result;try{result=reader.readRecords(this.data);}catch(e){this.fireEvent("loadexception",this,arg,null,e);cb.call(scope,null,arg,false);return;}
cb.call(scope,result,arg,true);}});