/**
 * conjoon
 * (c) 2007-2014 conjoon.org
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
 * @class com.conjoon.groupware.email.wizard.ServerOutboxCard
 * @extends Ext.ux.Wiz.Card
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
com.conjoon.groupware.email.wizard.ServerOutboxCard = Ext.extend(Ext.ux.Wiz.Card, {

    hostField     : null,
    useAuthField  : null,
    usernameField : null,
    passwordField : null,

    portField     : null,

    connectionSslRadio      : null,
    connectionTlsRadio      : null,
    connectionUnsecureRadio : null,

    initComponent : function()
    {
        this.monitorValid = true;

        this.baseCls    = 'x-small-editor';

        this.defaultType = 'textfield';
        this.title = com.conjoon.Gettext.gettext("Outbox server");


        this.hostField = new Ext.form.TextField({
            fieldLabel : com.conjoon.Gettext.gettext("Host"),
            allowBlank : false,
            labelStyle : 'width:85px;font-size:11px',
            width      : 200,
            name       : 'serverOutbox'
        });

        this.useAuthField = new Ext.form.Checkbox({
            fieldLabel : com.conjoon.Gettext.gettext("Server requires authentication"),
            labelStyle : 'margin-top:16px;width:170px;font-size:11px',
            style      : 'margin-top:20px;',
            name       : 'isOutboxAuth'
        });

        this.mon(this.useAuthField, 'check', this.onAuthCheck, this);

        this.usernameField = new Ext.form.TextField({
            fieldLabel : com.conjoon.Gettext.gettext("User name"),
            disabled   : true,
            labelStyle : 'width:85px;font-size:11px',
            width      : 200,
            name       : 'usernameOutbox'
        });

        this.passwordField = new Ext.form.TextField({
            inputType  : 'password',
            fieldLabel : com.conjoon.Gettext.gettext("Password"),
            disabled   : true,
            labelStyle : 'width:85px;font-size:11px',
            width      : 200,
            name       : 'passwordOutbox'
        });

        this.portField = new Ext.form.TextField({
            fieldLabel      : com.conjoon.Gettext.gettext("Port"),
            width           : 50,
            anchor          : '40%',
            name            : 'portOutbox',
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
            name       : 'outboxConnectionType',
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
            name       : 'outboxConnectionType',
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
            name       : 'outboxConnectionType',
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
                labelText : com.conjoon.Gettext.gettext("Outbox server"),
                text      : com.conjoon.Gettext.gettext("Specify the host address of the outbox server here (e.g. smtp.provider.de) and your user credentials, if the server requires authentication.")
            }),
            this.hostField,
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
            this.portField,
            this.useAuthField,
            this.usernameField,
            this.passwordField
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
            conn       = this.form.getValues()['outboxConnectionType'],
            portCombos = {
                IMAP : {
                    never : 25,
                    SSL   : 465,
                    TLS   : 465
                },

                POP : {
                    never : 25,
                    SSL   : 465,
                    TLS   : 465
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

    onAuthCheck : function(checkbox, checked)
    {
        this.passwordField.allowBlank = !checked;
        this.usernameField.allowBlank = !checked;

        if (!checked) {
            this.passwordField.reset();
            this.usernameField.reset();
        }

        this.passwordField.setDisabled(!checked);
        this.usernameField.setDisabled(!checked);
    }

});