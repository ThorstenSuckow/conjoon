/**
 * conjoon
 * (c) 2002-2010 siteartwork.de/conjoon.org
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
 * @class com.conjoon.groupware.email.wizard.ServerOutboxCard
 * @extends Ext.ux.Wiz.Card
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
com.conjoon.groupware.email.wizard.ServerOutboxCard = Ext.extend(Ext.ux.Wiz.Card, {

    hostField     : null,
    useAuthField  : null,
    usernameField : null,
    passwordField : null,

    portField     : null,

    connectionSslRadio      : null,
    connectionTslRadio      : null,
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
            labelStyle : 'margin-top:12px;width:180px;font-size:11px',
            style      : 'margin-top:14px;',
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
            value           : 25,
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
            disabled   : true,
            name       : 'outboxConnectionType'
        });

        this.connectionSslRadio = new Ext.form.Radio({
            boxLabel   : 'SSL',
            inputValue : 'SSL',
            hideLabel  : true,
            disabled   : true,
            itemCls    : 'com-conjoon-float-left com-conjoon-margin-l-25',
            name       : 'outboxConnectionType'
        });

        this.connectionTslRadio = new Ext.form.Radio({
            boxLabel   : 'TSL',
            inputValue : 'TSL',
            hideLabel  : true,
            disabled   : true,
            itemCls    : 'com-conjoon-float-left com-conjoon-margin-l-25',
            name       : 'outboxConnectionType'
        });

        this.items = [
            new com.conjoon.groupware.util.FormIntro({
                style     : 'margin:10px 0 5px 0;',
                labelText : com.conjoon.Gettext.gettext("Outbox server"),
                text      : com.conjoon.Gettext.gettext("Specify the host address of the outbox server here (e.g. smtp.provider.de) and your user credentials, if the server requires authentication.")
            }),
            this.hostField,
            this.portField,
            this.useAuthField,
            this.usernameField,
            this.passwordField,
            new Ext.BoxComponent({
                autoEl : {
                    tag   : 'div',
                    html  : 'Use secure connection:',
                    cls   : 'com-conjoon-margin-t-15 com-conjoon-margin-b-5'
            }}),
            this.connectionUnsecureRadio,
            this.connectionSslRadio,
            this.connectionTslRadio
        ];



        com.conjoon.groupware.email.EmailAccountWizardNameCard.superclass.initComponent.call(this);
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
        this.connectionSslRadio.setDisabled(!checked);
        this.connectionTslRadio.setDisabled(!checked);
        this.connectionUnsecureRadio.setDisabled(!checked);

    }

});