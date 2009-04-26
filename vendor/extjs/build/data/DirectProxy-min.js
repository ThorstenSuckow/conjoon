/*
 * Ext JS Library 3.0 Pre-alpha
 * Copyright(c) 2006-2008, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://extjs.com/license
 */


Ext.data.DirectProxy=function(config){if(typeof this.paramOrder=='string'){this.paramOrder=this.paramOrder.split(/[\s,|]/);}
Ext.data.DirectProxy.superclass.constructor.call(this,config);};Ext.extend(Ext.data.DirectProxy,Ext.data.DataProxy,{paramOrder:undefined,paramsAsHash:true,directFn:undefined,doRequest:function(action,rs,params,reader,callback,scope,options){var args=[];var directFn=this.api[action]||this.directFn;switch(action){case Ext.data.Api.CREATE:args.push(params[reader.meta.root]);break;case Ext.data.Api.READ:if(this.paramOrder){for(var i=0,len=this.paramOrder.length;i<len;i++){args.push(params[this.paramOrder[i]]);}}else if(this.paramsAsHash){args.push(params);}
break;case Ext.data.Api.UPDATE:args.push(params[reader.meta.idProperty]);args.push(params[reader.meta.root]);break;case Ext.data.Api.DESTROY:args.push(params[reader.meta.root]);break;}
args.push(this.createCallback(action,reader,callback,scope,options));directFn.apply(window,args);},createCallback:function(action,reader,callback,scope,arg){return{callback:(action==Ext.data.Api.READ)?function(result,e){if(!e.status){this.fireEvent(action+"exception",this,e,result);callback.call(scope,null,arg,false);return;}
var records;try{records=reader.readRecords(result);}
catch(ex){this.fireEvent("writeexception",this,action,e,result,ex);callback.call(scope,null,arg,false);return;}
this.fireEvent("write",this,action,e,arg);callback.call(scope,records,arg,true);}:function(result,e){if(!e.status){this.fireEvent("writeexception",this,action,e);callback.call(scope,null,e,false);return;}
this.fireEvent("write",this,action,result,e,arg);callback.call(scope,result,e,true);},scope:this}}});