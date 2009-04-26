/*
 * Ext JS Library 3.0 Pre-alpha
 * Copyright(c) 2006-2008, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://extjs.com/license
 */


Ext.data.ScriptTagProxy=function(config){Ext.data.ScriptTagProxy.superclass.constructor.call(this,config);this.head=document.getElementsByTagName("head")[0];};Ext.data.ScriptTagProxy.TRANS_ID=1000;Ext.extend(Ext.data.ScriptTagProxy,Ext.data.DataProxy,{timeout:30000,callbackParam:"callback",nocache:true,doRequest:function(action,rs,params,reader,callback,scope,arg){var p=Ext.urlEncode(Ext.apply(params,this.extraParams));var url=this.url||this.api[action];url+=(url.indexOf("?")!=-1?"&":"?")+p;if(this.nocache){url+="&_dc="+(new Date().getTime());}
var transId=++Ext.data.ScriptTagProxy.TRANS_ID;var trans={id:transId,action:action,cb:"stcCallback"+transId,scriptId:"stcScript"+transId,params:params,arg:arg,url:url,callback:callback,scope:scope,reader:reader};window[trans.cb]=this.createCallback(action,trans);url+=String.format("&{0}={1}",this.callbackParam,trans.cb);if(this.autoAbort!==false){this.abort();}
trans.timeoutId=this.handleFailure.defer(this.timeout,this,[trans]);var script=document.createElement("script");script.setAttribute("src",url);script.setAttribute("type","text/javascript");script.setAttribute("id",trans.scriptId);this.head.appendChild(script);this.trans=trans;},createCallback:function(action,trans){var conn=this;return(action==Ext.data.Api.READ)?function(res){conn.trans=false;conn.destroyTrans(trans,true);var result;try{result=trans.reader.readRecords(res);}catch(e){conn.fireEvent(Ext.data.Api.READ+"exception",conn,res,trans.arg,e);trans.callback.call(trans.scope||window,null,trans.arg,false);return;}
conn.fireEvent(Ext.data.Api.READ,conn,res,trans.arg);trans.callback.call(trans.scope||window,result,trans.arg,true);}:function(res){var reader=trans.reader;if(!res[reader.meta.successProperty]===true){conn.fireEvent("writeexception",action,conn,trans,res);trans.callback.call(trans.scope,null,res,false);return;}
conn.fireEvent("write",action,conn,res[reader.meta.root],res,trans.arg);trans.callback.call(trans.scope||window,res[reader.meta.root],res,true);}},isLoading:function(){return this.trans?true:false;},abort:function(){if(this.isLoading()){this.destroyTrans(this.trans);}},destroyTrans:function(trans,isLoaded){this.head.removeChild(document.getElementById(trans.scriptId));clearTimeout(trans.timeoutId);if(isLoaded){window[trans.cb]=undefined;try{delete window[trans.cb];}catch(e){}}else{window[trans.cb]=function(){window[trans.cb]=undefined;try{delete window[trans.cb];}catch(e){}};}},handleFailure:function(trans){this.trans=false;this.destroyTrans(trans,false);if(trans.action===Ext.data.Api.READ){this.fireEvent(Ext.data.Api.READ+"exception",this,null,trans.arg);}
else{this.fireEvent("writeexception",this,trans.action,null,trans.arg);}
trans.callback.call(trans.scope||window,null,trans.arg,false);}});