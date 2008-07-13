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
 
Ext.namespace('de.intrabuild.groupware.reception');

/**
 * @class de.intrabuild.groupware.reception.LoginWindow
 * @extends Ext.Window
 * A window holding a username and password field for submitting password credentials.
 * @constructor
 * @param {Object} config The configuration options.
 */
de.intrabuild.groupware.reception.LoginWindow = Ext.extend(Ext.Window, {

    /**
     * @cfg {String} loginUrl
     * The url for the form to submit the login data to.
     */

    /**
     * @cfg {String} softwareLabel The label to render in the upper left of 
     * the window
     */
    softwareLabel : 'intraBuild 2.0', 

    /**
     * @cfg {String} editionLabel The label to render under the software label
     * in the upper left of the window
     */
    editionLabel : 'mRAM edition', 

    /**
     * @cfg {String} versionLabel The label to render in the lower right of the
     * window
     */
    versionLabel : 'V0.1a &quot;Amelie&quot;', 

    /**
     * @param {Ext.Button} _loginButton The loginbutton attached to the window's 
     * footer
     * @private
     */
    _loginButton : null, 

    /**
     * @param {Ext.form.FormPanel} The form panel that holds both the username 
     * and the password field.
     * @private
     */
    _formPanel : null,

    /**
     * @param {Ext.form.TextField} The textfield for the username.
     * @private
     */
    _usernameField : null,
    
    /**
     * @param {de.intrabuild.groupware.reception.StateIndicator} _stateIndicator
     * A special component that's used for showing the current state of the login process
     * and displaying error messages of the login.
     * @private
     */
    _stateIndicator : null, 

    /**
     * @param {Ext.form.TextField} The textfield for the password.
     * Subclasses are advised to use a textfield that's capable of masking
     * the input string.
     * @private
     */
    _passwordField : null,


    initComponent : function()
    {
        this._stateIndicator = this._createStateIndicator();
        this._loginButton    = this._createLoginButton();
        
        this._usernameField = this._createUsernameField();
        this._passwordField = this._createPasswordField();
        
        this._formPanel = this._createFormPanel(
            this._usernameField, 
            this._passwordField
        );
        
        Ext.apply(this, {
            cls       : 'de-intrabuild-groupware-reception-LoginWindow',
            closable  : false,
            resizable : false,
            width     : 490,
            height    : 335,
            //layout    : 'fit',
            items     : [{
                xtype  :'box', 
                cls    : 'de-intrabuild-groupware-reception-LoginWindow-softwareLabel',
                autoEl : {
                    tag  : 'div', 
                    html : this.softwareLabel
            }}, {
                xtype  :'box',
                cls    : 'de-intrabuild-groupware-reception-LoginWindow-editionLabel', 
                autoEl : {
                    tag  : 'div', 
                    html : this.editionLabel
            }},
                this._formPanel, 
                this._stateIndicator, {
                xtype  :'box',
                cls    : 'de-intrabuild-groupware-reception-LoginWindow-versionLabel', 
                autoEl : {
                    tag  : 'div', 
                    html : this.versionLabel
            }}
            ],
            buttons   : [
                this._loginButton
            ]
        });
        
        this.addEvents(
            /**
             * @event beforelogin
             * Gets fired before the form data is send to the server.
             * Listeners should return false to cancel the form submit.
             *
             * @param {de.intrabuild.goupware.reception.LoginWindow}
             * @param {String} username
             * @param {String} password
             */        
            'beforelogin',
            /**
             * @event loginsuccess
             * Gets fired when the request for a login succeeded. 
             * The event does not indicate that a login attempt was valid.
             *
             * @param {de.intrabuild.goupware.reception.LoginWindow}
             * @param {Ext.form.BasicForm}
             * @param {Ext.from.Action}
             */        
            'loginsuccess',
            /**
             * @event loginfailure
             * Gets fired when a request for the login did not succed, most likely
             * because of a server error.
             *
             * @param {de.intrabuild.goupware.reception.LoginWindow}
             * @param {Ext.form.BasicForm}
             * @param {Ext.from.Action}
             */        
            'loginfailure'
        );
            
        de.intrabuild.groupware.reception.LoginWindow.superclass.initComponent.call(this);
    },

    /**
     * Overrides parent implementation and adds additional event listener to 
     * this component.
     *
     */ 
    initEvents : function() 
    {
        de.intrabuild.groupware.reception.LoginWindow.superclass.initEvents.call(this);
        this.on('show' , function() {
			try {
				this._usernameField.focus('', 10);
            } catch (e) {
				// ignore
			}
        }, this);
        this.on('beforelogin' , this._onBeforeLogin, this);
    },

// -------- listener
        
    /**
     * Listener for the form panel's clientvalidation event.
     * Gets called in a frequent interval defined by the formPanel this listener
     * is defined for.
     * Validates if and only if all input elements were validated successfully.
     * If all data of the input elements was valid, the login button gets 
     * rendered as enabled, otherwise as disabled.
     *
     * @param {Ext.form.FormPanel} The form panel that triggered the event.
     * @param {Boolean} isValid "true", if the user input was valid, otherwise
     * "false"
     *
     */
	_onClientValidation : function(formPanel, isValid)
	{
		if (!isValid) {
			this._loginButton.setDisabled(true);	
		} else {
			this._loginButton.setDisabled(false);	
		}
	},

    /**
     * Listener for the beforelogin event.
     *
     * @param {de.intrabuild.goupware.reception.LoginWindow}
     * @param {String} username
     * @param {String} password
     *
     * @protected
     */
    _onBeforeLogin : function(basicForm, action)
    {
        this._stateIndicator.setMessage("Please wait, trying to login...", "loading");
    },

    /**
     * Callback for a successfull login attempt
     *
     * @param {Ext.form.BasicForm}
     * @param {Ext.form.Action}
     *
     * @protected
     */
    _onLoginSuccess : function(basicForm, action)
    {
        this._stateIndicator.setMessage("Login valid!", 'ok');    
        this.fireEvent('loginsuccess', this, basicForm, action);    
    },

    /**
     * Callback for a erroneous login attempt.
     * This callback is invoked whenever the request to the login-url failed.
     *
     * @param {Ext.form.BasicForm}
     * @param {Ext.form.Action}
     *
     * @protected
     */
    _onLoginFailure : function(basicForm, action)
    {
		if (action) {
            if (action.failureType == Ext.form.Action.CONNECT_FAILURE) {
                this._stateIndicator.setErrorMessage(
				    "Unexpected error: Connection to server lost."
			     );
            } else if (action.result && action.result.error) {
                this._stateIndicator.setErrorMessage(action.result.error);
            } else {
				this._stateIndicator.setErrorMessage(
				    "Unexpected error: An unknown error occured"
				);
			}
		}
		
		this._passwordField.setValue('');
		
		try {
            this._passwordField.focus('', 10);
        } catch (e) {
            // ignore
        }
		
        this.fireEvent('loginfailure', this, basicForm, action);
    },
    
    /**
     * Listener for the login button's click event.
     *
     * @param {Ext.Button} button The button that triggered the event
     */
    _onLoginButtonClick : function(button)
    {
        this._doSubmit();
    }, 
    
    /**
     * Default-listener for special key events, like "escape" or "return".
     *
     * The method will submit the form if, and only if the triggered event
     * represents a "return"-keystroke and if, and only if both the password and
     * the username fields are none-empty.
     *
     * @param {Ext.form.TextField} The textfield that triggered the event
     * @param {Ext.EventObject} The object with details about the triggered event
     *
     */
    _onSpecialKey : function(textField, eventObject)
    {
        var keyCode = eventObject.getKey();
        var eO      = Ext.EventObject;
        
        if (keyCode != eO.ENTER && keyCode != eO.RETURN) {
            return;    
        }
         
        if (this._usernameField.getValue().trim() == "" 
            || this._passwordField.getValue().trim() == "") {
            return;
        } 
        
        this._doSubmit();        
    },
    
// -------- helper
    /**
     * Returns a {de.intrabuild.groupware.reception.StateIndicator} for showing the 
     * current login process and possible error messages that occured during login 
     * attempts.
     *
     * @return {de.intrabuild.groupware.reception.StateIndicator}
     */
    _createStateIndicator : function()
    {
        return new de.intrabuild.groupware.reception.StateIndicator();
    },
     
    /**
     * Either disables or enables the controls of this window .
     * This will also stop/start automatically monitoring the valid state of the
     * text input elements.
     *
     * @param {Boolean} disable true for disabling the control elements,
     * otherwise false
     */
    setControlsDisabled : function(disable)
    {
        if (disable) {
            this._formPanel.stopMonitoring();    
        } else {
            this._formPanel.startMonitoring();        
        }
        
        this._loginButton.setDisabled(disable);
        this._usernameField.setDisabled(disable);
        this._passwordField.setDisabled(disable);
    },
     
    /**
     * Submits this form.
     *
     * @todo send password md5 hashed?
     *
     * @protected
     */
    _doSubmit : function()
    {
        if (this.fireEvent(
                'beforelogin', 
                this, 
                this._usernameField.getValue(),
                this._passwordField.getValue()) === false) {
            return;            
        }
        
        this._formPanel.form.submit({
            params  : {
                username : this._usernameField.getValue(),
                password : this._passwordField.getValue()
            },
            success : this._onLoginSuccess,
            failure : this._onLoginFailure,
            scope   : this    
        });    
    }, 

    /**
     * Creates this window's login button.
     * The button will be shown in the footer of this window.
     *
     * @return {Ext.Button}
     *
     * @protected
     */    
    _createLoginButton : function()
    {
        var lbutton = new Ext.Button({
            text     : "Login",
            minWidth : 75,
            disabled : true,
            handler  : this._onLoginButtonClick,
            scope    : this   
        });
        
        return lbutton;
    },
    
    /**
     * Creates the form field for entering the username.
     *
     * @return {Ext.form.TextField}
     *
     * @protected
     */
    _createUsernameField : function()
    {
        return new Ext.form.TextField({
            fieldLabel  : "Username",
            name        : 'username',
            preventMark : true,
            allowBlank  : false,
            listeners   : {
                specialkey  : {
                    fn    : this._onSpecialKey,
                    scope : this        
                }     
            }    
        });    
    },    

    /**
     * Creates the form field for entering the password.
     *
     * @return {Ext.form.TextField}
     *
     * @protected
     */
    _createPasswordField : function()
    {
        return new Ext.form.TextField({
            inputType   : 'password',
            allowBlank  : false,
            preventMark : true,
            name        : 'password',
            fieldLabel  : "Password",
            listeners   : {
                specialkey  : {
                    fn    : this._onSpecialKey,
                    scope : this        
                }     
            }        
        });    
    },    
    
    /**
     * Creates the form panel that shows both the username and the password field.
     *
     * @param {Ext.form.TextField} usernameField The field for entering the username.
     * @param {Ext.form.TextField} passwordField The field for entering the password.
     *
     * @return {Ext.form.FormPanel}
     *
     * @protected
     */
    _createFormPanel : function(usernameField, passwordField)
    {
        return new Ext.form.FormPanel({
            monitorValid : true,
            url          : this.loginUrl,
            method       : 'post',
            labelAlign   : 'right',
            cls          : 'x-small-editor de-intrabuild-groupware-reception-LoginWindow-formPanel',
            labelWidth   : 75,
            defaults     : {
                anchor     : '100%',
                labelStyle : 'width:75px;font-size:11px;'
            },
            border   : false,
            items    : [
                new de.intrabuild.groupware.util.FormIntro({
            		style   : 'margin:0px 0 10px 0;',
            		label	: "Login",
            		text	: "Please input your username and your password. Press &quot;Login&quot; when ready."	
            	}),
                usernameField,
                passwordField
            ],
            listeners : {
                clientvalidation : {
                    fn    : this._onClientValidation,
                    scope : this    
                }    
            }   
        });    
    }
    
        


});  