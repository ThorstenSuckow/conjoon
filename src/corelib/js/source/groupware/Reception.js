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

Ext.namespace('com.conjoon.groupware');

/**
 * @class com.conjoon.groupware.Reception
 *
 * The reception takes care of login/logout/lock/unlock processes.
 *
 * @singleton
 */
com.conjoon.groupware.Reception = function() {

    /**
     * @param {Object} _options
     */
    var _options = {
        loginWindowClass : com.conjoon.groupware.reception.LoginWindow
    };


    /**
     * @param {Object}
     */
    var _user = null;

    /**
     * @param {Boolean}
     */
    var _applicationStarted = false;

    /**
     * @param {Object}
     */
    var _pingTask = null;

    /**
     * The recepion allows for one time listeners, that means, once the event
     * got fired, all listeners will be truncated.
     *
     * @param {Object}
     */
    var _listeners = [];

    /**
     * Listeners for a successfull response for a user load.
     *
     * @param {Object}
     */
    var _userLoadListeners = [];

    /**
     * Listeners for a load failure of a user.
     *
     * @param {Object}
     */
    var _userLoadFailureListeners = [];

    /**
     * Listeners get called before the user gets loaded
     *
     * @param {Object}
     */
    var _userBeforeLoadListeners = [];

    /**
     * The login window that will be shown to request user credentials
     * for logging into the application.
     *
     * @param {com.conjoon.groupware.reception.LoginWindow}
     */
    var loginWindow = null;

    /**
     * The logout window that will be shown when a user wants to either lock
     * the workbench or completely logout of the application.
     *
     * @param {com.conjoon.groupware.reception.LogoutWindow}
     */
    var _logoutWindow = null;

    /**
     * Callback for the receptions user load response.
     *
     * @param {XmlHttpResponse}
     * @param {Object} options
     *
     */
    var _onUserLoad = function(response, options)
    {
        var inspector = com.conjoon.groupware.ResponseInspector;

        var data = inspector.isSuccess(response);
        if (data === null) {
            return inspector.handleFailure(response, options);
        }

        _user = data.user;

        for (var i = 0, len = _userLoadListeners.length; i < len; i++) {
            _userLoadListeners[i]['fn'].call(_userLoadListeners[i]['scope'], _user);
        }
    };

    /**
     * Callback for the receptions user load failure.
     *
     * @param {XmlHttpResponse}
     * @param {Object} options
     *
     */
    var _onUserLoadFailure = function(response, options)
    {
        for (var i = 0, len = _userLoadFailureListeners.length; i < len; i++) {
            _userLoadFailureListeners[i]['fn'].call(_userLoadFailureListeners[i]['scope']);
        }
    };

    /**
     * Callback before the user gets loaded.
     *
     */
    var _onBeforeUserLoad = function()
    {
        for (var i = 0, len = _userBeforeLoadListeners.length; i < len; i++) {
            _userBeforeLoadListeners[i]['fn'].call(_userBeforeLoadListeners[i]['scope'], _user);
        }
    };

    /**
     * Callback for the login window's loginsuccess event.
     *
     * @param {Ext.form.BasicForm}
     * @param {Ext.form.Action}
     *
     */
    var _onLoginSuccess = function(basicForm, formAction)
    {
        for (var i = 0, len = _listeners.length; i < len; i++) {
            _listeners[i]['fn'].call(_listeners[i]['scope']);
        }

        _listeners = [];

        var context = _context;
        loginWindow.close();

        if (context == this.TYPE_LOGIN || !_applicationStarted) {
            (function(){
                this.location.href = './';
            }).defer(10, window);
        }
    };

    /**
     * Callback for the login window's loginfailure event.
     *
     * @param {Ext.form.BasicForm}
     * @param {Ext.form.Action}
     *
     */
    var _onLoginFailure = function(basicForm, formAction)
    {
        loginWindow.setControlsDisabled(false);
    };

    /**
     * Callback for the login window's beforelogin event.
     *
     * @param {com.conjoon.groupware.reception.LoginWindow} loginWindow
     * @param {String} username
     * @param {String} password
     *
     */
    var _onBeforeLogin = function(loginWindow, username, password)
    {
        loginWindow.setControlsDisabled(true);
    };

    /**
     * Invoked when either the "Yes" or the "No" button of the logout dialog
     * is clicked.
     *
     * Sends a request to the server that tells the backend that the user wishes
     * to logout.
     *
     * @param {String} buttonType The type of the button that was clicked. Can be
     * either 'yes' or 'no'.
     *
     * @see #_onLogoutSuccess
     */
    var _logout = function(buttonType)
    {
        if (buttonType == 'yes') {
            Ext.Ajax.request({
                url            : './default/reception/logout/format/json',
                success        : _onLogoutSuccess,
                failure        : _onLogoutFailure,
                disableCaching : true
            });
        }
    };

    /**
     * Listener for the login window's exit button.
     */
    var _onExit = function()
    {
        _logout('yes');
    };

    /**
     * Invoked when either the "Yes" or the "No" button of the logout dialog
     * is clicked.
     *
     * Tries to reload the application.
     *
     * @param {String} buttonType The type of the button that was clicked. Can be
     * either 'yes' or 'no'.
     */
    var _restart = function(buttonType)
    {
        if (buttonType == 'yes') {
            window.location.replace('./');
        }
    };

    /**
     * Listener for a successfull logout-request invoked by _logout.
     *
     * @param {Object} response The response object returned by the server.
     * @param {Object} options The options used to initiate the request.
     *
     */
    var _onLogoutSuccess = function(response, options)
    {
        window.onbeforeunload = Ext.emptyFn;

        var json = com.conjoon.util.Json;

        if (json.isError(response.responseText)) {
            _onLogoutFailure(response, options);
            return;
        }

        window.location.href = './';
    };

    /**
     * Listener for a erroneous logout-request invoked by _logout.
     *
     * @param {Object} response The response object returned by the server.
     * @param {Object} options The options used to initiate the request.
     *
     */
    var _onLogoutFailure = function(response, options)
    {
        var json = com.conjoon.util.Json;
        var msg  = Ext.MessageBox;

        var error = json.forceErrorDecode(response);

        msg.show({
            title   : error.title || com.conjoon.Gettext.gettext("Error"),
            msg     : error.message,
            buttons : msg.OK,
            icon    : msg[error.level.toUpperCase()],
            cls     :'com-conjoon-msgbox-'+error.level,
            width   : 400
        });
    };

    /**
     * Builds up the login window for authentication in an ongoing session.
     */
    var _login = function()
    {
        _buildLoginWindow({
            loginUrl : './default/reception/process/format/json',
            modal    : _applicationStarted
        });
        loginWindow.setFormIntroText(
            com.conjoon.Gettext.gettext("Please sign in with your username and your password. Press &quot;Login&quot; when ready.")
        );
    };

    /**
     * Builds up the login window for authentication when the app was started
     * but the user's session got lost.
     *
     */
    var _authenticate = function()
    {
        _buildLoginWindow({
            loginUrl      : './default/reception/process/format/json',
            usernameValue : _user.userName,
            modal         : _applicationStarted,
            draggable     : true
        });
        loginWindow.setFormIntroText(
            com.conjoon.Gettext.gettext("Your request could not be processed. Most likely did your session expire. Please sign in again and retry your last action.")
        );
    };

    /**
     * Builds up the login window for unlocking the workbench.
     *
     */
    var _unlock = function()
    {
        _startPing();

        _buildLoginWindow({
            loginUrl      : './default/reception/unlock/format/json',
            usernameValue : _user.userName,
            showExit      : !_applicationStarted,
            modal         : _applicationStarted,
            draggable     : true
        });

        var msg = null;
        if (_applicationStarted) {
            msg = com.conjoon.Gettext.gettext("The workbench has been locked. Please sign in again to unlock the workbench.");
        } else {
            msg = com.conjoon.Gettext.gettext("The workbench has been locked. Please sign in again to unlock the workbench. Press the &quot;exit&quot;-button to log the previous user out and to login with a new account.");
        }
        loginWindow.setFormIntroText(msg);
    };

    /**
     * Listener for the login window's destroy event.
     * Will reset some variables and stop the task for pinging the server
     * in a frequent interval which keeps the user's session alive.
     */
    var _onDestroy = function()
    {
        if (_context === this.TYPE_UNLOCK) {
            _stopPing();
        }
        loginWindow = null;
        _context    = null;

    };

    /**
     * Calls the ping action from the reception's controller.
     */
    var _pingServer = function()
    {
        Ext.Ajax.request({
            url            : './default/reception/ping/format/json',
            disableCaching : true,
            failure        : com.conjoon.groupware.ResponseInspector.handleFailure
        });
    };

    /**
     * Starts pinging the server in a frequent interval to keep the user's session
     * alive.
     */
    var _startPing = function()
    {
        if (_pingTask !== null) {
            return;
        }
        _pingTask = {
            run      : _pingServer,
            interval : 60000 // 1 minute
        }
        Ext.TaskMgr.start(_pingTask);
    };

    /**
     * Stops pinging the server.
     */
    var _stopPing = function()
    {
        if (_pingTask === null) {
            return;
        }
        Ext.TaskMgr.stop(_pingTask);
        _pingTask = null;
    };

    /**
     * Method for building the login window.
     *
     * @param {Object} config Additional config properties for the login window
     */
    var _buildLoginWindow = function(config)
    {
        if (loginWindow === null) {
            var options = {
                loginUrl      : './default/reception/process/format/json',
                softwareLabel : com.conjoon.groupware.Registry.get('/base/conjoon/name'),
                editionLabel  : com.conjoon.groupware.Registry.get('/base/conjoon/edition'),
                versionLabel  : com.conjoon.groupware.Registry.get('/base/conjoon/version'),
                draggable     : false
            };

            var config = config || {};

            Ext.apply(options, config);

            loginWindow = new _options['loginWindowClass'](options);
            loginWindow.on('exit',         _onExit,          com.conjoon.groupware.Reception);
            loginWindow.on('loginsuccess', _onLoginSuccess, com.conjoon.groupware.Reception);
            loginWindow.on('loginfailure', _onLoginFailure, com.conjoon.groupware.Reception);
            loginWindow.on('beforelogin',  _onBeforeLogin, com.conjoon.groupware.Reception);
            loginWindow.on('destroy',      _onDestroy,     com.conjoon.groupware.Reception);
        }

        if (!loginWindow.isVisible()) {
            loginWindow.show();
        }
    };

    /**
     * The listener for the {@see Ext.ux.util.MessageBus}' message
     * 'ext.lib.ajax.authorizationRequired'.
     * Will inspect the message and check which kind of autentication failure
     * is present: This can be either one of
     * com.conjoon.groupware.ResponseInspector.FAILURE_AUTH or
     * com.conjoon.groupware.ResponseInspector.FAILURE_LOCK
     * Either
     *
     * @throws Error if neither FAILURE_AUTH or FAILURE_LOCK is present in the
     * response; throws an error if the current context is AUTHENTICATE but a
     * locked failure was returned
     */
    var _handleAuthFailure = function(subject, message)
    {
        var rawResponse = message.rawResponse;

        var inspector = com.conjoon.groupware.ResponseInspector;
        var ft = inspector.getFailureType(rawResponse);

        switch (ft) {
            case inspector.FAILURE_AUTH:
                if (_context === this.TYPE_UNLOCK) {
                    // session got lost somewhere
                    loginWindow.close();
                }

                this.showLogin(this.TYPE_AUTHENTICATE);
            break;

            case inspector.FAILURE_LOCK:
                if (_context === this.TYPE_AUTHENTICATE) {
                    // session got lost somewhere
                    throw(
                       'com.conjoon.groupware.Reception._handleAuthFailure: '
                       +'Current context is AUTHENTICATE but server returned LOCKED'
                    );
                }

                this.lockWorkbench();
            break;

            default:
                throw(
                    'com.conjoon.groupware.Reception._handleAuthFailure: '
                    + 'Response did contain neither FAILURE_AUTH nor FAILURE_LOCK'
                );
            break;
        }


    };

    /**
     * Shows the login dialog to unlock the workbench
     *
     */
    var _lockWorkbench = function()
    {
        this.showLogin(this.TYPE_UNLOCK);
    };

    /**
     * @param {Number}
     * The current context this component is in. Equals to null if there is no
     * context, otherwise it can equal to either TYPE_LOGIN, TYPE_AUTHENTICATE
     * or TYPE_UNLOCK.
     */
    var _context = null;

    return {


        /**
         * @param {Number}
         * The type of context the login procedure is in. States that the app
         * is not started yet and that the login window is shown alone.
         */
        TYPE_LOGIN : 2,

        /**
         * @param {Number}
         * The type of context the login procedure is in. States that the app
         * was started but the session got somehow lost, thus a re-login is
         * needed.
         */
        TYPE_AUTHENTICATE : 4,

        /**
         * @param {Number}
         * The type of context the login procedure is in. States that the app
         * was started and the user lcoked the workbench.
         */
        TYPE_UNLOCK : 8,

        /**
         *
         */
        onLogin : function(fn, scope)
        {
            _listeners.push({
                fn    : fn,
                scope : scope || window
            });
        },

        /**
         *
         */
        onBeforeUserLoad : function(fn, scope)
        {
            _userBeforeLoadListeners.push({
                fn    : fn,
                scope : scope || window
            });
        },

        /**
         *
         */
        onUserLoad : function(fn, scope)
        {
            _userLoadListeners.push({
                fn    : fn,
                scope : scope || window
            });
        },

        /**
         *
         */
        onUserLoadFailure : function(fn, scope)
        {
            _userLoadFailureListeners.push({
                fn    : fn,
                scope : scope || window
            });
        },

        /**
         *
         */
        init : function(applicationStarted, options)
        {
            if (applicationStarted !== false) {
                window.onbeforeunload = this.confirmApplicationLeave();
            }

            _applicationStarted = applicationStarted;

            Ext.apply(_options, options || {});

            _onBeforeUserLoad();

            Ext.Ajax.request({
                url            : './default/reception/get.user/format/json',
                disableCaching : true,
                success        : _onUserLoad,
                failure        : _onUserLoadFailure
            });

            // subscribe to the message bus and listen to failed responses
            // due to "401 - authorization required" status codes
            Ext.ux.util.MessageBus.subscribe(
                'ext.lib.ajax.authorizationRequired',
                _handleAuthFailure,
                this
            );
            return this;
        },

        /**
         *
         * @return {Object}
         */
        getUser : function()
        {
            return _user;
        },

        /**
         * Sends a request to lock the workbench and shows the login dialog on
         * success.
         *
         * @see _lockWorkbench
         *
         * @throws Error if the current context is neither null nor TYPE_UNLOCK
         */
        lockWorkbench : function()
        {
            if (_context === this.TYPE_UNLOCK) {
                return;
            } else if (_context !== null) {
                throw(
                    'com.conjoon.groupware.Reception.lockWorkbench: '
                    +'Current context is AUTHENTICATE but server returned LOCKED'
                );
            }

            Ext.Ajax.request({
                url            : './default/reception/lock/format/json',
                disableCaching : true,
                success        : _lockWorkbench,
                failure        : com.conjoon.groupware.ResponseInspector.handleFailure,
                scope          : this
            });
        },

        /**
         * Tries to do a hard reload of the app.
         * Shows a confirmation screen before.
         *
         * @see _restart
         */
        restart : function()
        {
            var msg = Ext.MessageBox;

            com.conjoon.SystemMessageManager.show({
                title   : com.conjoon.Gettext.gettext("Restart"),
                msg     : com.conjoon.Gettext.gettext("All unsaved data will be lost. Are you sure you want to restart?"),
                buttons : msg.YESNO,
                icon    : msg.QUESTION,
                cls     : 'com-conjoon-msgbox-question',
                width   : 400,
                fn      : _restart
            });
        },

        /**
         * Shows a confirmation screen before a user wants to log out.
         * The calback will either init the logout procedure or void.
         *
         * @see _logout
         */
        logout : function()
        {
            var msg = Ext.MessageBox;

            com.conjoon.SystemMessageManager.show({
                title   : com.conjoon.Gettext.gettext("Logout"),
                msg     : com.conjoon.Gettext.gettext("All unsaved data will be lost. Are you sure you want to sign out and exit?"),
                buttons : msg.YESNO,
                icon    : msg.QUESTION,
                cls     : 'com-conjoon-msgbox-question',
                width   : 400,
                fn      : _logout
            });
        },

        /**
         * Display the logout window.
         */
        showLogout : function()
        {
            if (_logoutWindow === null) {
                _logoutWindow = new com.conjoon.groupware.reception.LogoutWindow();
                _logoutWindow.on('destroy', function() {
                    _logoutWindow = null;
                });
            }

            _logoutWindow.show();
        },

        /**
         * Displays the login window.
         * Checks if the recpetion is in a given context. If the context is anything
         * but null, the method will do nothing and return.
         * The context is stored in the _context property and set when the method
         * is called, and unset in the loginsuccessfull-listener.
         *
         */
        showLogin : function(contextType)
        {
            if (_context !== null){
                return;
            }

            switch (contextType) {
                case this.TYPE_LOGIN:
                    _context = contextType;
                    _login();
                break;
                case this.TYPE_AUTHENTICATE:
                    _context = contextType;
                    _authenticate();
                break;
                case this.TYPE_UNLOCK:
                    _context = contextType;
                    _unlock();
                break;
                default:
                    throw(
                       'com.conjoon.groupware.Reception: '
                       + 'no valid login context provided.'
                    );
                break;
            }
        },

        /**
         * Returns true if the workbench is currently locked.
         * The workbench can be locked if the user needs to authenticate
         * or if the user has locked the workbench.
         *
         * @return {Boolean}
         */
        isLocked : function()
        {
            return (_context === this.TYPE_UNLOCK
                    || _context === this.TYPE_AUTHENTICATE);
        },

        /**
         * Removes all userload-related listeners from the reception.
         */
        removeAllListeners : function()
        {
            _userLoadListeners        = [];
            _userLoadFailureListeners = [];
            _userBeforeLoadListeners  = [];
        },

        /**
         *
         * @return {Function}
         */
        confirmApplicationLeave : function()
        {
            return function (evt) {
                var message = com.conjoon.Gettext.gettext("conjoon\nAre you sure you want to exit your current session?");
                if (typeof evt == "undefined") {
                    evt = window.event;
                }
                if (evt) {
                  evt.returnValue = message;
                }
                return message;
            };
        }


    };

}();