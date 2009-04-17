/*
 * Ext JS Library 3.0 RC1
 * Copyright(c) 2006-2009, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://extjs.com/license
 */


Ext.util.DelayedTask=function(fn,scope,args){var me=this,NULL=null,id=NULL,_delay,_time,call=function(){var now=new Date().getTime();if(now-_time>=_delay){clearInterval(id);id=NULL;fn.apply(scope,args||[]);}};me.delay=function(delay,newFn,newScope,newArgs){if(id&&delay!=_delay){this.cancel();}
_delay=delay;_time=new Date().getTime();fn=newFn||fn;scope=newScope||scope;args=newArgs||args;if(!id){id=setInterval(call,_delay);}};me.cancel=function(){if(id){clearInterval(id);id=NULL;}};};