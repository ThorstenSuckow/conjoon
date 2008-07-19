/**
 * intrabuild
 * (c) 2002-2008 siteartwork.de/MindPatterns
 * license@siteartwork.de
 *
 * $Author: T. Suckow $
 * $Id: _scripts.phtml 2 2008-06-21 10:38:49Z T. Suckow $
 * $Date: 2008-06-21 12:38:49 +0200 (Sa, 21 Jun 2008) $ 
 * $Revision: 2 $
 * $LastChangedDate: 2008-06-21 12:38:49 +0200 (Sa, 21 Jun 2008) $
 * $LastChangedBy: T. Suckow $
 * $URL: file:///F:/svn_repository/intrabuild/trunk/src/www/application/modules/default/views/scripts/index/_scripts.phtml $ 
 */

Ext.namespace('de.intrabuild.groupware');  
  
/**
 * @class de.intrabuild.groupware.Reception
 * 
 * The reception takes care of login/logout/lock/unlock processes. 
 *
 * @singleton
 */  
de.intrabuild.groupware.Reception = function() {
  
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
     * The login window that will be shown to request user credentials
     * for logging into the application.
     *
     * @param {de.intrabuild.groupware.reception.LoginWindow}
     */
    var loginWindow = null;
   
    /**
     * The logout window that will be shown when a user wants to either lock
     * the workbench or completely logout of the application.
     *
     * @param {de.intrabuild.groupware.reception.LogoutWindow}
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
		var inspector = de.intrabuild.groupware.ResponseInspector;
		
		var data = inspector.isSuccessfull(response);
		if (data === null) {
			return inspector.handleFailure(response, options);
		} 
		
		_user = data.user;
		
		for (var i = 0, len = _userLoadListeners.length; i < len; i++) {
            _userLoadListeners[i]['fn'].call(_userLoadListeners[i]['scope'], _user);
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
				this.location.href = '/';
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
     * @param {de.intrabuild.groupware.reception.LoginWindow} loginWindow
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
	            url            : '/default/reception/logout/format/json',
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
            window.location.replace('/'); 
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
        var json = de.intrabuild.util.Json;
        
        if (json.isError(response.responseText)) {
            _onLogoutFailure(response, options);
            return;
        }     
				
		window.location.href = '/';		  
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
        var json = de.intrabuild.util.Json;
        var msg  = Ext.MessageBox;
        
        var error = json.forceErrorDecode(response);
            
        msg.show({
            title   : error.title || 'Error',
            msg     : error.message,
            buttons : msg.OK,
            icon    : msg[error.level.toUpperCase()],
            cls     :'de-intrabuild-msgbox-'+error.level,
            width   : 400
        });
    };  	
	
	/**
	 * Builds up the login window for authentication in an ongoing session.
	 */
	var _login = function()
	{
        _buildLoginWindow({
			loginUrl : '/default/reception/process/format/json',
			modal    : _applicationStarted
		});
		loginWindow.setFormIntroText(
            "Please input your username and your password. Press &quot;Login&quot; when ready."
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
			loginUrl      : '/default/reception/process/format/json',
			usernameValue : _user.emailAddress,
            modal         : _applicationStarted,
            draggable     : true
		});
        loginWindow.setFormIntroText(
            "Your request could not be processed. Most likely did your session expire. Please log in again and retry your last action."
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
			loginUrl      : '/default/reception/unlock/format/json',
			usernameValue : _user.emailAddress,
			showExit      : !_applicationStarted,
            modal         : _applicationStarted,
			draggable     : true
        });

        var msg = null;
        if (_applicationStarted) {
            msg = "The workbench has been locked. Please log in to unlock the workbench again.";
		} else {
			msg = "The workbench has been locked. Please log in to unlock the workbench again. Press the &quot;exit&quot;-button to log the previous user out and to login with a new account.";
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
            url            : '/default/reception/ping/format/json',
			disableCaching : true,
			failure        : de.intrabuild.groupware.ResponseInspector.handleFailure
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
	            loginUrl      : '/default/reception/process/format/json',
	            softwareLabel : 'intraBuild 2.0',
	            editionLabel  : 'alpha dev branch',
	            versionLabel  : 'V0.1a',
	            draggable     : false
	        };
			
			var config = config || {};
			
			Ext.apply(options, config);
			
			loginWindow = new de.intrabuild.groupware.reception.LoginWindow(options);
			loginWindow.on('exit',         _onExit,          de.intrabuild.groupware.Reception);
			loginWindow.on('loginsuccess', _onLoginSuccess, de.intrabuild.groupware.Reception);
			loginWindow.on('loginfailure', _onLoginFailure, de.intrabuild.groupware.Reception);
			loginWindow.on('beforelogin',  _onBeforeLogin, de.intrabuild.groupware.Reception);
			loginWindow.on('destroy',      _onDestroy,     de.intrabuild.groupware.Reception);
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
	 * de.intrabuild.groupware.ResponseInspector.FAILURE_AUTH or 
	 * de.intrabuild.groupware.ResponseInspector.FAILURE_LOCK
	 * Either
	 * 
	 * @throws Error if neither FAILURE_AUTH or FAILURE_LOCK is present in the 
	 * response; throws an error if the current context is AUTHENTICATE but a 
	 * locked failure was returned
	 */
	var _handleAuthFailure = function(subject, message)
	{
		var rawResponse = message.rawResponse;
		
		var inspector = de.intrabuild.groupware.ResponseInspector;
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
					   'de.intrabuild.groupware.Reception._handleAuthFailure: '
					   +'Current context is AUTHENTICATE but server returned LOCKED'
					);
                }
				
				this.lockWorkbench();
            break;
			
			default:
				throw(
		            'de.intrabuild.groupware.Reception._handleAuthFailure: '
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
		init : function(applicationStarted)
		{
			_applicationStarted = applicationStarted;
			
			Ext.Ajax.request({
				url            : '/default/reception/get.user/format/json',
				disableCaching : true,
				success        : _onUserLoad
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
                    'de.intrabuild.groupware.Reception.lockWorkbench: '
                    +'Current context is AUTHENTICATE but server returned LOCKED'
                );				
			} 
			
			Ext.Ajax.request({
				url            : '/default/reception/lock/format/json',
				disableCaching : true,
				success        : _lockWorkbench,
				failure        : de.intrabuild.groupware.ResponseInspector.handleFailure,
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
            
            msg.show({
                title   : "Restart",
                msg     : "All unsaved data will be lost. Are you sure you want to restart?",
                buttons : msg.YESNO,
                icon    : msg.QUESTION,
                cls     : 'de-intrabuild-msgbox-question',
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
			
	        msg.show({
	            title   : "Logout",
	            msg     : "All unsaved data will be lost. Are you sure you want to log out and exit?",
	            buttons : msg.YESNO,
				icon    : msg.QUESTION,
                cls     : 'de-intrabuild-msgbox-question',
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
                _logoutWindow = new de.intrabuild.groupware.reception.LogoutWindow();
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
					   'de.intrabuild.groupware.Reception: '
					   + 'no valid login context provided.'
					);
			    break;
			}
        }    
        
        
    };  
    
}();  