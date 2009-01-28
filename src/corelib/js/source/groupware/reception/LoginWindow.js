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

Ext.namespace('com.conjoon.groupware.reception');

/**
 * @class com.conjoon.groupware.reception.LoginWindow
 * @extends Ext.Window
 * A window holding a username and password field for submitting password credentials.
 * @constructor
 * @param {Object} config The configuration options.
 */
com.conjoon.groupware.reception.LoginWindow = Ext.extend(Ext.Window, {

    /**
     * @cfg {String} usernameValue
     * If submitted, this value will be always send instead the value provided
     * by the username textfield. Additionally, the username field will be always
     * rendered as disabled and the default value equals to this value.
     */

    /**
     * @cfg {String} defaultFocusField
     * Which field to focus when the dialog gets rendered. Can be either
     * 'username' or 'password'. Defaults to 'username'.
     */
    defaultFocusField : 'username',

    /**
     * @cfg {Boolean} showExit
     * True to display the "exit"-button, otherwise "false"
     */

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
     * @param {com.conjoon.groupware.reception.StateIndicator} _stateIndicator
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

    /**
     * @param {com.conjoon.groupware.util.FormIntro} The form intro for the
     * form panel.
     */
    _formIntro : null,

    initComponent : function()
    {
        var buttonConfig = [];

        this._stateIndicator = this._createStateIndicator();
        this._loginButton    = this._createLoginButton();

        if (this.showExit) {
            this._exitButton = this._createExitButton();
            buttonConfig.push(this._exitButton);
        }

        buttonConfig.push(this._loginButton);

        this._usernameField = this._createUsernameField();

        if (this.usernameValue) {
            this._usernameField.setValue(this.usernameValue);
            this._usernameField.setDisabled(true);
            this.defaultFocusField = 'password';
        }

        this._passwordField = this._createPasswordField();

        this._formIntro = this._createFormIntro();

        this._formPanel = this._createFormPanel(
            this._usernameField,
            this._passwordField,
            this._formIntro
        );

        Ext.applyIf(this, {
            height : 335
        });

        Ext.apply(this, {
            cls       : 'com-conjoon-groupware-reception-LoginWindow',
            closable  : false,
            resizable : false,
            width     : 490,
            items     : [{
                xtype  :'box',
                cls    : 'com-conjoon-groupware-reception-LoginWindow-softwareLabel',
                autoEl : {
                    tag  : 'div',
                    html : this.softwareLabel
            }}, {
                xtype  :'box',
                cls    : 'com-conjoon-groupware-reception-LoginWindow-editionLabel',
                autoEl : {
                    tag  : 'div',
                    html : this.editionLabel
            }},
                this._formPanel,
                this._stateIndicator, {
                xtype  :'box',
                cls    : 'com-conjoon-groupware-reception-LoginWindow-versionLabel',
                autoEl : {
                    tag  : 'div',
                    html : this.versionLabel
            }}
            ],
            buttons   : buttonConfig
        });

        this.addEvents(
            /**
             * @event exit
             * gets fired when the exit-button was clicked
             */
            'exit',
            /**
             * @event beforelogin
             * Gets fired before the form data is send to the server.
             * Listeners should return false to cancel the form submit.
             *
             * @param {com.conjoon.goupware.reception.LoginWindow}
             * @param {String} username
             * @param {String} password
             */
            'beforelogin',
            /**
             * @event loginsuccess
             * Gets fired when the request for a login succeeded.
             * The event does not indicate that a login attempt was valid.
             *
             * @param {com.conjoon.goupware.reception.LoginWindow}
             * @param {Ext.form.BasicForm}
             * @param {Ext.from.Action}
             */
            'loginsuccess',
            /**
             * @event loginfailure
             * Gets fired when a request for the login did not succed, most likely
             * because of a server error.
             *
             * @param {com.conjoon.goupware.reception.LoginWindow}
             * @param {Ext.form.BasicForm}
             * @param {Ext.from.Action}
             */
            'loginfailure'
        );

        com.conjoon.groupware.reception.LoginWindow.superclass.initComponent.call(this);
    },

    /**
     * Overrides parent implementation and adds additional event listener to
     * this component.
     *
     */
    initEvents : function()
    {
        com.conjoon.groupware.reception.LoginWindow.superclass.initEvents.call(this);
        this.on('show' , function() {
            try {
                if (this.defaultFocusField == 'password') {
                    this._passwordField.focus('', 10);
                } else {
                    this._usernameField.focus('', 10);
                }
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
     * @param {com.conjoon.goupware.reception.LoginWindow}
     * @param {String} username
     * @param {String} password
     *
     * @protected
     */
    _onBeforeLogin : function(basicForm, action)
    {
        this._stateIndicator.setMessage(com.conjoon.Gettext.gettext("Please wait, trying to login..."), "loading");
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
        this._stateIndicator.setMessage(com.conjoon.Gettext.gettext("Login valid!"), 'ok');
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
                    com.conjoon.Gettext.gettext("Unexpected error: Connection to server lost.")
                 );
            } else if (action.result && action.result.error) {
                this._stateIndicator.setErrorMessage(action.result.error);
            } else {
                this._stateIndicator.setErrorMessage(
                    com.conjoon.Gettext.gettext("Unexpected error: An unknown error occured")
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
     * Listener for the exit button's click event.
     *
     * @param {Ext.Button} button The button that triggered the event
     */
    _onExitButtonClick : function(button)
    {
        this.fireEvent('exit');
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
     * Returns a {com.conjoon.groupware.reception.StateIndicator} for showing the
     * current login process and possible error messages that occured during login
     * attempts.
     *
     * @return {com.conjoon.groupware.reception.StateIndicator}
     */
    _createStateIndicator : function()
    {
        return new com.conjoon.groupware.reception.StateIndicator();
    },

    /**
     * Sets the text for the form intro.
     *
     * @param {String}
     */
    setFormIntroText : function(text)
    {
       this._formIntro.setText(text);
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
        if (!this.usernameValue) {
            this._usernameField.setDisabled(disable);
        }
        this._passwordField.setDisabled(disable);
        if (this._exitButton) {
            this._exitButton.setDisabled(disable);
        }
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
        var username = this.usernameValue || this._usernameField.getValue();

        if (this.fireEvent(
                'beforelogin',
                this,
                username,
                this._passwordField.getValue()) === false) {
            return;
        }

        this._formPanel.form.submit({
            params  : {
                username : username,
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
            text     : com.conjoon.Gettext.gettext("Login"),
            minWidth : 75,
            disabled : true,
            handler  : this._onLoginButtonClick,
            scope    : this
        });

        return lbutton;
    },

    /**
     * Creates this window's exit button.
     * The button will be shown in the footer of this window.
     *
     * @return {Ext.Button}
     *
     * @protected
     */
    _createExitButton : function()
    {
        var ebutton = new Ext.Button({
            text     : com.conjoon.Gettext.gettext("Exit"),
            minWidth : 75,
            handler  : this._onExitButtonClick,
            scope    : this
        });

        return ebutton;
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
            fieldLabel  : com.conjoon.Gettext.gettext("User name"),
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
            fieldLabel  : com.conjoon.Gettext.gettext("Password"),
            listeners   : {
                specialkey  : {
                    fn    : this._onSpecialKey,
                    scope : this
                }
            }
        });
    },

    /**
     * Creates and returns the form intro for use within the form panel.
     *
     * @return {com.conjoon.groupware.util.FormIntro}
     */
    _createFormIntro : function()
    {
        return new com.conjoon.groupware.util.FormIntro({
            style   : 'margin:0px 0 10px 0;',
            label   : com.conjoon.Gettext.gettext("Login"),
            text    : com.conjoon.Gettext.gettext("Please input your username and your password. Press &quot;Login&quot; when ready.")
        });
    },

    /**
     * Creates the form panel that shows both the username and the password field.
     *
     * @param {Ext.form.TextField} usernameField The field for entering the username.
     * @param {Ext.form.TextField} passwordField The field for entering the password.
     * @param {com.conjoon.groupware.util.FormIntro} formIntro The form intro
     * with the label and introduction text.
     *
     * @return {Ext.form.FormPanel}
     *
     * @protected
     */
    _createFormPanel : function(usernameField, passwordField, formIntro)
    {
        return new Ext.form.FormPanel({
            monitorValid : true,
            url          : this.loginUrl,
            method       : 'post',
            labelAlign   : 'right',
            cls          : 'x-small-editor com-conjoon-groupware-reception-LoginWindow-formPanel',
            labelWidth   : 75,
            defaults     : {
                anchor     : '100%',
                labelStyle : 'width:75px;font-size:11px;'
            },
            border   : false,
            items    : [
                formIntro,
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