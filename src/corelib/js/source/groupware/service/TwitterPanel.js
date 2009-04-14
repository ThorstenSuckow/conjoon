/**
 * conjoon
 * (c) 2002-2009 siteartwork.de/conjoon.org
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

Ext.namespace('com.conjoon.groupware.service');

/**
 * A Panel that contains all components needed to use with a
 * Twitter account.
 *
 * @class com.conjoon.groupware.service.TwitterPanel
 * @extends com.conjoon.service.twitter.TwitterPanel
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
com.conjoon.groupware.service.TwitterPanel = Ext.extend(com.conjoon.service.twitter.TwitterPanel, {


    /**
     * Inits this component.
     *
     */
    initComponent : function()
    {
        var recTweetStore = new com.conjoon.service.twitter.data.TweetStore({
            url : './service/twitter/get.recent.tweets/format/json'
        });

        var recentTweets = new com.conjoon.service.twitter.TweetList({
            store           : recTweetStore,
            clearBeforeLoad : true
        });

        var tweetPoller = new com.conjoon.service.twitter.data.TweetPoller({
             url         : './service/twitter/get.recent.tweets/format/json',
             updateStore : recTweetStore
        });

        var recentTweetStore = new com.conjoon.service.twitter.data.TweetStore({
            url : './service/twitter/get.users.recent.tweets/format/json'
        });

        var usersRecentTweets = new com.conjoon.service.twitter.TweetList({
            store           : recentTweetStore,
            clearBeforeLoad : true,
            trackOver       : false,
            overClass       : null,
            cls             : 'com-conjoon-service-twitter-TweetList usersRecentTweets'
        });

        var friendsStore = new com.conjoon.service.twitter.data.TwitterUserStore({
            url : './service/twitter/get.friends/format/json'
        });

        var friendsList = new com.conjoon.service.twitter.FriendsList({
            store : friendsStore
        });

        // add loadexception listeners to the attached stores.
        recTweetStore.on('loadexception',    this._defaultLoadExceptionListener, this);
        recentTweetStore.on('loadexception', this._defaultLoadExceptionListener, this);
        friendsStore.on('loadexception',     this._defaultLoadExceptionListener, this);

        var inputBox = new com.conjoon.service.twitter.InputBox();

        var userInfoBox = new com.conjoon.service.twitter.UserInfoBox({
            tweetStore : recentTweetStore
        });

        Ext.apply(this, {
            accountStore      : com.conjoon.service.twitter.data.AccountStore.getInstance(),
            iconCls           : 'com-conjoon-service-twitter-twitterIcon',
            inputBox          : inputBox,
            userInfoBox       : userInfoBox,
            usersRecentTweets : usersRecentTweets,
            recentTweets      : recentTweets,
            friendsList       : friendsList,
            tweetPoller       : tweetPoller
        });

        this.on('render', this._onShow, this, {single : true});

        com.conjoon.groupware.service.TwitterPanel.superclass.initComponent.call(this);
    },

    initEvents : function()
    {
        com.conjoon.groupware.service.TwitterPanel.superclass.initEvents.call(this);


    },

// -------- listeners

    /**
     * The default load exception listener. Will switch back to the first active
     * card if necessary.
     *
     */
    _defaultLoadExceptionListener : function()
    {
        this._clearCurrentAccount();
        this.getChooseAccountButton().resetAccountMenuItemStates(true);
    },

    _onShow : Ext.emptyFn
});