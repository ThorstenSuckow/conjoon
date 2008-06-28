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
 
de.intrabuild.groupware.feeds.FeedRunner = function(){
    
    var commitId = Ext.data.Record.COMMIT;
    
    var store = new Ext.data.Store({
        autoLoad   : false,
        reader     : new Ext.data.JsonReader({
                          root: 'items',
                          id : 'id'
                      }, de.intrabuild.groupware.feeds.ItemRecord),
        url        : '/groupware/feeds/get.feed.items/format/json',
        baseParams : {
            removeold : false    
        }
    });
    
    var firstTimeLoaded = false;
    
    var task = null;
    
    var runnable = false;
    
    var updateInterval = Number.MAX_VALUE;
    
	var defaultUpdateInterval = 3600;
	
    var onStoreLoad = function(store, records, options)
    {
        if (!records || (records && !records.length)) {
            return;
        }
        
        for (var i = 0, len = records.length; i < len; i++) {
            feedStore.addSorted(records[i].copy);    
        }
        
        store.removeAll();
        if (len > 0) {
            notifyUser(len);
        }
    };
    
    var onStoreChange = function(store, record, operation)
    {
        if (operation && (typeof(operation) == 'string') && operation != commitId) {
            return;
        }
        
        stopRunning();
        
        var recs = store.getRange();
        
        for (var i = 0, len = recs.length; i < len; i++) {
            updateInterval = Math.min(recs[i].get('updateInterval'), updateInterval);
        }
        
		run();
    };
    
    var stopRunning = function()
    {
        runnable = false;
        if (task) {
            Ext.TaskMgr.stop(task);  
        }
    };
    
    var run = function()
    {
        task = {
            run      : updateFeeds,
            interval : (updateInterval <= 0 ? 
                        defaultUpdateInterval : 
						updateInterval)*1000
        }
        Ext.TaskMgr.start(task);  
    };
    
    var updateFeeds = function()
    {
        if (!runnable) {
            runnable = true;
            return;    
        }
        
        if (!firstTimeLoaded) {
            store.load();    
            firstTimeLoaded = true;
        } else {
            store.reload();    
        }
        
    };
    
    var notifyUser = function(feedCount)
    {
        var text = "There "+(feedCount > 1 ? "are " : "is ")+"<b>"+feedCount+"</b> new feed"+(feedCount > 1 ? "s " : " ")+"available.";  
        
        new Ext.ux.ToastWindow({    
            title   : "New feed"+(feedCount > 1 ? "s" : "")+" available",    
            html    : text
        }).show(document); 

    };
    
    // leave this here since listener only works if observer function gets defined before
    // (stopRunning, onStoreLoad)
    var feedStore = de.intrabuild.util.Registry.get('de.intrabuild.groupware.feeds.FeedStore');
    feedStore.on('beforeload', stopRunning, de.intrabuild.groupware.feeds.FeedRunner);
    store.on('load', onStoreLoad, de.intrabuild.groupware.feeds.FeedRunner);
    return {
        
        
        getListener : function()
        {
            return {
                add    : onStoreChange,    
                remove : onStoreChange,    
                update : onStoreChange,    
                load   : onStoreChange
            };
        }    
        
    };
    
}();