/**
 * intrabuild
 * (c) 2002-2008 siteartwork.de/MindPatterns
 * license@siteartwork.de
 *
 * $Author$
 * $Id$
 * $Date$ 
 * $Revision$
 * $LastChangedDate$
 * $LastChangedBy$
 * $URL$ 
 */
 
Ext.namespace('de.intrabuild.groupware.email'); 
 
/**
 * @class de.intrabuild.groupware.email.AccountStore
 * @singleton
 */ 
de.intrabuild.groupware.email.AccountStore = function() {
	
    var _store = null;
	
	var _getStore = function()
	{
		return new Ext.data.Store({
		    storeId  : Ext.id(),
		    url      : '/groupware/email/get.email.accounts/format/json',
		    autoLoad : false,
		    pruneModifiedRecords : true,
		    reader   : new Ext.data.JsonReader({
		        root : 'accounts',
		        id   : 'id'
		    }, de.intrabuild.groupware.email.AccountRecord)
		});		
	};	
	
	return {
		
		/**
		 * 
		 * @return {Ext.data.Store}
		 */
		getInstance : function()
		{
			if (_store === null) {
				_store = _getStore();
			}
			
			return _store;
		}
		
	};
	
}();