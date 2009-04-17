/*
 * Ext JS Library 3.0 RC1
 * Copyright(c) 2006-2009, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://extjs.com/license
 */


Ext.data.DataProxy=function(){this.addEvents('beforeload','load','beforesave','save','beforedestroy','destroy','beforecreate','create');Ext.data.DataProxy.superclass.constructor.call(this);};Ext.extend(Ext.data.DataProxy,Ext.util.Observable,{request:function(action,rs,params,reader,writer,cb,scope,options){if(!this.api[action]){if(this.url){this.api[action]=this.url;}
else if(typeof(this[action])!='function'){throw new Error('No proxy url defined for api action "'+action+'"');}}
if(this.fireEvent("before"+action,this,params)!==false){this.doRequest.apply(this,arguments);}
else{cb.call(scope||this,null,arg,false);}},doRequest:function(action,rs,params,reader,writer,cb,scope,options){this[action](params,reader,cb,scope,options);}});