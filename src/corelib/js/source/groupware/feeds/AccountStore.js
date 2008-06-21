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
 
var deintrabuildgroupwarefeedsAccountStore = new Ext.data.Store({
    storeId	    : Ext.id(),
    autoLoad    : false,
    reader      : new Ext.data.JsonReader({
                      root: 'accounts',
                      id : 'id'
                  }, de.intrabuild.groupware.feeds.AccountRecord),
    url         : '/groupware/feeds/get.feed.accounts/format/json',
    listeners   : de.intrabuild.groupware.feeds.FeedRunner.getListener()
});

de.intrabuild.util.Registry.register('de.intrabuild.groupware.feeds.AccountStore', deintrabuildgroupwarefeedsAccountStore);