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
 
Ext.namespace('de.intrabuild.groupware.feeds'); 
 
/**
 * @class de.intrabuild.groupware.feeds.FeedStore
 * @singleton
 */ 
de.intrabuild.groupware.feeds.FeedStore = function() {
    
    var _store = null;
    
    var _getStore = function()
    {
        return new Ext.data.GroupingStore({
		    storeId     : Ext.id(),
		    autoLoad    : false,
		    reader      : new Ext.data.JsonReader({
		                      root: 'items',
		                      id : 'id'
		                  }, de.intrabuild.groupware.feeds.ItemRecord),
		    sortInfo    : {field: 'pubDate', direction: "DESC"},
		    url         : '/groupware/feeds/get.feed.items/format/json',
		    groupField  : 'name',
		    baseParams  : {
		        removeold : true    
		    }
		});
    };  
    
    return {
        
        /**
         * 
         * @return {Ext.data.GroupingStore}
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