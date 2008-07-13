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
 * The reception takes care of login/logout processes. 
 *
 * @singleton
 */  
de.intrabuild.groupware.Reception = function() {
   
   
    /**
     * The login window that will be shown to request user credentials
     * for logging into the application.
     *
     * @param {de.intrabuild.groupware.reception.LoginWindow}
     */
    var loginWindow = null;
   
    /**
     * Callback for the login window's loginsuccess event.
     *
     * @param {Ext.form.BasicForm}
     * @param {Ext.form.Action}
     *
     */
    var _onLoginSuccess = function(basicForm, formAction)
    {
        window.location.href = '/';
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
	
    return {

        /**
         *  
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
         * Displays the login window.
         *
         */    
        showLogin : function()
        {
            if (loginWindow === null) {
                loginWindow = new de.intrabuild.groupware.reception.LoginWindow({
                    loginUrl      : 'default/login/process/format/json',
					softwareLabel : 'intraBuild 2.0',   
					editionLabel  : 'alpha dev branch',
					versionLabel  : 'V0.1a'
                });
                loginWindow.on('loginsuccess', _onLoginSuccess, de.intrabuild.groupware.Reception);
                loginWindow.on('loginfailure', _onLoginFailure, de.intrabuild.groupware.Reception);
                loginWindow.on('beforelogin',  _onBeforeLogin, de.intrabuild.groupware.Reception);
            }
            
            loginWindow.show();
        }    
        
        
    };  
    
}();  