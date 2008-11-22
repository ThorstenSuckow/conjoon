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
 * @class de.intrabuild.groupware.feeds.FeedViewBaton
 * @singleton
 *
 * Manages the opening of feed items as new tabs. Will not open a new tab if the
 * tab with the passed feed item id does already exist.
 * The class will also load the feed cpntents from the server if, and only if,
 * the fully configured feed item record was not submitted.
 *
 */
de.intrabuild.groupware.feeds.FeedViewBaton = function() {

	var openedFeeds = {};

	var AccountStore = de.intrabuild.groupware.feeds.AccountStore.getInstance();

	var LinkInterceptor = de.intrabuild.groupware.util.LinkInterceptor;

	var contentPanel = null;

	var idPrefix = 'de.intrabuild.groupware.feeds.FeedItemView_';

	var toolbar = null;

	var activeRecord = null;

    var _requestIds = {};

	var registerToolbar = function()
    {
        if (toolbar == null) {
            var tbarManager = de.intrabuild.groupware.ToolbarManager;

			var linkButton = new Ext.Toolbar.Button({
			    id       : 'de.intrabuild.groupware.feeds.FeedView.toolbar.LinkButton',
			    cls      : 'x-btn-text-icon',
			    iconCls  : 'de-intrabuild-groupware-feeds-FeedViewBaton-toolbar-visitEntryButton-icon',
			    text     : '&#160;'+de.intrabuild.Gettext.gettext("Visit entry"),
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

		(function() {
            this.open(LinkInterceptor.getRedirectLink(openedFeeds[id]['link']));
		}).defer(1, window);
	};

	/**
	 * Loads the feed's contents for the specified id from the server.
	 *
	 * @param {Number} id
	 * @param {String} panelId
	 *
	 */
	var loadFeedContents = function(id, panelId)
	{
	    if (_requestIds[panelId]) {
	        return;
	    }

	    _requestIds[panelId] = Ext.Ajax.request({
	        url    : '/groupware/feeds/get.feed.content/format/json',
	        params : {
	            id : id
	        },
			panelId : panelId,
	        success : onFeedLoadSuccess,
	        failure : onFeedLoadFailure
	    });
	};

	/**
	 * Callback for the successfull loading of a feed's content.
	 *
	 * @param {XmlHttpResponse} response
	 * @param {Object} options
	 */
	var onFeedLoadSuccess = function(response, options)
	{
		var inspector = de.intrabuild.groupware.ResponseInspector;

		var data = inspector.isSuccess(response);

		if (data === null) {
			onFeedLoadFailure(response, options);
            return;
		}
		var item = data.item;
		var rec = de.intrabuild.util.Record.convertTo(
            de.intrabuild.groupware.feeds.ItemRecord,
			item,
			item.id
		);

        _requestIds[options.panelId] = null;
        delete _requestIds[options.panelId];

		Ext.ux.util.MessageBus.publish(
			'de.intrabuild.groupware.feeds.FeedViewBaton.onFeedLoadSuccess', {
			id : item.id
		});

		openedFeeds[options.panelId]['body'].update(rec.get('content'));
	};

	/**
     * Callback for an erroneous loading of a feed's content.
     *
     * @param {XmlHttpResponse} response
     * @param {Object} options
     */
    var onFeedLoadFailure = function(response, options)
    {
        _requestIds[options.panelId] = null;
        delete _requestIds[options.panelId];

        de.intrabuild.groupware.ResponseInspector.handleFailure(response, {
			onLogin : {
				fn : function(){
					loadFeedContents(options.params.id, options.panelId);
				}
			}
		});

    };

	/**
	 *
	 *
	 * @param {de.intrabuild.groupware.feeds.FeedItemRecord}
	 */
	var buildPanel = function(feedItemRecord)
	{
        var accRec = AccountStore.getById(feedItemRecord.get('groupwareFeedsAccountsId'));
        var link   = accRec.get('link');
        var name   = feedItemRecord.get('name')+' - '+accRec.get('description');

		var body = new Ext.Panel({
			region     : 'center',
			listeners  : de.intrabuild.groupware.util.LinkInterceptor.getListener(),
			autoScroll : true,
			cls        : 'de-intrabuild-groupware-feeds-FeedView-panel',
			html       : ''
        });

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
                   '<div class="author">'+de.intrabuild.Gettext.gettext("Posted by")+': '+feedItemRecord.get('author')+'</div>'+
                   '</div>'

            },body
			]
        });

		var tbarManager = de.intrabuild.groupware.ToolbarManager;

        view.on('destroy', function(panel){
			delete openedFeeds[panel.id];
			if (_requestIds[panel.id]) {
                Ext.Ajax.abort(_requestIds[panel.id]);
                delete _requestIds[panel.id];
            }

			// hide this only if there are no more feed tabs to display
            // this is needed if there is no tab which could be activated
            // which shows a toolbar upon activate
            var hide = true;
            for (var i in openedFeeds) {
                hide = false;
                break;
            }
            if (hide) {
                tbarManager.hide('de.intrabuild.groupware.feeds.FeedView.toolbar');
            }
        });


        view.on('activate', function(panel) {
            tbarManager.show('de.intrabuild.groupware.feeds.FeedView.toolbar');
        });

        view.on('deactivate', function(panel) {
            tbarManager.hide('de.intrabuild.groupware.feeds.FeedView.toolbar');
        });

        contentPanel.add(view);
        contentPanel.setActiveTab(view);
        openedFeeds[idPrefix+feedItemRecord.id] = {
            view : view,
			body : body.body,
            link : feedItemRecord.get('link')
        };

        return view;
	};

	return {

        /**
         * Displays a feed item's content and it's details in a new tab.
         * If the second argument is set to true, the baton will load additionally
         * feed's contents from the server.
         *
         * @param {de.intrabuild.groupware.feeds.FeedItemRecord} feedItemRecord either the fully configured
         * feed item record or the id of the feed item lo load
         * @param {Boolean} loadFromServer true if the record is not fully
         * configured and needs loading from the server, otherwise false
         */
		showFeed : function(feedItemRecord, loadFromServer)
		{
			if (!contentPanel) {
				contentPanel = de.intrabuild.util.Registry.get('de.intrabuild.groupware.ContentPanel');
			}

			if (toolbar == null) {
				registerToolbar();
			}

			var recordId = -1;
			var isRecord = true;

			var opened = openedFeeds[idPrefix+feedItemRecord.id];

			if (opened) {
				contentPanel.setActiveTab(opened['view']);
				return opened;
			} else {
				buildPanel(feedItemRecord);
				if (loadFromServer === true) {
					loadFeedContents(
                        feedItemRecord.id,
					    idPrefix+feedItemRecord.id
					);
				} else {
                    openedFeeds[idPrefix+feedItemRecord.id]['body'].update(feedItemRecord.get('content'));
				}
			}
		}
	}
}();