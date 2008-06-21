Ext.namespace('de.intrabuild.util.PreLoader');

de.intrabuild.util.PreLoader = function() {
	
	var _kernel = function(){
		this.addEvents(
			/**
			 * Fired when all objects have been sucessfully loaded
			 */
			'load'
		);
	};
	
	Ext.extend(_kernel, Ext.util.Observable, {
		
	});
	
	var kernel = new _kernel();
	
	var stores = {};
	
	var storeCount = 0;
	
	var storeLoaded = function(store)
	{
	    store.un('load', storeLoaded, de.intrabuild.util.PreLoader);
		storeCount--;
		if (storeCount == 0) {
			kernel.fireEvent('load');
		} else if (storeCount < 0 ) {
			throw('de.intrabuild.util.PreLoader: storeCount is negative, but most not be.');	
		}	
	};
	
	var storeDestroyed = function(store)
	{
		store.un('load', storeLoaded, de.intrabuild.util.PreLoader);
		storeCount--;
		delete stores[Ext.StoreMgr.getKey(store)];
	};
	
	return {
		
		on : function(eventName, fn, scope, parameters)
		{
			kernel.on(eventName, fn, scope, parameters);
		},
		
		addStore : function(store)
		{
			var id = Ext.StoreMgr.getKey(store);
			
			if (!id) {
				throw('de.intrabuild.util.PreLoader: store must have a property storeId or id.');	
			}
			
			if (stores[id]) {
				throw('de.intrabuild.util.PreLoader: store with id '+id+' was already added.');
			}
			
			store.on('load', 	storeLoaded, de.intrabuild.util.PreLoader);
			store.on('destroy', storeDestroyed, de.intrabuild.util.PreLoader);
			stores[id] = store;
			storeCount++;
		},
		
		load : function()
		{
			for (var i in stores) {
				stores[i].load();	
			}	
		}	
		
	};
	
}();