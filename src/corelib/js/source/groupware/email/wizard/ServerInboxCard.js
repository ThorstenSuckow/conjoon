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

Ext.namespace('com.conjoon.groupware.email.wizard');

/**
 *
 *
 * @class com.conjoon.groupware.email.wizard.ServerInboxCard
 * @extends Ext.ux.Wiz.Card
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
com.conjoon.groupware.email.wizard.ServerInboxCard = Ext.extend(Ext.ux.Wiz.Card, {

    hostField     : null,
    usernameField : null,
    passwordField : null,
    accountStore  : null,
    portField     : null,

    connectionSslRadio      : null,
    connectionTlsRadio      : null,
    connectionUnsecureRadio : null,

    /**
     * @type {String]
     */
    protocol : null,

    initComponent : function()
    {
        this.monitorValid = true;
        this.accountStore = com.conjoon.groupware.email.AccountStore.getInstance();


        this.baseCls    = 'x-small-editor';
        this.labelWidth = 100;

        this.defaultType = 'textfield';
        this.title = com.conjoon.Gettext.gettext("Inbox server");
        this.defaults = {
            labelStyle : 'width:100px;font-size:11px'
         };


        this.hostField = new Ext.form.TextField({
            fieldLabel : com.conjoon.Gettext.gettext("Host"),
            allowBlank : false,
            validator  : this.validateInbox.createDelegate(this),
            name       : 'serverInbox',
            anchor     : '100%'
        });

        this.usernameField = new Ext.form.TextField({
            fieldLabel : com.conjoon.Gettext.gettext("User name"),
            allowBlank : false,
            name       : 'usernameInbox',
            anchor     : '100%'
        });

        this.passwordField = new Ext.form.TextField({
            inputType  : 'password',
            fieldLabel : com.conjoon.Gettext.gettext("Password"),
            allowBlank : false,
            name       : 'passwordInbox',
            anchor     : '100%'
        });

        this.portField = new Ext.form.TextField({
            fieldLabel      : com.conjoon.Gettext.gettext("Port"),
            width           : 50,
            anchor          : '40%',
            name            : 'portInbox',
            labelStyle      : 'width:55px;font-size:11px',
            allowBlank      : false,
            validator       : this.isPortValid.createDelegate(this)
        });

        this.connectionUnsecureRadio = new Ext.form.Radio({
            boxLabel   : com.conjoon.Gettext.gettext('Never'),
            inputValue : 'never',
            itemCls    : 'com-conjoon-float-left',
            checked    : true,
            hideLabel  : true,
            name       : 'inboxConnectionType',
            listeners  : {
                check  : {
                    fn    : this.updatePortFieldBasedOnProtocolAndConnection,
                    scope : this
                }
            }
        });

        this.connectionSslRadio = new Ext.form.Radio({
            boxLabel   : 'SSL',
            inputValue : 'SSL',
            hideLabel  : true,
            itemCls    : 'com-conjoon-float-left com-conjoon-margin-l-25',
            name       : 'inboxConnectionType',
            listeners  : {
                check  : {
                    fn    : this.updatePortFieldBasedOnProtocolAndConnection,
                    scope : this
                }
            }
        });

        this.connectionTlsRadio = new Ext.form.Radio({
            boxLabel   : 'TLS',
            inputValue : 'TLS',
            hideLabel  : true,
            itemCls    : 'com-conjoon-float-left com-conjoon-margin-l-25',
            name       : 'inboxConnectionType',
            listeners  : {
                check  : {
                    fn    : this.updatePortFieldBasedOnProtocolAndConnection,
                    scope : this
                }
            }
        });

        this.items = [
            new com.conjoon.groupware.util.FormIntro({
                style     : 'margin:10px 0 15px 0;',
                labelText : com.conjoon.Gettext.gettext("Inbox server"),
                text      : com.conjoon.Gettext.gettext("Specify the host address of the inbox server here (e.g. pop3.provider.de) and your user credentials for authentication.")
            }),
            this.hostField,
            this.usernameField,
            this.passwordField,
            new Ext.BoxComponent({
                autoEl : {
                    tag   : 'div',
                    cls   : 'com-conjoon-margin-b-15'
                }}),
            new Ext.BoxComponent({
                autoEl : {
                    tag   : 'div',
                    html  : 'Secure connection:',
                    style : 'line-height:18px;margin-right:15px;padding-top:2px',
                    cls   : 'com-conjoon-float-left'
            }}),
            this.connectionUnsecureRadio,
            this.connectionSslRadio,
            this.connectionTlsRadio,
            new Ext.BoxComponent({
                autoEl : {
                    tag   : 'div',
                    cls   : 'com-conjoon-clear com-conjoon-margin-b-5'
                }}),
            this.portField

        ];

        com.conjoon.groupware.email.EmailAccountWizardNameCard.superclass.initComponent.call(this);
    },

    setProtocol : function(protocol)
    {
        if (protocol !== 'POP' && protocol !== 'IMAP') {
            throw("Unknown Protocol \""+protocol+"\"");
        }

        this.protocol = protocol;

        if (!this.portField.getValue()) {
            this.updatePortFieldBasedOnProtocolAndConnection();
        }
    },

    /**
     * @note using check event for individual radiofields triggers
     * this method two times, one time for check and one time for uncheck
     */
    updatePortFieldBasedOnProtocolAndConnection : function()
    {
        var prot       = this.protocol,
            conn       = this.form.getValues()['inboxConnectionType'],
            portCombos = {
                IMAP : {
                    never : 143,
                    SSL   : 993,
                    TLS   : 993
                },

                POP : {
                    never : 110,
                    SSL   : 995,
                    TLS   : 995
                }
            };

        this.portField.setValue(portCombos[prot][conn]);
    },

    /**
     * Checks wether the passed argument is a number in the range
     * from 0 to 65535.
     *
     * @param {String} value The value to check for validity
     * @return {Boolean} true if the passed value was valid, otherwise false
     */
    isPortValid : function(value)
    {
         var num = /^[0-9_]+$/;

         if (!num.test(value)) {
          return false;
         }

         if (value < 0 || value > 65535) {
            return false;
         }

         return true;
    },

    validateInbox : function(value)
    {
        value = value.trim();

        if (value === "") {
            return false;
        } else {
            /**
             * @ext-bug 2.0.2 seems to look for any match
             */
            //var index = this.accountStore.find('name', value, 0, false, false);
            /*var recs = this.accountStore.getRange();
            for (var i = 0, len = recs.length; i < len; i++) {
                if (recs[i].get('serverInbox').toLowerCase() === value) {
                    return false;
                }
            }

            return true;*/
        }


        return true;
    }

});