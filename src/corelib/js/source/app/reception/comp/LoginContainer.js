/**
 * conjoon
 * (c) 2007-2015 conjoon.org
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
 * $Author: T. Suckow $
 * $Id: LoginWindow.js 1752 2013-10-25 22:43:40Z T. Suckow $
 * $Date: 2013-10-26 00:43:40 +0200 (Sa, 26 Okt 2013) $
 * $Revision: 1752 $
 * $LastChangedDate: 2013-10-26 00:43:40 +0200 (Sa, 26 Okt 2013) $
 * $LastChangedBy: T. Suckow $
 * $URL: http://svn.conjoon.org/trunk/src/corelib/js/source/groupware/reception/LoginWindow.js $
 */

/**
 * @class conjoon.reception.comp.LoginContainer
 * @extends Ext.Container
 * A container holding a username and password field for submitting
 * authentication data credentials.
 * @constructor
 * @param {Object} config The configuration options.
 */
Ext.defineClass('conjoon.reception.comp.LoginContainer', {

    extend : 'Ext.Container',

    cls   : 'cn-reception-loginContainer',

    /**
     * @cfg {String} usernameValue
     * If submitted, this value will be always send instead the value provided
     * by the username textfield. Additionally, the username field will be always
     * rendered as disabled and the default value equals to this value.
     */

    /**
     * @cfg {Number} lastUserRequest The timestamp of the last successfull call
     * to retrieve a user obejct of the current logged in user
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
     * @cfg {Boolean} rememberMeCookie
     * Whether a checkbox should be rendered that allows the user to choose
     * if he wishes conjoon to remember the login on this current
     * client's machine. Defaults to false.
     */
    rememberMeCookie : false,

    /**
     * @param {Ext.Button} loginButtonControl
     * @private
     */
    loginButtonControl : null,

    /**
     * @param {Ext.Button} exitButtonControl
     * @private
     */
    exitButtonControl : null,

    /**
     * @param {Ext.form.FormPanel} The form panel that holds both the username
     * and the password field.
     * @private
     */
    formPanel : null,

    /**
     * @param {Ext.form.TextField} usernameControl The textfield for the username.
     * @private
     */
    usernameControl : null,

    /**
     * @param {conjoon.reception.comp.StateIndicator} stateIndicator
     * A special component that's used for showing the current state of the login process
     * and displaying error messages of the login.
     * @private
     */
    stateIndicator : null,

    /**
     * @param {Ext.form.TextField} passwordControl The textfield for the password.
     * Subclasses are advised to use a textfield that's capable of masking
     * the input string.
     * @private
     */
    passwordControl : null,

    /**
     * @param {Ext.form.Checkbox} A checkbox representing the "remember my login"
     * option
     * @private
     */
    rememberMeCheckbox : null,

    /**
     * @cfg {String} softwareLabel
     */
    softwareLabel : "-",

    /**
     * @cfg {String} editionLabel
     */
    editionLabel : "-",

    /**
     * @cfg {String} versionLabel
     */
    versionLabel : "-",

    /**
     * Overrides parent implementation by taking care of rendering the
     * container.
     */
    show : function() {

        if (!this.rendered) {
            this.render(document.body);
        }

        conjoon.reception.comp.LoginContainer.superclass.show.call(this);
    },

    /**
     * Destroys this container with its child elements.
     *
     * @param {String} closeContext The context the login container is closed in.
     * Can be 'login' or 'exit'
     */
    close : function(closeContext) {

        var me = this;

        if (closeContext) {
            var slideOutTo = 'l';
            if (closeContext === 'login') {
                // okay
            } else if (closeContext === 'exit') {
                slideOutTo = 'r';
            }

            this.el.ghost(slideOutTo, {
                duration : 0.8,
                easing: 'easeOut',
                remove   : true,
                callback : function() {
                    this.destroy();
                },
                scope : me
            });


            return;
        }



        this.destroy();
    },

    /**
     * Helper function for controlling visibility of the formpanel
     *
     * @param {Boolean} show
     */
    showFormPanel : function(show) {
        this.getFormPanel().setVisible(show);
    },

    /**
     * Helper function for controlling visibility of the loginbutton
     *
     * @param {Boolean} show
     */
    showLoginButton : function(show) {
        this.getLoginButtonControl().setVisible(show);
    },

    /**
     * Inits this component.
     */
    initComponent : function() {

        var me = this;

        this.stateIndicator     = this.getStateIndicator();
        this.loginButtonControl = this.getLoginButtonControl();

        if (this.showExit) {
            this.exitButtonControl = this.getExitButtonControl();
        }

        this.usernameControl = this.getUsernameControl(
            this.usernameValue !== undefined
        );

        if (this.usernameValue) {
            this.usernameControl.setValue(this.usernameValue);
            this.usernameControl.setDisabled(true);
            this.defaultFocusField = 'password';
        }

        this.passwordControl = this.getPasswordControl();

        if (this.rememberMeCookie === true) {
            this.rememberMeControl = this.getRememberMeControl();
        }

        var items = [
            this.usernameControl,
            this.passwordControl
        ];

        if (this.rememberMeControl) {
            items.push(this.rememberMeControl)
        }

        this.formPanel = this.getFormPanel({items : items});

        var mainContainerItems = this.exitButtonControl
                             ? [this.exitButtonControl]
                             : [];

        mainContainerItems = mainContainerItems.concat([
            new Ext.BoxComponent({
                autoEl : {
                    tag  : 'div',
                    cls  : 'softwareInfo',
                    html : me.softwareLabel
                }
            }),
            this.formPanel,
            me.getLoginButtonControl()
        ]);

        me.items = [
            new Ext.Container({
                cls : 'mainContainer',
                layout : 'hbox',
                items : mainContainerItems
            }),
            new Ext.BoxComponent({
                autoEl : {
                    tag  : 'div',
                    cls : 'infoPanel',
                    cn : [{
                        tag : 'span',
                        cls : 'formLabel',
                        id : 'cn_reception_formLabel'
                    }, {
                        tag : 'span',
                        html : me.versionLabel,
                        cls : 'versionInfo'
                    }, {
                        tag : 'span',
                        html : me.editionLabel,
                        cls : 'editionInfo'
                    }]

                }
            }),
            me.getStateIndicator()
        ];

        this.initEvents();

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
             * @param {conjoon.reception.comp.LoginContainer}
             * @param {String} username
             * @param {String} password
             */
            'beforelogin',
            /**
             * @event loginsuccess
             * Gets fired when the request for a login succeeded.
             * The event does not indicate that a login attempt was valid.
             *
             * @param {conjoon.reception.comp.LoginContainer}
             * @param {Ext.form.BasicForm}
             * @param {Ext.from.Action}
             */
            'loginsuccess',
            /**
             * @event loginfailure
             * Gets fired when a request for the login did not succed, most likely
             * because of a server error.
             *
             * @param {conjoon.reception.comp.LoginContainer}
             * @param {Ext.form.BasicForm}
             * @param {Ext.from.Action}
             */
            'loginfailure'
        );


        conjoon.reception.comp.LoginContainer.superclass.initComponent.call(this);
    },

    /**
     * Sets the text for the form intro.
     *
     * @param {String}
     */
    setFormIntroText : function(text)
    {
        // empty
    },

    /**
     * Sets the label for the form intro.
     *
     * @param {String}
     */
    setFormIntroLabel : function(text)
    {
        Ext.fly(document.getElementById('cn_reception_formLabel')).update(text);
    },

    /**
     * Listener for the beforelogin event.
     *
     * @param {conjoon.reception.comp.LoginContainer}
     * @param {String} username
     * @param {String} password
     *
     * @protected
     */
    onBeforeLogin : function(basicForm, action)
    {
        this.getStateIndicator().setMessage(com.conjoon.Gettext.gettext("Please wait, trying to login..."), "loading");
    },

    /**
     * Overrides parent implementation and adds additional event listener to
     * this component.
     *
     */
    initEvents : function()
    {
        this.on('show' , function() {
            try {
                if (this.defaultFocusField == 'password') {
                    this.getPasswordControl().focus('', 10);
                } else {
                    this.getUsernameControl().focus('', 10);
                }
            } catch (e) {
                // ignore
            }
        }, this);

        this.on('beforelogin' , this.onBeforeLogin, this);
    },

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
    onClientValidation : function(formPanel, isValid)
    {
        if (!isValid) {
            this.getLoginButtonControl().setDisabled(true);
        } else {
            this.getLoginButtonControl().setDisabled(false);
        }
    },

    /**
     * Returns a {conjoon.reception.comp.StateIndicator} for showing the
     * current login process and possible error messages that occured during login
     * attempts.
     *
     * @return {conjoon.reception.comp.StateIndicator}
     */
    getStateIndicator : function()
    {
        var me = this;

        if (!me.stateIndicator) {
            me.stateIndicator = new conjoon.reception.comp.StateIndicator();
        }

        return me.stateIndicator;
    },

    /**
     * Callback for a successfull login attempt
     *
     * @param {Ext.form.BasicForm}
     * @param {Ext.form.Action}
     *
     * @protected
     */
    onLoginSuccess : function(basicForm, action)
    {
        this.getStateIndicator().setMessage(com.conjoon.Gettext.gettext("Login valid!"), 'ok');
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
    onLoginFailure : function(basicForm, action)
    {
        if (action) {
            if (action.failureType == Ext.form.Action.CONNECT_FAILURE) {
                this.getStateIndicator().setErrorMessage(
                    com.conjoon.Gettext.gettext("Unexpected error: Connection to server lost.")
                );
            } else if (action.result && action.result.error) {
                this.getStateIndicator().setErrorMessage(action.result.error);
            } else {
                this.getStateIndicator().setErrorMessage(
                    com.conjoon.Gettext.gettext("Unexpected error: An unknown error occured")
                );
            }
        }

        if (!com.conjoon.groupware.Reception.isClosed()) {
            this.getPasswordControl().setValue('');
        }

        try {
            this.getPasswordControl().focus('', 10);
        } catch (e) {
            // ignore
        }

        this.fireEvent('loginfailure', this, basicForm, action);
    },

    /**
     * Returns the control representing an exit button.
     *
     * @return {Ext.BoxComponent}
     */
    getExitButtonControl : function() {
        var me = this;

        if (!me.exitButtonControl) {
            me.exitButtonControl = new Ext.BoxComponent({
                autoEl : {
                    tag  : 'div',
                    cls : 'exitButton',
                    html : '&lt;',
                    title : com.conjoon.Gettext.gettext("Exit")
                },
                listeners : {
                    afterrender : function() {
                        this.mon(
                            this.el, 'click', me.onExitButtonClick, me
                        );
                    }
                }
            });
        }

        return me.exitButtonControl;
    },

    /**
     * Listener for the exit button's click event.
     *
     * @param {Object} evt
     * @param {HTMLElement} el
     */
    onExitButtonClick : function(evt, el)
    {
        this.fireEvent('exit');
    },

    /**
     * Returns the control representing a login button.
     *
     * @return {Ext.BoxComponent}
     */
    getLoginButtonControl : function() {

        var me = this;

        if (!me.loginButtonControl) {

            me.loginButtonControl = new Ext.BoxComponent({
                autoEl : {
                    tag  : 'div',
                    cls : 'loginButton',
                    html : '&gt;'
                },
                listeners : {
                    afterrender : function() {
                        this.mon(
                            this.el, 'click', me.onLoginButtonClick, me
                        );
                    },
                    disable : {
                        fn : me.onLoginControlDisable,
                        single : true,
                        scope : me
                    },
                    enable : {
                        fn : me.onLoginControlEnable,
                        single : true,
                        scope : me
                    }
                }
            });
        }

        return me.loginButtonControl;
    },

    /**
     * Listener for the loginControl's disable event.
     *
     * @param {Ext.BoxComponent} loginControl
     */
    onLoginControlDisable : function(loginControl) {

        var me = this;

        loginControl.el.dom.title = "";

        me.mon(loginControl, 'enable', me.onLoginControlEnable, me, {single : true});
    },

    /**
     * Listener for the loginControl's enable event.
     *
     * @param {Ext.BoxComponent} loginControl
     */
    onLoginControlEnable : function (loginControl) {
        var me = this;

        loginControl.el.dom.title = com.conjoon.Gettext.gettext("Login");

        me.mon(loginControl, 'disable', me.onLoginControlDisable, me, {single : true});
    },

    /**
     * Listener for the login button's click event.
     *
     * @param {Object} evt
     * @param {HTMLElement} el
     */
    onLoginButtonClick : function(evt, el)
    {
        if (this.getLoginButtonControl().disabled) {
            return;
        }

        this.doSubmit();
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
            this.getFormPanel().stopMonitoring();
        } else {
            this.getFormPanel().startMonitoring();
        }

        this.getLoginButtonControl().setDisabled(disable);
        if (!this.usernameValue) {
            this.getUsernameControl().setDisabled(disable);
        }
        this.getPasswordControl().setDisabled(disable);
        if (this.getExitButtonControl()) {
            this.getExitButtonControl().setDisabled(disable);
        }

        this.getRememberMeControl().setDisabled(disable);
    },

    /**
     * Submits this form.
     *
     * @todo send password md5 hashed?
     *
     * @protected
     */
    doSubmit : function()
    {
        var username = this.usernameValue || this.getUsernameControl().getValue();

        if (this.fireEvent(
            'beforelogin',
            this,
            username,
            this.getPasswordControl().getValue()) === false) {
            return;
        }

        var params = {
            username : username,
            password : this.getPasswordControl().getValue()
        };

        if (this.rememberMeCookie && this.rememberMeControl) {
            params.rememberMe = this.rememberMeControl.getValue() ? 1 : 0;
        }

        if (this.lastUserRequest) {
            params.lastUserRequest = this.lastUserRequest;
        }

        this.getFormPanel().form.submit({
            params  : params,
            success : this.onLoginSuccess,
            failure : this.onLoginFailure,
            scope   : this
        });
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
    onSpecialKey : function(textField, eventObject)
    {
        var keyCode = eventObject.getKey();
        var eO      = Ext.EventObject;

        if (keyCode != eO.ENTER && keyCode != eO.RETURN) {
            return;
        }

        if (this.getUsernameControl().getValue().trim() == ""
            || this.getPasswordControl().getValue().trim() == "") {
            return;
        }

        this.doSubmit();
    },


    /**
     * Creates the form field for entering the username.
     *
     * @param {Boolean} displayOnly whether a display instead of an input text
     * field should be generated
     *
     * @return {Ext.form.TextField}
     *
     * @protected
     */
    getUsernameControl : function(displayOnly)
    {
        var me = this;

        if (!me.usernameControl) {

            var fieldClass = displayOnly === true
                             ? Ext.form.DisplayField
                             : Ext.form.TextField

            me.usernameControl = new fieldClass({
                name        : 'username',
                preventMark : true,
                cls : 'usernameControl',
                hideLabel : true,
                allowBlank  : false,
                listeners   : {
                    specialkey  : {
                        fn    : this.onSpecialKey,
                        scope : this
                    },
                    afterrender : function() {
                        this.el.dom.placeholder = com.conjoon.Gettext.gettext("Username");
                    }
                }
            })
        }

        return me.usernameControl;
    },

    /**
     * Creates the checkbox for choosing whether the login shoudl be remebered.
     *
     * @return {Ext.form.Checkbox}
     *
     * @protected
     */
    getRememberMeControl : function()
    {
        var me = this;

        if (!me.rememberMeControl) {
            me.rememberMeControl = new Ext.form.Checkbox({
                name : 'rememberMe',
                ctCls : 'rememberMeControl',
                inputValue : true,
                hideLabel : true,
                boxLabel   : com.conjoon.Gettext.gettext("Remember my login")
            });
        }

        return me.rememberMeControl;
    },

    /**
     * Creates the form field for entering the password.
     *
     * @return {Ext.form.TextField}
     *
     * @protected
     */
    getPasswordControl : function()
    {
        var me = this;

        if (!me.passwordControl) {
            me.passwordControl =  new Ext.form.TextField({
                inputType   : 'password',
                allowBlank  : false,
                preventMark : true,
                name        : 'password',
                cls : 'passwordControl',
                hideLabel : true,
                placeholder  : com.conjoon.Gettext.gettext("Password"),
                listeners   : {
                    specialkey  : {
                        fn    : this.onSpecialKey,
                        scope : this
                    },
                    afterrender : function() {
                        this.el.dom.placeholder = com.conjoon.Gettext.gettext("Password");
                    }
                }
            });
        }

        return me.passwordControl;
    },


    /**
     * Creates the form panel that shows both the username and the password field.
     *
     * @param {object} cfg initial config for initially creating this object
     *
     * @return {Ext.form.FormPanel}
     *
     * @protected
     */
    getFormPanel : function(cfg) {

        var me = this;

        if (!me.formPanel) {

            me.formPanel = new Ext.form.FormPanel(Ext.apply({
                monitorValid : true,
                url          : this.loginUrl,
                method       : 'post',
                cls          : 'formPanel',
                defaults     : {
                    anchor : '100%'
                },
                border    : false,
                listeners : {
                    clientvalidation : {
                        fn    : this.onClientValidation,
                        scope : this
                    }
                }
            }, cfg));
        }

        return me.formPanel;
    }

});