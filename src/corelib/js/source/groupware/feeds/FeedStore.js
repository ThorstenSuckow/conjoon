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
 
var deintrabuildgroupwarefeedsFeedStore = new Ext.data.GroupingStore({
    storeId	    : Ext.id(),
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

de.intrabuild.util.Registry.register('de.intrabuild.groupware.feeds.FeedStore', deintrabuildgroupwarefeedsFeedStore);