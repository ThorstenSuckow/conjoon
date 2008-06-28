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
 * Helps adding feed views to the main content panel.
 *
 */
de.intrabuild.groupware.feeds.FeedViewBaton = function() {
	
	var openedFeeds = {};
	
	var AccountStore = de.intrabuild.util.Registry.get('de.intrabuild.groupware.feeds.AccountStore');
	
	var LinkInterceptor = de.intrabuild.groupware.util.LinkInterceptor;
	
	var contentPanel = null;
	
	var idPrefix = 'de.intrabuild.groupware.feeds.FeedItemView_';
	
	var toolbar = null;
	
	var activeRecord = null;
	
	var registerToolbar = function()
    {
        if (toolbar == null) {
            var tbarManager = de.intrabuild.groupware.ToolbarManager;      
            
			var linkButton = new Ext.Toolbar.Button({   
			    id       : 'de.intrabuild.groupware.feeds.FeedView.toolbar.LinkButton',
			    cls      : 'x-btn-text-icon',  
			    iconCls  : 'de-intrabuild-groupware-feeds-FeedViewBaton-toolbar-visitEntryButton-icon',
			    text     : '&#160;Visit entry',
			    handler  : function(){visitFeedEntry();}
			});
			
            
            toolbar = new Ext.Toolbar([
            	linkButton
            ]);        
			
			
            tbarManager.register('de.intrabuild.groupware.feeds.FeedView.toolbar', toolbar);
        }
    };   	
	
	var visitFeedEntry = function(type)
	{
		var tab = contentPanel.getActiveTab();
		
		var id = tab.id;
		
		if (!openedFeeds[id]) {
			return;	
		}
		
		window.open.defer(1, window, [LinkInterceptor.getRedirectLink(openedFeeds[id]['link'])]);	
	};
	
	return {
	
		showFeed : function(feedItemRecord, config)
		{
			if (!contentPanel) {
				contentPanel = de.intrabuild.util.Registry.get('de.intrabuild.groupware.ContentPanel');		
			}
			
			if (toolbar == null) {
				registerToolbar();	
			}
			
			var opened = openedFeeds[idPrefix+feedItemRecord.id]; 
			if (opened) {
				contentPanel.setActiveTab(opened['view']);
				return opened;	
			} else {
				
				var accRec = AccountStore.getById(feedItemRecord.get('groupwareFeedsAccountsId'));
				var link = accRec.get('link');
				var name = feedItemRecord.get('name')+' - '+accRec.get('description');
				
				var view = new Ext.Panel({
				    layout     : 'border',
				    id         : idPrefix+feedItemRecord.id,
    				title      : feedItemRecord.get('title'),
				    closable   : true,
	                iconCls    : 'de-intrabuild-groupware-feeds-FeedView-Icon',
	                hideMode   : 'offsets',
	                items      : [{
	                    region    : 'north',
	                    bodyStyle : 'border-bottom:none',
	                    cls       : 'de-intrabuild-groupware-feeds-FeedView-header',
	                    html      : 
	                    
    	                   '<div class="header">'+
    		               '<span class="date">'+Ext.util.Format.date(feedItemRecord.get('pubDate'), 'd.m.Y H:i')+'</span>'+               
   		                   '<div class="subject">'+feedItemRecord.get('title')+'</div>'+
   		                   '<div class="name">'+name+'</div>'+
   		                   '<div class="link"><a href="'+LinkInterceptor.getRedirectLink(link)+'" target="_blank">'+link+'</a></div>'+
   		                   '<div class="author">Posted by: '+feedItemRecord.get('author')+'</div>'+
    		               '</div>'
	                    
	                },{
	                    region     : 'center',
    				    listeners  : de.intrabuild.groupware.util.LinkInterceptor.getListener(),    
    	                autoScroll : true,
    	                cls        : 'de-intrabuild-groupware-feeds-FeedView-panel',
    	                html       : feedItemRecord.get('content')
				    }]
				});
				
				view.on('destroy', function(panel){
					openedFeeds[panel.id] = null;
					delete openedFeeds[panel.id];},
					de.intrabuild.groupware.email.EmailViewBaton
				);
				
				
				view.on('activate', function(panel) {
					var tbarManager = de.intrabuild.groupware.ToolbarManager;
					tbarManager.show('de.intrabuild.groupware.feeds.FeedView.toolbar');	
				}); 
				
				/**
				 * @todo check why the EmailView-toolbar is referenced here.
				 */
				view.on('deactivate', function(panel) {
					var tbarManager = de.intrabuild.groupware.ToolbarManager;
	        		tbarManager.hide('de.intrabuild.groupware.email.EmailView.toolbar');	
				}); 
				
		    	contentPanel.add(view);
		    	contentPanel.setActiveTab(view);
		    	openedFeeds[idPrefix+feedItemRecord.id] = {
		    	    view : view,
		    	    link : feedItemRecord.get('link')    
		    	};
		    	
		    	return view;
			}
			
		}	
	}
}();