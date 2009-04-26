/*
 * Ext JS Library 3.0 Pre-alpha
 * Copyright(c) 2006-2008, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://extjs.com/license
 */


Ext.data.Api=(function(){return{CREATE:'create',READ:'load',UPDATE:'save',DESTROY:'destroy',getVerbs:function(){return[this.CREATE,this.READ,this.UPDATE,this.DESTROY];},isVerb:function(action,crud){var found=false;crud=crud||this.getVerbs();for(var n=0,len=crud.length;n<len;n++){if(crud[n]==action){found=true;break;}}
return found;},isValid:function(api){var invalid=[];var crud=this.getVerbs();for(var action in api){if(!this.isVerb(action,crud)){invalid.push(action);}}
return(!invalid.length)?true:invalid;}}})();