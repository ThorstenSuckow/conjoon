/**
 * conjoon
 * (c) 2002-2012 siteartwork.de/conjoon.org
 * licensing@conjoon.org
 *
 * $Author$
 * $Id$
 * $Date$
 * $Revision$
 * $LastChangedDate$
 * $LastChangedBy$
 * $URL$
 */

Ext.namespace('com.conjoon.groupware.feeds');

/**
 * @class com.conjoon.groupware.feeds.FeedViewBaton
 * @singleton
 *
 * Manages the opening of feed items as new tabs. Will not open a new tab if the
 * tab with the passed feed item id does already exist.
 * The class will also load the feed cpntents from the server if, and only if,
 * the fully configured feed item record was not submitted.
 *
 */
com.conjoon.groupware.feeds.FeedViewBaton = function() {

    var openedFeeds = {};

    var AccountStore = com.conjoon.groupware.feeds.AccountStore.getInstance();

    var LinkInterceptor = com.conjoon.groupware.util.LinkInterceptor;

    var contentPanel = null;

    var idPrefix = 'com.conjoon.groupware.feeds.FeedItemView_';

    var toolbar = null;

    var activeRecord = null;

    var _requestIds = {};

    var registerToolbar = function()
    {
        if (toolbar == null) {
            var tbarManager = com.conjoon.groupware.workbench.ToolbarController;

            var linkButton = new Ext.Toolbar.Button({
                id       : 'com.conjoon.groupware.feeds.FeedView.toolbar.LinkButton',
                cls      : 'x-btn-text-icon',
                iconCls  : 'com-conjoon-groupware-feeds-FeedViewBaton-toolbar-visitEntryButton-icon',
                text     : '&#160;'+com.conjoon.Gettext.gettext("Visit entry"),
                handler  : function(){visitFeedEntry();}
            });


            toolbar = new Ext.Toolbar([
                linkButton
            ]);


            tbarManager.register('com.conjoon.groupware.feeds.FeedView.toolbar', toolbar);
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
     * @param {Number} groupwareFeedsAccountsId
     * @param {String} panelId
     *
     */
    var loadFeedContents = function(id, groupwareFeedsAccountsId, panelId)
    {
        if (_requestIds[panelId]) {
            return;
        }

        _requestIds[panelId] = Ext.Ajax.request({
            url    : './groupware/feeds.item/get.feed.content/format/json',
            params : {
                id                       : id,
                groupwareFeedsAccountsId : groupwareFeedsAccountsId
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
        var inspector = com.conjoon.groupware.ResponseInspector;

        var data = inspector.isSuccess(response);

        if (!data || (data && !data.item)) {
            onFeedLoadFailure(response, options);
            return;
        }
        var item = data.item;
        var rec = com.conjoon.util.Record.convertTo(
            com.conjoon.groupware.feeds.ItemRecord,
            item,
            item.id
        );

        _requestIds[options.panelId] = null;
        delete _requestIds[options.panelId];

        Ext.ux.util.MessageBus.publish(
            'com.conjoon.groupware.feeds.FeedViewBaton.onFeedLoadSuccess', {
            id : item.id
        });

        openedFeeds[options.panelId].view.setTitle(rec.get('title'));
        openedFeeds[options.panelId].view.setIconClass(
            'com-conjoon-groupware-feeds-FeedView-Icon'
        );

        openedFeeds[options.panelId].body.body.update(rec.get('content'));
    };

    /**
     * Callback for an erroneous loading of a feed's content.
     *
     * @param {XmlHttpResponse} response
     * @param {Object} options
     */
    var onFeedLoadFailure = function(response, options)
    {
        var responseInspector = com.conjoon.groupware.ResponseInspector;

        _requestIds[options.panelId] = null;
        delete _requestIds[options.panelId];

        var succ        = responseInspector.isSuccess(response);
        var authFailure = responseInspector.isAuthenticationFailure(response);
        // success is eitehr false or null
        // if succ is null, the no response was returned or response
        // could not be decoded, if success === false, then the item was
        // not found on the server
        if (!succ && !authFailure) {
            var panel = openedFeeds[options.panelId];
            if (panel && panel.view) {
                panel.view.ownerCt.remove(panel.view);
            }

            if (succ === false) {
                Ext.ux.util.MessageBus.publish(
                    'com.conjoon.groupware.feeds.FeedViewBaton.onFeedLoadFailure',
                    {id : options.params.id}
                );
            }
        }

        responseInspector.handleFailure(response, {
            onLogin : {
                fn : function(){
                    loadFeedContents(
                        options.params.id,
                        options.params.groupwareFeedsAccountsId,
                        options.panelId
                    );
                }
            },
            title   : com.conjoon.Gettext.gettext("Error while loading feed item")
        });

    };

    /**
     *
     *
     * @param {com.conjoon.groupware.feeds.ItemRecord}
     * @param {Boolean} loadFromServer
     */
    var buildPanel = function(feedItemRecord, loadFromServer)
    {
        var accRec = AccountStore.getById(feedItemRecord.get('groupwareFeedsAccountsId')),
            link   = accRec.get('link').
            name   = feedItemRecord.get('name')+' - '+accRec.get('description'),
            body = new Ext.Panel({
                region     : 'center',
                autoScroll : true,
                cls        : 'com-conjoon-groupware-feeds-FeedView-panel',
                html       : ''
            }),
            key = idPrefix+feedItemRecord.id,
            view;

        view = new Ext.Panel({
            /**
             * Not creating a specific class in this case. We simply set cnCompType
             * as a helper to indicate we have a panel representing a feed entry
             * @ticket CN-789
             */
            cnCompType : 'feedViewPanel',
            feedItemRecord : feedItemRecord,
            cnLoadContentFromServer : loadFromServer,
            /**
             * @inheritdoc
             * @ticket CN-789
             */
            getState : function() {

                var me = this,
                    feedData = me.feedItemRecord.data;

                // remove unnedded information from the record
                delete feedData.description;
                delete feedData.content;

                return {
                    cnCompType : 'feedViewPanel',
                    feedItem   : feedData,
                    title      : feedItemRecord.get('title'),
                    iconCls    : 'com-conjoon-groupware-feeds-FeedView-Icon'
                };

            },
            layout     : 'border',
            id         : idPrefix+feedItemRecord.id,
            title      : loadFromServer
                         ? com.conjoon.Gettext.gettext("Loading...")
                         : feedItemRecord.get('title'),
            iconCls    : loadFromServer
                         ? 'com-conjoon-groupware-pending-icon'
                         : 'com-conjoon-groupware-feeds-FeedView-Icon',
            closable   : true,
            hideMode   : 'offsets',
            listeners  : com.conjoon.groupware.util.LinkInterceptor.getListener(),
            items      : [{
                region    : 'north',
                bodyStyle : 'border-bottom:none',
                cls       : 'com-conjoon-groupware-feeds-FeedView-header',
                html      :
                   '<div class="header">'+
                   '<span class="date">'+Ext.util.Format.date(feedItemRecord.get('pubDate'), 'd.m.Y H:i')+'</span>'+
                   '<div class="subject">'+feedItemRecord.get('title')+'</div>'+
                   '<div class="name">'+name+'</div>'+
                   '<div class="link"><a href="'+link+'" target="_blank">'+link+'</a></div>'+
                   '<div class="author">'+
                   com.conjoon.Gettext.gettext("Posted by")+': '+
                   (
                    feedItemRecord.get('authorEmail')
                    ? '<a href="mailto:'+feedItemRecord.get('authorEmail')+'">'+feedItemRecord.get('author')+'</a>'
                    : feedItemRecord.get('author')
                   )+
                   (
                    feedItemRecord.get('authorUri')
                    ? '<a class="url" href="'+feedItemRecord.get('authorUri')+'"><img src="'+Ext.BLANK_IMAGE_URL+'" border="0" /></a>'
                    : ''
                   )+
                   '</div>'+
                   '</div>'

            },body
            ]
        });

        var tbarManager = com.conjoon.groupware.workbench.ToolbarController;

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
                tbarManager.hide('com.conjoon.groupware.feeds.FeedView.toolbar');
            }
        }, view, {single : true});


        view.on('activate', function(panel) {
            if (panel.cnLoadContentFromServer === true) {
                var feedItemRecord = panel.feedItemRecord;
                loadFeedContents(
                    feedItemRecord.id,
                    feedItemRecord.get('groupwareFeedsAccountsId'),
                    idPrefix+feedItemRecord.id
                );
            }
            tbarManager.show('com.conjoon.groupware.feeds.FeedView.toolbar');
        });

        view.on('deactivate', function(panel) {
            tbarManager.hide('com.conjoon.groupware.feeds.FeedView.toolbar');
        });

        contentPanel.add(view);
        contentPanel.setActiveTab(view);

        openedFeeds[key] = {
            view : view,
            /**
             * We are only storing a reference to the body and concatenate
             * to the body's body later on, since the body might not be rendered
             * immediately (see afterrender)
             * @ticket CN-789
             */
            body : body,
            link : feedItemRecord.get('link')
        };

        return openedFeeds[key];
    };

    return {

        /**
         * Displays a feed item's content and it's details in a new tab.
         * If the second argument is set to true, the baton will load additionally
         * feed's contents from the server.
         *
         * @param {com.conjoon.groupware.feeds.ItemRecord} feedItemRecord the fully configured
         * feed item record
         * @param {Boolean} loadFromServer true if the record is not fully
         * configured and needs loading from the server, otherwise false
         *
         * @return {Object}
         */
        showFeed : function(feedItemRecord, loadFromServer)
        {
            if (!contentPanel) {
                contentPanel = com.conjoon.util.Registry.get('com.conjoon.groupware.ContentPanel');
            }

            if (toolbar == null) {
                registerToolbar();
            }

            var recordId = -1;
            var isRecord = true;

            var opened = openedFeeds[idPrefix+feedItemRecord.id];

            if (opened) {
                // needs to be set before activate
                opened.view.cnLoadContentFromServer = loadFromServer;
                contentPanel.setActiveTab(opened['view']);
            } else {
                opened = buildPanel(feedItemRecord, loadFromServer);
                opened.view.cnLoadContentFromServer = loadFromServer;
            }

            if (loadFromServer !== true) {
                openedFeeds[idPrefix+feedItemRecord.id].body.body.update(feedItemRecord.get('content'));
            }


            return opened;
        }
    }
}();
