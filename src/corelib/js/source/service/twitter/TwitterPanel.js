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
            border : false,
            bbar   : new Ext.Toolbar({
                items : [
                    this.getHomePanel(),
                    this.getShowRecentTweetsButton(),
                    this.getShowFriendsButton(),
                    '->',
                    this.getShowInputButton(),
                    '-',
                    this.getChooseAccountButton()
                ]
            })
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

        this.inputBox.getUpdateButton().on('click', this._onUpdateButtonClick, this);

        this.getChooseAccountButton().on('checkchange', this._onAccountButtonCheckChange, this);

        this.getChooseAccountButton().on('exitclick', this._onAccountButtonExitClick, this);
    },

// -------- listeners

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

        Ext.Ajax.request({
            url    : '/service/twitter/send.update/format/json',
            params : {
                message   : v,
                accountId : this._currentAccountId
            },
            success : this._onUpdateSuccess,
            failure : this._onUpdateFailure,
            scope   : this
        });
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
        }
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
     * Depending on the target, the event will be delegated to _handleReplyClick
     * if a click on the reply link/icon was detected.
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

        if (this.inputBox.rendered) {
            this.inputBox.setMessage("");
        }

        // remove all data from the stores
        this.recentTweets.store.removeAll();
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
        var recipient = '@'+this.recentTweets.getSelectedRecords()[0].get('screenName');

        if (!this._showInputButton.pressed) {
            this._showInputButton.toggle(true);
        }

        this.inputBox.focus();

        if (v.indexOf(recipient) != -1) {
            return;
        }

        v = recipient+' '+v;
        this.inputBox.setMessage(v);
    },

    /**
     * Loads the data for the specified account into this panel and
     * its components.
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
     *  com.conjoon.service.twitter.data.TweetRecord} record
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

        this.userInfoBox.loadUser(tweetRecord);
        this.usersRecentTweets.store.load({
            params : {
                id     : this._currentAccountId,
                userId : tweetRecord.get('userId')
                       ? tweetRecord.get('userId')
                      :  tweetRecord.get('id')
            }
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
    }

});