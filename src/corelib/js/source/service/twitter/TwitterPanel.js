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

Ext.namespace('com.conjoon.service.twitter');

/**
 * A default panel that renders various components for using the Twitter
 * service, such as updating the status, showing freinds list etc.
 * It serves as a controller for the attached components and is capable of
 * managing multiple accounts from which a user can choose.
 *
 * @class com.conjoon.service.twitter.TwitterPanel
 * @extends Ext.Panel
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
com.conjoon.service.twitter.TwitterPanel = Ext.extend(Ext.Panel, {

    /**
     * @cfg {com.conjoon.service.twitter.data.AccountStore} accountStore The
     * store used to choose between various accounts. this instance will be
     * passed to the _chooseAccountButton so the contents of the store can be
     * rendered into a menu.
     */

    /**
     * @cfg {com.conjoon.service.twitter.InputBox} inputBox The inputBox used to
     * update a user's status.
     */

    /**
     * @cfg {com.conjoon.service.twitter.TweetList} recentTweets The TweetList used
     * to render the recent tweets associated with a chosen account.
     */

    /**
     * @cfg {com.conjoon.service.twitter.TweetList} usersRecentTweets The TweetList
     * used to render tweets of a chosen user.
     */

    /**
     * @cfg {com.conjoon.service.twitter.userInfoBox} userInfoBox The UserInfoBox used
     * to display a user's informations.
     */

    /**
     * @cfg {com.conjoon.service.twitter.FriendsList} friendsList The friendsList used
     * to render the followers of the chosen account.
     */

    /**
     * @cfg {com.conjoon.service.twitter.data.TweetPoller} tweetPoller The tweet Poller
     * that updates the records of the recentTweets' store
     */

    /**
     * @cfg {String} titleTpl This template is used to be rendered as the title for this
     * panel.
     */
    titleTpl : 'Twitter - {0}',

    /**
     * @type {com.conjoon.service.twitter.HomePanel} _homePanel The HomePanel used to
     * render as teh startscreen.
     */
    _homePanel : null,

    /**
     * @type {Ext.Button} _showRecentTweetsButton The button used to switch to the
     * recentTweets panel.
     */
    _recentTweetsButton : null,

    /**
     * @type {Ext.Button} _showFriendsButton The button used to display a list of a
     * user's followers.
     */
    _showFriendsButton : null,

    /**
     * @type {Ext.Button} _showInputButton The button used to show/hide the InputBox.
     */
    _showInputButton : null,

    /**
     * @type {Ext.Button} _switchFriendshipButton The button used to
     * switch friendship to a user
     */
    _switchFriendshipButton : null,

    /**
     * @type {com.conjoon.service.twitter.AccountButton} _chooseAccountButton The
     * button used to choose between different accounts.
     */
    _chooseAccountButton : null,

    /**
     * @type {com.conjoon.service.twitter.RecentTweetsContainer} _recentTweetsContainer
     * The RecentTweetsContainer for the tweetList and the InputBox.
     */
    _recentTweetsContainer : null,

    /**
     * @type {com.conjoon.service.twitter.TwitterUserContainer} _usersRecentTweetsContainer
     * The TwitterUserContainer used to display tweets of/information about a specific
     * user.
     */
    _usersRecentTweetsContainer : null,

    /**
     * @type {Number} _currentAccountId Holds the id of the currently selected account.
     * Will default to 0 or -1 if no current account was selected.
     */
    _currentAccountId : -1,

    /**
     * @type {Number} _currentTwitterId Holds the id of the currently selected account as
     * provided by twitter. Defaults to 0 or -1 if no current account was selected.
     */
    _currentTwitterId : -1,

    /**
     * @type Ext.Toolbar} _toolbar
     */
    _toolbar : null,

    /**
     * @type {Object} _metaTaskConfig Configuration object for the task that updates
     * meta information on the recent tweets list in a frequent interval.
     */
    _metaTaskConfig : null,

    /**
     * @type {Object} _metaTask The current task that updates the meta information on the
     * current tweet list in a frequent interval.
     */
    _metaTask : null,

    /**
     * @type {String} _lastRequestedUser The sreen name for the user which tweets were most
     * recently requested.
     */
    _lastRequestedUser : null,

    /**
     * Inits this component.
     *
     */
    initComponent : function()
    {
        // overwrite hideMode for Ext.ux.layout.SlideLayout
        this.getHomePanel().hideMode                  = 'visibility';
        this.getRecentTweetsContainer().hideMode      = 'visibility';
        this.friendsList.hideMode                     = 'visibility';
        this.getUsersRecentTweetsContainer().hideMode = 'visibility';

        Ext.apply(this, {
            title      : String.format(this.titleTpl,
                com.conjoon.Gettext.gettext("choose account")
            ),
            activeItem : 0,
            layout     : new Ext.ux.layout.SlideLayout(),
            items      : [
                this.getHomePanel(),
                this.getRecentTweetsContainer(),
                this.friendsList,
                this.getUsersRecentTweetsContainer()
            ],
            cls    : 'com-conjoon-service-twitter-TwitterPanel',
            bbar   : this.getToolbar()
        });

        com.conjoon.service.twitter.TwitterPanel.superclass.initComponent.call(this);
    },

    /**
     * Calls the parent's implementation and attaches the listeners for the various
     * components.
     */
    initEvents : function()
    {
        com.conjoon.service.twitter.TwitterPanel.superclass.initEvents.call(this);

        this.mon(this.tweetPoller, 'updateavailable', this._onTweetPollerUpdateAvailable, this);

        this.mon(this.friendsList, 'show', this._onFriendsListShow, this);

        this.mon(this.inputBox, 'render', this.showInputBox.createDelegate(this, [false]), this);

        this.mon(this.recentTweets,      'click', this._onRecentTweetClick, this);
        this.mon(this.usersRecentTweets, 'click', this._onRecentTweetClick, this);
        this.mon(this.friendsList,       'click', this._onFriendsListClick, this);

        this.mon(this.accountStore, 'remove', this._onAccountStoreRemove, this);
        this.mon(this.accountStore, 'update', this._onAccountStoreUpdate, this);

        this.mon(this.userInfoBox.tweetStore, 'beforeload', this._onBeforeUsersRecentTweetStoreLoad, this);

        this.mon(this.userInfoBox, 'userload',       this._onUserLoad, this);
        this.mon(this.userInfoBox, 'userloadfailed', this._onUserLoadFailed, this);

        this.on('resize', function() {
            this.inputBox.setWidth(this.el.getSize().width);
        }, this);


        this.mon( this.getUsersRecentTweetsContainer(), 'hide', this._onUsersRecentTweetsHide, this);

        this.mon(this.recentTweets.store, 'beforeload', this._onRecentTweetBeforeLoad, this);
        this.mon(this.recentTweets.store, 'load',       this._onRecentTweetLoad,       this);

        this.mon(this.inputBox.getUpdateButton(), 'click', this._onUpdateButtonClick, this);

        this.mon(this.getChooseAccountButton(), 'checkchange', this._onAccountButtonCheckChange, this);

        this.mon(this.getChooseAccountButton(), 'exitclick', this._onAccountButtonExitClick, this);
    },

// -------- listeners

    /**
     * Gets fired when new tweets available from the tweet poller.
     *
     * @param {com.conjoon.service.twitter.data.TweetPoller} tweetPoller
     * @param {Array} records
     */
    _onTweetPollerUpdateAvailable : function(tweetPoller, records)
    {
        Ext.ux.util.MessageBus.publish('com.conjoon.service.twitter.newTweets', {
            tweets : records
        });
    },

    /**
     * Listener for the beforeload event of the recent tweet's store.
     * Default implementation will hide the switchFriendshipButton.
     *
     * @protected
     */
    _onBeforeUsersRecentTweetStoreLoad : function()
    {
        this.getSwitchFriendshipButton().setVisible(false);
    },

    /**
     * Listener for the userloadfailed event.
     *
     * @param {com.conjoon.service.twitter.userInfoBox} userInfoBox
     */
    _onUserLoadFailed : function()
    {
        this.handleSystemMessage(
            new com.conjoon.SystemMessage({
                title : com.conjoon.Gettext.gettext("Error while loading recent tweets"),
                text  : String.format(
                    com.conjoon.Gettext.gettext("Loading recent tweets for user \"{0}\" failed, due to an internal error triggered by the Twitter API."),
                    this._lastRequestedUser
                ),
                type  : com.conjoon.SystemMessage.TYPE_ERROR
            })
        );

        this.getShowRecentTweetsButton().toggle(true);
    },

    /**
     * Listener for the userload event of the userInfoBox. Default implementation
     * sets the state of the switchFriendshipButton according to the relationship
     * of the currently logged in user and the loaded user.
     *
     * @protected
     */
    _onUserLoad : function(userInfoBox, record)
    {
        if (record.get('userId') == this._currentTwitterId) {
            this.getSwitchFriendshipButton().setVisible(false);
        } else {

            this.getSwitchFriendshipButton().setTooltip(
                (record.get('isFollowing')
                ?  com.conjoon.Gettext.gettext("Unfollow this user")
                :  com.conjoon.Gettext.gettext("Follow this user"))
            );

            this.getSwitchFriendshipButton().setIconClass(
                record.get('isFollowing')
                ?  'unfollow_user_icon'
                :  'follow_user_icon'
            );
            this.getSwitchFriendshipButton().setVisible(true);
        }
    },

    /**
     * Listener for the hide event of the users recent tweet's container.
     * This default implementation will hide the switchFriendshipButton and
     * cancel any ongoing request of the users recent tweet's proxy.
     *
     * @protected
     */
    _onUsersRecentTweetsHide : function()
    {
        var proxy = this.userInfoBox.tweetStore.proxy;
        if (proxy.activeRequest[Ext.data.Api.actions.read]) {
            proxy.getConnection().abort(proxy.activeRequest[Ext.data.Api.actions.read]);
        }

        this.getSwitchFriendshipButton().setVisible(false);
    },

    /**
     * Called before the recentTweets'store loads. Will stop the tweetPoller's
     * task.
     *
     * @param {Ext.data.Store} store
     * @param {Object} options
     */
    _onRecentTweetBeforeLoad : function(store, options)
    {
        this.stopMetaTask();
        this.tweetPoller.stopPolling();
    },

    /**
     * Called when the recentTweets'store loads. Will start polling with the
     * account id specified in the ooptions param's "id" property.
     *
     * @param {Ext.data.Store} store
     * @param {Object} options
     */
    _onRecentTweetLoad : function(store, records, options)
    {
        var rec = this.accountStore.getById(options.params.id);

        if (!rec) {
            return;
        }

        this.startMetaTask();
        this.tweetPoller.startPolling(
            options.params.id,
            rec.get('updateInterval')
        );
    },

    /**
     * Callback for teh remove event of the account store. If the removed
     * account equals to teh currently selected, the view will be reset by
     * calling this._clearCurrentAccount().
     *
     * @param {Ext.data.Store} store
     * @param {Ext.data.Record} record
     * @param {Number} index
     */
    _onAccountStoreRemove : function(store, record, index)
    {
        if (this._currentAccountId == record.id) {
            this._clearCurrentAccount();
        }
    },

    /**
     * Listens to the accountStore's update event.
     * This implementation will change the title of the panel  text if a "commit"
     * was specified in "operation" if the edited account equals to the currently
     * selected account. Additionally, polling will be stoppped and restarted with
     * the value from the account store's updateInterval property.
     *
     * @param {Ext.data.Store} store
     * @param {com.conjoon.service.twitter.data.AccountRecord} record
     * @param {String} operation
     *
     * @protected
     */
    _onAccountStoreUpdate : function(store, record, operation)
    {
        if (operation == 'commit' && this._currentAccountId == record.id) {
            this.setTitle(
                String.format(
                    this.titleTpl, record.get('name')
                )
            );
            this.tweetPoller.stopPolling();

            this.tweetPoller.startPolling(
                record.id,
                record.get('updateInterval')
            );
        }
    },

    /**
     * Listens to the "exitclick" event of the _chooseAccountButton's exit menu item.
     *
     * @param {com.conjoon.service.twitter.AccountButton} accountButton
     * @param {Ext.menu.Item} menuItem
     *
     * @protected
     */
    _onAccountButtonExitClick : function(accountButton, menuItem)
    {
        this._clearCurrentAccount();
    },

    /**
     * Listens to the checkchange event of the _chooseAccountButton's menu items.
     *
     * @param {com.conjoon.service.twitter.AccountButton} accountButton
     * @param {Ext.menu.CheckItem} menuItem
     * @param {Boolean} checked
     *
     * @protected
     */
    _onAccountButtonCheckChange : function(accountButton, checkItem, checked)
    {
        if (checked) {

            var rec = null;

            var accountItemMap = accountButton._accountItemMap;

            for (var i in accountItemMap) {
                if (accountItemMap[i].id == checkItem.id) {
                    rec = this.accountStore.getById(i);
                }
            }

            if (rec != null && rec.id == this._currentAccountId) {
                return;
            }

            if (rec == null) {
                this._accountChangeFailed();
                this.setTitle(
                    String.format(this.titleTpl, com.conjoon.Gettext.gettext("choose account"))
                );
                return;
            }

            this._clearCurrentAccount();

            this.setTitle(
                String.format(this.titleTpl, checkItem.text)
            );
            this._loadAccount.defer(1, this, [rec]);
        }

    },

    /**
     * Listens to the "click" event of the InputBox' updateButton.
     *
     * @protected
     */
    _onUpdateButtonClick : function()
    {
        var v = this.inputBox.getMessage().trim();

        if (v == "") {
            this.handleSystemMessage(new com.conjoon.SystemMessage({
                title : com.conjoon.Gettext.gettext("Error"),
                text  : com.conjoon.Gettext.gettext("Cannot send an empty message!"),
                type  : com.conjoon.SystemMessage.TYPE_ERROR
            }));
            return;
        } else if (v.length > this.inputBox.inputMaxLength) {
            this.handleSystemMessage(new com.conjoon.SystemMessage({
                title : com.conjoon.Gettext.gettext("Error"),
                text  : String.format(
                    com.conjoon.Gettext.gettext("This update is over {0} characters!"),
                    this.inputBox.inputMaxLength
                ),
                type  : com.conjoon.SystemMessage.TYPE_ERROR
            }));
            return;
        } else if (this._currentAccountId <= 0) {
            this.handleSystemMessage(new com.conjoon.SystemMessage({
                title : com.conjoon.Gettext.gettext("Error"),
                text  : com.conjoon.Gettext.gettext("There seems no account to be configured!"),
                type  : com.conjoon.SystemMessage.TYPE_ERROR
            }));
            return;
        }

        this.inputBox.setInputBusy(true);
        this.getToolbar().setDisabled(true);

        var replyStatus = this.inputBox.getReplyStatus();

        Ext.Ajax.request({
            url    : './service/twitter/send.update/format/json',
            params : {
                message           : v,
                accountId         : this._currentAccountId,
                inReplyToStatusId : (replyStatus != null
                                    ? replyStatus[0]
                                    : null)
            },
            success : this._onUpdateSuccess,
            failure : this._onUpdateFailure,
            scope   : this
        });
    },

    /**
     * Callback for the success-event of the Ajax request that requests
     * to (un)favorite a specific tweet.
     *
     * @param {XmlHttpResponse} response
     * @param {Object} options
     *
     * @protected
     */
    _onFavoriteTweetSuccess : function(response, options)
    {
        var insp = com.conjoon.groupware.ResponseInspector;

        if (!insp.isSuccess(response)) {
            this._onFavoriteTweetFailure(response, options);
            return;
        }

        Ext.fly(options.target).removeClass('pending');

        var json          = com.conjoon.util.Json;
        var responseValue = json.getResponseValues(response.responseText);

        var rec = com.conjoon.util.Record.convertTo(
            com.conjoon.service.twitter.data.TweetRecord,
            responseValue.favoritedTweet,
            responseValue.favoritedTweet.id
        );

        var store  = this.recentTweets.store;
        var updRec = store.getById(rec.id);

        if (!updRec) {
            return;
        }

        updRec.set('favorited', rec.get('favorited'));
    },

    /**
     * Callback for the failure-event of the Ajax request that requests
     * to (un)favorite a specific tweet.
     *
     * @param {XmlHttpResponse} response
     * @param {Object} options
     *
     * @protected
     */
    _onFavoriteTweetFailure : function(response, options)
    {
        Ext.fly(options.target).removeClass('pending');
        com.conjoon.groupware.ResponseInspector.handleFailure(response);
    },

    /**
     * Callback for the success-event of the Ajax request that requests
     * to delete a specific tweet.
     * The HtmlNode representing the record to delete can be found in the
     * options-property "item".
     *
     * @param {XmlHttpResponse} response
     * @param {Object} options
     *
     * @protected
     */
    _onDeleteTweetSuccess : function(response, options)
    {
        var insp = com.conjoon.groupware.ResponseInspector;

        if (!insp.isSuccess(response)) {
            this._onDeleteTweetFailure(response, options);
            return;
        }

        Ext.fly(options.item).unmask();
        this.getToolbar().setDisabled(false);

        var json          = com.conjoon.util.Json;
        var responseValue = json.getResponseValues(response.responseText);

        this.recentTweets.removeTweet(responseValue.deletedTweet.id);
    },

    /**
     * Callback for the failure-event of the Ajax request that requests
     * to delete a tweet.
     * The HtmlNode representing the record to delete can be found in the
     * options-property "item".
     *
     * @param {XmlHttpResponse} response
     * @param {Object} options
     *
     * @protected
     */
    _onDeleteTweetFailure : function(response, options)
    {
        this.getToolbar().setDisabled(false);
        Ext.fly(options.item).unmask();
        Ext.fly(options.item).highlight('ff0000');

        com.conjoon.groupware.ResponseInspector.handleFailure(response);
    },

    /**
     * Callback for the success-event of the Ajax request that sends
     * the status update to the server.
     *
     * @param {XmlHttpResponse} response
     * @param {Object} options
     *
     * @protected
     */
    _onUpdateSuccess : function(response, options)
    {
        var insp = com.conjoon.groupware.ResponseInspector;

        if (!insp.isSuccess(response)) {
            this._onUpdateFailure(response, options);
            return;
        }

        var json          = com.conjoon.util.Json;
        var responseValue = json.getResponseValues(response.responseText);

        var rec = com.conjoon.util.Record.convertTo(
            com.conjoon.service.twitter.data.TweetRecord,
            responseValue.tweet,
            responseValue.tweet.id
        );

        this.inputBox.setInputBusy(false);
        this.getToolbar().setDisabled(false);
        this.inputBox.setMessage("");
        this.recentTweets.store.insert(0, [rec]);
        this.getShowInputButton().toggle(false);
    },

    /**
     * Callback for the failure-event of the Ajax request that sends
     * the status update to the server.
     *
     * @param {XmlHttpResponse} response
     * @param {Object} options
     *
     * @protected
     */
    _onUpdateFailure : function(response, options)
    {
        this.inputBox.setInputBusy(false);
        this.getToolbar().setDisabled(false);
        com.conjoon.groupware.ResponseInspector.handleFailure(response);
    },

    /**
     * Callback for the "click" event for the FriendsList.
     *
     * @param {com.conjoon.service.twitter.Friendslist} dataView The DataView
     * that triggered this event
     * @param {Number} index The index of the target node
     * @param {HtmlElement} item native HtmlElement on which this event occured
     * @param {Ext.EvenObject} e The raw Ext.EventObject
     */
    _onFriendsListClick : function(dataView, index, item, e)
    {
        this.showUserInfo(this.friendsList.getSelectedRecords()[0]);
    },

    /**
     * Callback for the "click" event for the RecentTweetsList.
     * Depending on the target, the event will be delegated to other methods
     * if a click on the reply/delete link/icon was detected.
     *
     * @param {com.conjoon.service.twitter.TweetList} dataView The DataView
     * that triggered this event
     * @param {Number} index The index of the target node
     * @param {HtmlElement} item native HtmlElement on which this event occured
     * @param {Ext.EvenObject} e The raw Ext.EventObject
     *
     * @protected
     */
    _onRecentTweetClick : function(dataView, index, item, e)
    {

        switch (e.getTarget().className) {
            case 'tweet_reply_icon':
                this._handleReplyClick(dataView, index, item, e);
            break;

            case 'authorName':
                this.showUserInfo(dataView.getSelectedRecords()[0]);
            break;

            case 'tweetUrl':
                this._handleTweetUrlClick(dataView, index, item, e);
            break;

            case 'screenName':
                this.showUserInfo(e.getTarget().firstChild.data);
            break;

            case 'tweet_delete_icon':
                this._handleTweetDeleteClick(dataView, index, item, e);
            break;

            case 'tweet_bookmark_icon':
                this._handleTweetBookmarkClick(dataView, index, item, e, true);
            break;

            case 'tweet_unbookmark_icon':
                this._handleTweetBookmarkClick(dataView, index, item, e, false);
            break;

            case 'when':
                this.showUserInfo(dataView.getSelectedRecords()[0], 'status');
            break;

            case 'source':
                this._handleTweetSourceClick(dataView, index, item, e, false);
            break;

            case 'inReplyTo':
                this.showUserInfo(dataView.getSelectedRecords()[0], 'replyStatus');
            break;
        }
    },

    /**
     * Callback for the FriendsList's "show" event.
     * Requests the FirendsList's store to reload the contents based on
     * the currently selected account.
     *
     * @protected
     */
    _onFriendsListShow : function()
    {
        if (this._currentAccountId <= 0) {
            return;
        }
        var rec = this.accountStore.getById(
            this._currentAccountId
        );

        if (!rec) {
            return;
        }

        var store = this.friendsList.store;

        if (store.getRange().length == 0) {
            store.load.defer(1, store, [{
                params : {
                    id : rec.get('id')
                }
            }]);
        }

    },

    /**
     * Callback for the toggle event of the _showInputButton.
     * Will either show or hide the InputBox based on the passed state.
     *
     * @param {Ext.button} button
     * @param {Boolean} pressed
     *
     * @protected
     */
    _onShowInputButton : function(button, pressed)
    {
        if (pressed) {
            this.showInputBox(true);
        } else {
            this.showInputBox(false);
        }
    },

    /**
     * Handler for the _showRecentTweets/_showFriendsButton.
     * Will either render the _showRecentTweetsButton disabled/pressed
     * if the recentTweets view is shown or enabled if it is currently not shown.
     *
     * @param {Ext.button} button
     * @param {Boolean} pressed
     *
     * @protected
     */
    _onButtonToggle : function(button, pressed)
    {
        if (button.id == this._showRecentTweetsButton.id) {

            if (pressed) {
                this.getLayout().setActiveItem(
                    this.getRecentTweetsContainer().getId()
                );
                this._showRecentTweetsButton.setDisabled(true);
                this._showInputButton.setDisabled(false);
            }

        } else if (button.id == this._showFriendsButton.id) {
            if (pressed) {
                this.getLayout().setActiveItem(this.friendsList.getId());

                this._showRecentTweetsButton.suspendEvents();
                this._showRecentTweetsButton.toggleHandler = null;
                this._showRecentTweetsButton.toggle(false);
                this._showRecentTweetsButton.setDisabled(false);
                this._showRecentTweetsButton.resumeEvents();
                this._showRecentTweetsButton.toggleHandler = this._onButtonToggle;
                this._showInputButton.setDisabled(true);
            } else {
                this.getLayout().setActiveItem(
                    this.getRecentTweetsContainer().getId()
                );
                this._showRecentTweetsButton.suspendEvents();
                this._showRecentTweetsButton.toggleHandler = null;
                this._showRecentTweetsButton.toggle(true);
                this._showRecentTweetsButton.setDisabled(true);
                this._showRecentTweetsButton.resumeEvents();
                this._showRecentTweetsButton.toggleHandler = this._onButtonToggle;
                this._showInputButton.setDisabled(false);
            }
        }
    },

// -------- public API

    /**
     * Stops the task that is responsible for updating a tweets list meta information
     * in a frequent interval.
     *
     */
    stopMetaTask : function()
    {
        if (this._metaTask) {
            this._metaTask.stop(this._metaTaskConfig);
        }

        this._task = null;
    },

    /**
     * Starts the task that is responsible for updating a tweets list meta information
     * in a frequent interval.
     */
    startMetaTask : function()
    {
        this.stopMetaTask();

        this._metaTaskConfig = {
            run      : this.recentTweets.updateMetaInfo,
            scope    : this.recentTweets,
            interval : 60000
        };

        this._metaTask = new Ext.util.TaskRunner();
        this._metaTask.start(this._metaTaskConfig);
    },

    /**
     * Returns the currently used account. Return sa value equal or less to 0
     * if there is currently no account chosen.
     *
     * @return {Number}
     */
    getCurrentAccountId : function()
    {
        return this._currentAccountId;
    },

    /**
     * Handles a system message triggered by this controller.
     *
     * @param {com.conjoon.SystemMessage) message
     */
    handleSystemMessage : function(message)
    {
        com.conjoon.SystemMessageManager.show({
            title   : message.title,
            msg     : message.text,
            buttons : Ext.Msg.OK,
            scope   : this,
            icon    : Ext.Msg.ERROR,
            cls     :'com-conjoon-msgbox-error',
            width   :400
        });
    },

    /**
     * Clears the _currentAccountId and resets some components' states
     * before another account is chosen.
     *
     * @protected
     */
    _clearCurrentAccount : function()
    {
        this._currentAccountId = -1;
        this._currentTwitterId = -1;

        this.stopMetaTask();
        this.tweetPoller.stopPolling();

        if (this.inputBox.rendered) {
            this.inputBox.setMessage("");
        }

        // remove all data from the stores
        this.recentTweets.store.removeAll();
        this.recentTweets.setAccountRecord(null);
        this.usersRecentTweets.store.removeAll();
        this.friendsList.store.removeAll();

        this.getShowInputButton().toggle(false);

        this.getChooseAccountButton().getExitMenuItem().setDisabled(true);

        this.getShowFriendsButton().toggle(false);
        this.getShowRecentTweetsButton().toggle(false);
        this.getShowInputButton().setDisabled(true);
        this.getShowRecentTweetsButton().setDisabled(true);
        this.getShowFriendsButton().setDisabled(true);

        this.setTitle(
            String.format(this.titleTpl, com.conjoon.Gettext.gettext("choose account"))
        );

        this.getLayout().setActiveItem(this.getHomePanel().getId())
    },

    /**
     * Delegate for the callback for the "click" event for the RecentTweetsList.
     * gets called internally if a click on a link with the class "tweetUrl" is
     * clicked.
     *
     * @param {com.conjoon.service.twitter.TweetList} dataView The DataView
     * that triggered this event
     * @param {Number} index The index of the target node
     * @param {HtmlElement} item native HtmlElement on which this event occured
     * @param {Ext.EvenObject} e The raw Ext.EventObject
     *
     * @protected
     */
    _handleTweetUrlClick : function(dataView, index, item, e)
    {
        com.conjoon.groupware.util.LinkInterceptor.handleLinkClick(e.getTarget());
    },

    /**
     * Delegate for the callback for the "click" event for the RecentTweetsList.
     * Gets called internally if a click on the "source" link happend.
     *
     * @param {com.conjoon.service.twitter.TweetList} dataView The DataView
     * that triggered this event
     * @param {Number} index The index of the target node
     * @param {HtmlElement} item native HtmlElement on which this event occured
     * @param {Ext.EvenObject} e The raw Ext.EventObject
     *
     * @protected
     */
    _handleTweetSourceClick : function(dataView, index, item, e)
    {
        com.conjoon.groupware.util.LinkInterceptor.handleLinkClick(e.getTarget());
    },

    /**
     * Delegate for the callback for the "click" event for the RecentTweetsList.
     * Gets called internally if a click on the "bookmark"/"unbookmark"
     * link/icon happend.
     *
     * @param {com.conjoon.service.twitter.TweetList} dataView The DataView
     * that triggered this event
     * @param {Number} index The index of the target node
     * @param {HtmlElement} item native HtmlElement on which this event occured
     * @param {Ext.EvenObject} e The raw Ext.EventObject
     * @param {Boolean} Whether to favorite this tweet - true to favorite it, otherwise
     * false
     *
     * @protected
     */
    _handleTweetBookmarkClick : function(dataView, index, item, e, favorite)
    {
        var selRec = this.recentTweets.getSelectedRecords()[0];

        if (!selRec) {
            return;
        }

        Ext.fly(e.getTarget()).addClass('pending');

        Ext.Ajax.request({
            target : e.getTarget(),
            url    : './service/twitter/favorite.tweet/format/json',
            params : {
                accountId : this._currentAccountId,
                tweetId   : selRec.id,
                favorite  : favorite ? 1 : 0
            },
            success : this._onFavoriteTweetSuccess,
            failure : this._onFavoriteTweetFailure,
            scope   : this
        });
    },

    /**
     * Sends a request to switch the friendship between the currently logged in
     * user and the user that is passed via the record property.
     *
     * @param {com.conjoon.service.twitter.UserInfoBox} userInfoBox
     * @param {Ext.data.Record} record
     */
    _onSwitchFriendshipButton : function()
    {
        var record = this.userInfoBox.getLoadedUser();

        if (!record) {
            return;
        }

        this.userInfoBox.el.mask(
            (record.get('isFollowing')
             ? com.conjoon.Gettext.gettext("Unfollowing user...")
             : com.conjoon.Gettext.gettext("Following user...")),
            'x-mask-loading'
        );

        this.mun(this.usersRecentTweets, 'click', this._onRecentTweetClick, this);

        this.usersRecentTweets.setDisabled(true);
        this.getToolbar().setDisabled(true);

        Ext.Ajax.request({
            user   : record,
            url    : './service/twitter/switch.friendship/format/json',
            params : {
                accountId        : this._currentAccountId,
                screenName       : record.get('screenName'),
                createFriendship : !record.get('isFollowing')
            },
            success : this._onSwitchFriendshipSuccess,
            failure : this._onSwitchFriendshipFailure,
            scope   : this
        });
    },

    /**
     *
     * @param {XmlHttpResponse} response
     * @param {Object} options
     */
    _onSwitchFriendshipSuccess : function(response, options)
    {
        var insp = com.conjoon.groupware.ResponseInspector;

        if (!insp.isSuccess(response)) {
            this._onSwitchFriendshipFailure(response, options);
            return;
        }

        this.mon(this.usersRecentTweets, 'click', this._onRecentTweetClick, this);
        this.usersRecentTweets.setDisabled(false);
        this.getToolbar().setDisabled(false);
        this.userInfoBox.el.unmask();

        var json          = com.conjoon.util.Json;
        var responseValue = json.getResponseValues(response.responseText);

        options.user.set('isFollowing', responseValue.isFollowing);
        this.userInfoBox.loadUser(options.user, true);
    },

    /**
     *
     * @param {XmlHttpResponse} response
     * @param {Object} options
     */
    _onSwitchFriendshipFailure : function(response, options)
    {
        this.mon(this.usersRecentTweets, 'click', this._onRecentTweetClick, this);
        this.usersRecentTweets.setDisabled(false);
        this.getToolbar().setDisabled(false);
        this.userInfoBox.el.unmask();
        com.conjoon.groupware.ResponseInspector.handleFailure(response);
    },

    /**
     * Delegate for the callback for the "click" event for the RecentTweetsList.
     * Gets called internally if a click on the "delete" link/icon happend.
     *
     * @param {com.conjoon.service.twitter.TweetList} dataView The DataView
     * that triggered this event
     * @param {Number} index The index of the target node
     * @param {HtmlElement} item native HtmlElement on which this event occured
     * @param {Ext.EvenObject} e The raw Ext.EventObject
     *
     * @protected
     */
    _handleTweetDeleteClick : function(dataView, index, item, e)
    {
        var selRec = this.recentTweets.getSelectedRecords()[0];

        if (!selRec) {
            return;
        }

        com.conjoon.SystemMessageManager.confirm(
            new com.conjoon.SystemMessage({
                title : com.conjoon.Gettext.gettext("Delete Tweet"),
                text  : com.conjoon.Gettext.gettext(
                    "Are you sure you want to delete this tweet? There is no \"undo\"!"
                ),
                type  : com.conjoon.SystemMessage.TYPE_CONFIRM
            }), {
                fn : function(button) {
                    if (button != 'yes') {
                        return;
                    }
                    this.getToolbar().setDisabled(true);
                    Ext.fly(item).mask(
                        com.conjoon.Gettext.gettext("Deleting..."),
                        'x-mask-loading'
                    );
                    Ext.Ajax.request({
                        item   : item,
                        url    : './service/twitter/delete.tweet/format/json',
                        params : {
                            accountId : this._currentAccountId,
                            tweetId   : selRec.id
                        },
                        success : this._onDeleteTweetSuccess,
                        failure : this._onDeleteTweetFailure,
                        scope   : this
                    });
                },
                scope : this
            }
        );
    },

    /**
     * Delegate for the callback for the "click" event for the RecentTweetsList.
     * gets called internally if a click on the "reply" link/icon happend.
     *
     * @param {com.conjoon.service.twitter.TweetList} dataView The DataView
     * that triggered this event
     * @param {Number} index The index of the target node
     * @param {HtmlElement} item native HtmlElement on which this event occured
     * @param {Ext.EvenObject} e The raw Ext.EventObject
     *
     * @protected
     */
    _handleReplyClick : function(dataView, index, item, e)
    {
        var v = this.inputBox.getMessage();

        var selRec = this.recentTweets.getSelectedRecords()[0];

        var recipient   = selRec.get('screenName');
        var atRecipient = '@' + recipient;
        var statusId    = selRec.get('id');

        if (!this._showInputButton.pressed) {
            this._showInputButton.toggle(true);
        }

        this.inputBox.focus();
        this.inputBox.setReplyStatus([statusId, recipient]);

        if (v.indexOf(atRecipient) != -1) {
            v = v.replace(new RegExp(atRecipient), "");
        }

        v = atRecipient+' '+v;
        this.inputBox.setMessage(v);
    },

    /**
     * Loads the data for the specified account into this panel and
     * its components. Will also request the recentTweets' store to reload
     * is contents while setting its accountRecord property to the given record.
     *
     * @param {com.conjoon.service.twitter.data.AccountRecord} record
     *
     * @protected
     */
    _loadAccount : function(record)
    {
        this._currentAccountId = -1;
        this._currentTwitterId = -1;

        this.getShowRecentTweetsButton().toggle(true);

        this.getShowInputButton().setDisabled(false);
        this.getShowRecentTweetsButton().setDisabled(false);
        this.getShowFriendsButton().setDisabled(false);

        this.getChooseAccountButton().getExitMenuItem().setDisabled(false);

        if (!record) {
            return;
        }

        this._currentAccountId = record.get('id');
        this._currentTwitterId = record.get('twitterId');

        this.recentTweets.setAccountRecord(record);
        var store = this.recentTweets.store;

        if (store.getRange().length == 0) {
            store.load.defer(1, store, [{
                params : {
                    id : record.get('id')
                }
            }]);
        }
    },

    /**
     * Loads the information for the specified user into the TwitterUserContainer.
     * Based on the provided params, a full list of recent tweets for the user will be
     * loaded, or just a single entry according to the parameter "type".
     *
     * @param {com.conjoon.service.twitter.data.TwitterUserRecord|
     * com.conjoon.service.twitter.data.TweetRecord|String} record or the screenName
     * of the user to fetch the information for
     * @param {String} type if anything but "status" or "replyStatus", a full list for the
     * user will be provided. If "status", a single entry for this user will be shown, if
     * "replyStatus", the tweet on which this tweet is an reply will be shown, only if the
     * "inReplyToStatusId" property of the passed tweetRecord is a number greater than 0.
     */
    showUserInfo : function(tweetRecord, type)
    {
        this._showInputButton.setDisabled(true);

        this._showRecentTweetsButton.suspendEvents();
        this._showFriendsButton.suspendEvents();
        this._showRecentTweetsButton.toggleHandler = null;
        this._showFriendsButton.toggleHandler      = null;

        this._showRecentTweetsButton.toggle(false);
        this._showRecentTweetsButton.setDisabled(false);

        this._showFriendsButton.toggle(false);

        this._showRecentTweetsButton.resumeEvents();
        this._showFriendsButton.resumeEvents();
        this._showRecentTweetsButton.toggleHandler = this._onButtonToggle;
        this._showFriendsButton.toggleHandler      = this._onButtonToggle;

        this.getLayout().setActiveItem(
            this.getUsersRecentTweetsContainer().getId()
        );

        var params = {
            id : this._currentAccountId
        };

        if (type === 'status') {
            Ext.apply(params, {
                statusId : tweetRecord.id
            });
        } else if (type === 'replyStatus' && tweetRecord.get('inReplyToStatusId') > 0) {
            Ext.apply(params, {
                statusId : tweetRecord.get('inReplyToStatusId')
            });
        }

        if ((typeof tweetRecord) != 'string') {
            this._lastRequestedUser = tweetRecord.get('screenName');
            Ext.apply(params, {
                userId     : tweetRecord.get('userId')
                           ? tweetRecord.get('userId')
                           : tweetRecord.get('id')
            });
        } else {
            this._lastRequestedUser = tweetRecord;
            Ext.apply(params, {
                userName : tweetRecord
            });
        }

        this.usersRecentTweets.store.load({
            params : params
        })
    },

    /**
     * Shows/hides the InputBox.
     *
     * @param {Boolean} show true to show the InputBox, false to hide it.
     */
    showInputBox : function(show)
    {
        var rc = this.getRecentTweetsContainer();

        if (show) {
            var conf = {
                duration : .2,
                callback   : function() {
                    rc.syncSize();
                    this.inputBox.focus();
                    this.inputBox.onResize();
                },
                scope : this
            };

            this.inputBox.el.slideIn('t', conf);
        } else {

            var conf = {
                duration   : .2,
                useDisplay : true,
                callback   : rc.syncSize,
                scope      : rc
            };

            this.inputBox.el.slideOut('t', conf);
        }
    },

    /**
     * Returns the RecentTweetsContainer.
     *
     * @return {com.conjoon.service.twitter.RecentTweetsContainer}
     */
    getRecentTweetsContainer : function()
    {
        if (!this._recentTweetsContainer) {
            this._recentTweetsContainer = this._getRecentTweetsContainer();
        }

        return this._recentTweetsContainer;
    },

    /**
     * Returns the TwitterUserContainer.
     *
     * @return {com.conjoon.service.twitter.TwitterUserContainer}
     */

    getUsersRecentTweetsContainer : function()
    {
        if (!this._usersRecentTweetsContainer) {
            this._usersRecentTweetsContainer = this._getUsersRecentTweetsContainer();
        }

        return this._usersRecentTweetsContainer;
    },

    /**
     * Returns the button that's controlling if the recent tweets list should be
     * shown or not.
     *
     * @return {Ext.Button}
     */
    getShowRecentTweetsButton : function()
    {
        if (!this._showRecentTweetsButton) {
            this._showRecentTweetsButton = this._getShowRecentTweetsButton();
        }

        return this._showRecentTweetsButton;
    },

    /**
     * Returns the button that's controlling if the friends list should be
     * shown or not.
     *
     * @return {Ext.Button}
     */
    getShowFriendsButton : function()
    {
        if (!this._showFriendsButton) {
            this._showFriendsButton = this._getShowFriendsButton();
        }

        return this._showFriendsButton;
    },

    /**
     * Returns the button that controlls if the InputBox should be shown or not.
     *
     * @return {Ext.Button}
     */
    getShowInputButton : function()
    {
        if (!this._showInputButton) {
            this._showInputButton = this._getShowInputButton();
        }

        return this._showInputButton;
    },

    /**
     * Returns the button that controlls if a friendship to a user should
     * be switched.
     *
     * @return {Ext.Button}
     */
    getSwitchFriendshipButton : function()
    {
        if (!this._switchFriendshipButton) {
            this._switchFriendshipButton = this._getSwitchFriendshipButton();
        }

        return this._switchFriendshipButton;
    },

    /**
     * Returns the AccountButton used by this component.
     *
     * @return {com.conjoon.service.twitter.AccountButton}
     */
    getChooseAccountButton : function()
    {
        if (!this._chooseAccountButton) {
            this._chooseAccountButton = this._getChooseAccountButton();
        }

        return this._chooseAccountButton;
    },

    /**
     * Returns the component that is used as a start screen for this
     * application.
     *
     * @return {com.conjoon.service.twitter.HomePanel}
     */
    getHomePanel : function()
    {
        if (!this._homePanel) {
            this._homePanel = this._getHomePanel();
        }

        return this._homePanel;
    },

    /**
     * returns the toolbar for this panel.
     *
     * @return {Ext.Toolbar}
     */
    getToolbar : function()
    {
        if (!this._toolbar) {
            this._toolbar = this._getToolbar();
        }

        return this._toolbar;
    },

// -------- builders

    /**
     * Override this to add custom behavior.
     *
     * @return {com.conjoon.service.twitter.HomePanel}
     *
     * @protected
     */
    _getHomePanel : function()
    {
        return new com.conjoon.service.twitter.HomePanel();
    },

    /**
     * Override this to add custom behavior.
     *
     * @return {Ext.Button}
     *
     * @protected
     */
    _getShowRecentTweetsButton : function()
    {
        return new Ext.Button({
            toggleGroup   :  'com-conjoon-service-twitter',
            enableToggle  : true,
            pressed       : false,
            disabled      : true,
            toggleHandler : this._onButtonToggle,
            scope         : this,
            iconCls       : 'show_recent_icon'
        });
    },

    /**
     * Override this to add custom behavior.
     *
     * @return {com.conjoon.service.twitter.AccountButton}
     *
     * @protected
     */
    _getChooseAccountButton : function()
    {
        return new com.conjoon.service.twitter.AccountButton({
            accountStore : this.accountStore
        });
    },

    /**
     * Override this to add custom behavior.
     *
     * @return {Ext.Button}
     *
     * @protected
     */
    _getShowInputButton : function()
    {
        return new Ext.Button({
            enableToggle  : true,
            scope         : this,
            pressed       : false,
            disabled      : true,
            iconCls       : 'show_input_icon',
            toggleHandler : this._onShowInputButton,
            scope         : this
        });
    },

    /**
     * Override this to add custom behavior.
     *
     * @return {Ext.Button}
     *
     * @protected
     */
    _getSwitchFriendshipButton : function()
    {
        return new Ext.Button({
            scope   : this,
            hidden  : true,
            iconCls : 'follow_user_icon',
            handler : this._onSwitchFriendshipButton,
            scope   : this
        });
    },

    /**
     * Override this to add custom behavior.
     *
     * @return {Ext.Button}
     *
     * @protected
     */
    _getShowFriendsButton : function()
    {
        return new Ext.Button({
            toggleGroup   : 'com-conjoon-service-twitter',
            enableToggle  : true,
            disabled      : true,
            toggleHandler : this._onButtonToggle,
            scope         : this,
            iconCls       : 'show_friends_icon'
        });
    },


    /**
     * Override this to add custom behavior.
     *
     * @return {com.conjoon.service.twitter.RecentTweetsContainer}
     *
     * @protected
     */
    _getRecentTweetsContainer : function()
    {
        return new com.conjoon.service.twitter.RecentTweetsContainer({
            inputBox     : this.inputBox,
            recentTweets : this.recentTweets
        });
    },

    /**
     * Override this to add custom behavior.
     *
     * @return {com.conjoon.service.twitter.TwitterUserContainer}
     *
     * @protected
     */
    _getUsersRecentTweetsContainer : function()
    {
        return new com.conjoon.service.twitter.TwitterUserContainer({
            userInfoBox       : this.userInfoBox,
            usersRecentTweets : this.usersRecentTweets
        });
    },

    /**
     * Override this to add custom behavior.
     *
     * @return {Ext.Toolbar}
     */
    _getToolbar : function()
    {
        return new Ext.Toolbar({
            items : [
                this.getShowRecentTweetsButton(),
                this.getShowFriendsButton(),
                '->',
                this.getSwitchFriendshipButton(),
                this.getShowInputButton(),
                '-',
                this.getChooseAccountButton()
            ]
        });
    }

});