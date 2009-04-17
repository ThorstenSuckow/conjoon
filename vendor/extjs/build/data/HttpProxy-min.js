/*
 * Ext JS Library 3.0 RC1
 * Copyright(c) 2006-2009, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://extjs.com/license
 */


Ext.data.HttpProxy=function(conn){Ext.data.HttpProxy.superclass.constructor.call(this);this.conn=conn;this.api=conn.api||{load:undefined,save:undefined,create:undefined,destroy:undefined};Ext.copyTo(this,conn,'url,prettyUrls');this.useAjax=!conn||!conn.events;};Ext.extend(Ext.data.HttpProxy,Ext.data.DataProxy,{prettyUrls:false,getConnection:function(){return this.useAjax?Ext.Ajax:this.conn;},doRequest:function(action,rs,params,reader,writer,cb,scope,arg){var o={params:params||{},request:{callback:cb,scope:scope,arg:arg},reader:reader,callback:this.createCallback(action),scope:this};if(this.useAjax){this.conn.url=this.buildUrl(action,rs);Ext.applyIf(o,this.conn);this.activeRequest=Ext.Ajax.request(o);}else{this.conn.request(o);}},buildUrl:function(action,record){record=record||null;var url=(this.api[action])?this.api[action]:this.url;if(this.prettyUrls===true&&record instanceof Ext.data.Record&&!record.phantom){url+='/'+record.id}
return url;},createCallback:function(action){return(action=='load')?function(o,success,response){if(!success){this.fireEvent("loadexception",this,o,response);o.request.callback.call(o.request.scope,null,o.request.arg,false);return;}
var result;try{result=o.reader.read(response);}catch(e){this.fireEvent("loadexception",this,o,response,e);o.request.callback.call(o.request.scope,null,o.request.arg,false);return;}
this.fireEvent("load",this,o,o.request.arg);o.request.callback.call(o.request.scope,result,o.request.arg,true);}:function(o,success,response){var reader=o.reader;var res=reader.readResponse(response);if(!res[reader.meta.successProperty]===true){this.fireEvent(action+"exception",this,o,res);o.request.callback.call(o.request.scope,null,res,false);return;}
this.fireEvent(action,this,res[reader.meta.root],res,o.request.arg);o.request.callback.call(o.request.scope,res[reader.meta.root],res,true);}}});