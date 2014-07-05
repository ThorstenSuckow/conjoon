/**
 * conjoon
 * (c) 2007-2014 conjoon.org
 * licensing@conjoon.org
 *
 * conjoon
 * Copyright (C) 2014 Thorsten Suckow-Homberg/conjoon.org
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
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
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
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
        this.mon(recTweetStore,    'exception', this._defaultLoadExceptionListener, this);
        this.mon(recentTweetStore, 'exception', this._defaultLoadExceptionListener, this);
        this.mon(friendsStore,     'exception', this._defaultLoadExceptionListener, this);

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

    /**
     * Overriden to add listeners to accountStore/accountButton events for
     * triggering saveState.
     *
     * @inheritdoc
     */
    initStateEvents : function() {

        var me = this,
            store = me.accountStore,
            accountButton = me.getChooseAccountButton(),
            opt = {delay : 100};

        com.conjoon.groupware.service.TwitterPanel.superclass.initStateEvents.apply(me, arguments);

        me.mon(accountButton, 'exitclick', me.saveState, me, opt);
        me.mon(accountButton, 'checkchange', me.saveState, me, opt);
        me.mon(store, 'remove', me.saveState, me, opt);
    },

    /**
     * Overriden to init menu item check in afterrender event for selecting
     * last active twitter account.
     *
     * @inheritdoc
     */
    applyState : function(state){

        var me = this;

        com.conjoon.groupware.service.TwitterPanel.superclass.applyState.apply(me, arguments);

        if (state.currentAccountId > 0) {

            this.on('afterrender', function() {
                var menuItem = this.getChooseAccountButton().
                               getMenuItemForAccountId(state.currentAccountId);
                if (menuItem) {
                    menuItem.setChecked(true);
                }
            }, me, {single : true});
        }
    },

    /**
     * Overriden to return needed state information.
     *
     * @inheritdoc
     */
    getState : function() {

        var me = this,
            state = {
            collapsed : me.collapsed,
            hidden    : !me.isVisible(),
            currentAccountId : me.getCurrentAccountId()
        };

        if (!me.collapsed && me.resizable !== false) {
            state.height = this.getHeight();
        }

        return state;
    },

// -------- listeners

    /**
     * The default load exception listener. Will switch back to the first active
     * card if necessary.
     *
     */
    _defaultLoadExceptionListener : function(proxy, type, action, options, response, arg)
    {
        this._clearCurrentAccount();
        this.getChooseAccountButton().resetAccountMenuItemStates(true);
    },

    _onShow : Ext.emptyFn
});
