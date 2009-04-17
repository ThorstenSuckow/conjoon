/*
 * Ext JS Library 3.0 RC1
 * Copyright(c) 2006-2009, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://extjs.com/license
 */


Ext.data.DirectProxy=function(config){Ext.apply(this,config);this.api=config.api||{load:undefined,save:undefined,create:undefined,destroy:undefined};if(typeof this.paramOrder=='string'){this.paramOrder=this.paramOrder.split(/[\s,|]/);}
Ext.data.DirectProxy.superclass.constructor.call(this);};Ext.extend(Ext.data.DirectProxy,Ext.data.DataProxy,{paramOrder:undefined,paramsAsHash:true,doRequest:function(action,rs,params,reader,writer,cb,scope,options){var args=[];var directFn=this.api[action];switch(action){case'save':args.push(params[reader.meta.idProperty]);args.push(params[writer.dataProperty]);break;case'destroy':args.push(params[writer.dataProperty]);break;case'create':args.push(params[writer.dataProperty]);break;case'load':args.push(params);break;}
args.push(this.createCallback(action,reader,cb,scope,options));directFn.apply(window,args);},createCallback:function(action,reader,cb,scope,arg){return{callback:(action=='load')?function(result,e){if(!e.status){this.fireEvent(action+"exception",this,e,result);cb.call(scope,null,arg,false);return;}
var records;try{records=reader.readRecords(result);}
catch(ex){this.fireEvent(action+"exception",this,e,result,ex);cb.call(scope,null,arg,false);return;}
this.fireEvent(action,this,e,arg);cb.call(scope,records,arg,true);}:function(result,e){if(!e.status){this.fireEvent(action+"exception",this,e);cb.call(scope,null,e,false);return;}
this.fireEvent(action,this,result,e,arg);cb.call(scope,result,e,true);},scope:this}}});