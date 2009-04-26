/*
 * Ext JS Library 3.0 Pre-alpha
 * Copyright(c) 2006-2008, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://extjs.com/license
 */


Ext.data.DataProxy=function(conn){conn=conn||{};Ext.apply(this,conn);if(conn.api){var valid=Ext.data.Api.isValid(conn.api);if(valid!==true){throw new Error('Ext.data.DataProxy#constructor recieved an invalid API-configuration "'+valid.join(', ')+'".  Please ensure your proxy API-configuration contains only the actions "'+Ext.data.Api.getVerbs().join(', '));}}
else{this.api={};this.api[Ext.data.Api.CREATE]=undefined;this.api[Ext.data.Api.READ]=undefined;this.api[Ext.data.Api.UPDATE]=undefined;this.api[Ext.data.Api.DESTROY]=undefined;}
this.addEvents('before'+Ext.data.READ,Ext.data.READ,'beforewrite','write');Ext.data.DataProxy.superclass.constructor.call(this);};Ext.extend(Ext.data.DataProxy,Ext.util.Observable,{setApi:function(){if(arguments.length==1){var valid=Ext.data.Api.isValid(arguments[0]);if(valid===true){this.api=arguments[0];}
else{throw new Error('Ext.data.DataProxy#setApi received invalid API action(s) "'+valid.join(', ')+'".  Valid API actions are: '+Ext.data.Api.getVerbs().join(', '));}}
else if(arguments.length==2){if(!Ext.data.Api.isVerb(arguments[0])){throw new Error('Ext.data.DataProxy#setApi received an invalid API action "'+arguments[0]+'".  Valid API actions are: '+Ext.data.Api.getVerbs().join(', '))}
this.api[arguments[0]]=arguments[1];}},request:function(action,rs,params,reader,callback,scope,options){params=params||{};if((action==Ext.data.Api.READ)?this.fireEvent("before"+action,this,params,options):this.fireEvent("beforewrite",this,action,params,options)!==false){this.doRequest.apply(this,arguments);}
else{callback.call(scope||this,null,arg,false);}},load:function(params,reader,callback,scope,arg){this.doRequest(Ext.data.READ,null,params,reader,callback,scope,arg);},doRequest:function(action,rs,params,reader,callback,scope,options){this[action](params,reader,callback,scope,options);}});