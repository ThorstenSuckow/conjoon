/*
 * Ext JS Library 3.0 Pre-alpha
 * Copyright(c) 2006-2008, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://extjs.com/license
 */


Ext.data.HttpProxy=function(conn){Ext.data.HttpProxy.superclass.constructor.call(this,conn);this.conn=conn;this.conn.url=null;this.useAjax=!conn||!conn.events;this.activeRequest={};var verbs=Ext.data.Api.getVerbs();for(var n=0,len=verbs.length;n<len;n++){this.activeRequest[verbs[n]]=undefined;}};Ext.extend(Ext.data.HttpProxy,Ext.data.DataProxy,{prettyUrls:false,getConnection:function(){return this.useAjax?Ext.Ajax:this.conn;},setUrl:function(url,makePermanent){this.conn.url=url;if(makePermanent===true){this.url=url;}},buildUrl:function(action,record){record=record||null;var url=(this.api[action])?this.api[action]:this.url;if(typeof(url)=='undefined'){throw new Error('HttpProxy tried to build an url for the action "'+action+'" but could not find an api definition for this action or an url to fall-back to.  Please review your proxy configuration.');}
if(this.prettyUrls===true&&record instanceof Ext.data.Record&&!record.phantom){url+='/'+record.id;}
return url;},doRequest:function(action,rs,params,reader,cb,scope,arg){var o={params:params||{},request:{callback:cb,scope:scope,arg:arg},reader:reader,callback:this.createCallback(action),scope:this};if(this.useAjax){if(this.conn.url===null){this.conn.url=this.buildUrl(action,rs);}
else if(this.prettyUrls===true&&rs instanceof Ext.data.Record&&!rs.phantom){this.conn.url+='/'+rs.id;}
Ext.applyIf(o,this.conn);if(this.activeRequest[action]){Ext.Ajax.abort(this.activeRequest[action]);}
this.activeRequest[action]=Ext.Ajax.request(o);this.conn.url=null;}else{this.conn.request(o);}},createCallback:function(action){return(action==Ext.data.Api.READ)?function(o,success,response){this.activeRequest[action]=undefined;if(!success){this.fireEvent(action+"exception",this,o,response);o.request.callback.call(o.request.scope,null,o.request.arg,false);return;}
var result;try{result=o.reader.read(response);}catch(e){this.fireEvent(action+"exception",this,o,response,e);o.request.callback.call(o.request.scope,null,o.request.arg,false);return;}
this.fireEvent(action,this,o,o.request.arg);o.request.callback.call(o.request.scope,result,o.request.arg,true);}:function(o,success,response){this.activeRequest[action]=undefined;var reader=o.reader;var res=reader.readResponse(response);if(!res[reader.meta.successProperty]===true){this.fireEvent("writeexception",this,action,o,res);o.request.callback.call(o.request.scope,null,res,false);return;}
this.fireEvent("write",this,action,res[reader.meta.root],res,o.request.arg);o.request.callback.call(o.request.scope,res[reader.meta.root],res,true);}}});