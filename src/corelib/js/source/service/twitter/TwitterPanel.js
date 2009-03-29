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
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
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
     * @type Ext.Toolbar} _toolbar
     */
    _toolbar : null,

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

        this.friendsList.on('show', this._onFriendsListShow, this);

        this.inputBox.on('render', this.showInputBox.createDelegate(this, [false]), this);

        this.recentTweets.on('click', this._onRecentTweetClick, this);
        this.friendsList.on('click',  this._onFriendsListClick, this);

        this.recentTweets.store.on('beforeload', this._onRecentTweetBeforeLoad, this);
        this.recentTweets.store.on('load',       this._onRecentTweetLoad,       this);

        this.tweetPoller.on('updateempty', this._onTweetPollerUpdateEmpty, this);

        this.inputBox.getUpdateButton().on('click', this._onUpdateButtonClick, this);

        this.getChooseAccountButton().on('checkchange', this._onAccountButtonCheckChange, this);

        this.getChooseAccountButton().on('exitclick', this._onAccountButtonExitClick, this);
    },

// -------- listeners

    /**
     * Called before the recentTweets'store loads. Will stop the tweetPoller's
     * task.
     *
     * @param {Ext.data.Store} store
     * @param {Object} options
     */
    _onRecentTweetBeforeLoad : function(store, options)
    {
        this.tweetPoller.stopPolling();
    },

    /**
     * Called when the tweet poller finished loading and no additional records
     * have been added to the tweetPoller's updateStore (which should be the store
     * of the current recentTweets).
     * This implementation will force the recentTweets to update its meta information,
     * e.g. informations which can be calculated and displayed using the current set
     * of records in the recentTweets store, such as a posted timestamp.
     *
     * @param {com.conjoon.service.twitter.data.TweetPoller} tweetPoller
     *
     * @protected
     */
    _onTweetPollerUpdateEmpty : function(tweetPoller)
    {
        this.recentTweets.updateMetaInfo();
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

        this.tweetPoller.startPolling(
            options.params.id,
            rec.get('updateInterval')
        );
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
            url    : '/service/twitter/send.update/format/json',
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

        var encResponse = insp.isSuccess(response);

        if (!insp.isSuccess(response)) {
            this._onFavoriteTweetFailure(response, options);
            return;
        }

        Ext.fly(options.item).removeClass('pending');

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
        Ext.fly(options.item).removeClass('pending');
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

        var encResponse = insp.isSuccess(response);
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

        var encResponse = insp.isSuccess(response);
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
                this.showUserInfo(this.recentTweets.getSelectedRecords()[0]);
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
            item   : item,
            url    : '/service/twitter/favorite.tweet/format/json',
            params : {
                accountId : this._currentAccountId,
                tweetId   : selRec.id,
                favorite  : favorite
            },
            success : this._onFavoriteTweetSuccess,
            failure : this._onFavoriteTweetFailure,
            scope   : this
        });
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
                        url    : '/service/twitter/delete.tweet/format/json',
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
        this.getShowRecentTweetsButton().toggle(true);

        this.getShowInputButton().setDisabled(false);
        this.getShowRecentTweetsButton().setDisabled(false);
        this.getShowFriendsButton().setDisabled(false);

        this.getChooseAccountButton().getExitMenuItem().setDisabled(false);

        if (!record) {
            return;
        }

        this._currentAccountId = record.get('id');

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
     *
     * @param {com.conjoon.service.twitter.data.TwitterUserRecord|
     * com.conjoon.service.twitter.data.TweetRecord|String} record or the screenName
     * of the user to fetch the information for
     */
    showUserInfo : function(tweetRecord)
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
            id : this._currentAccountId,
        };

        if ((typeof tweetRecord) != 'string') {
            this.userInfoBox.loadUser(tweetRecord);
            Ext.apply(params, {
                userId : tweetRecord.get('userId')
                       ? tweetRecord.get('userId')
                       : tweetRecord.get('id'),
            });
        } else {
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
                this.getHomePanel(),
                this.getShowRecentTweetsButton(),
                this.getShowFriendsButton(),
                '->',
                this.getShowInputButton(),
                '-',
                this.getChooseAccountButton()
            ]
        });
    }

});