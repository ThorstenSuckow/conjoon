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
 * @class com.conjoon.groupware.email.wizard.ServerInboxCard
 * @extends Ext.ux.Wiz.Card
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 */
com.conjoon.groupware.email.wizard.ServerInboxCard = Ext.extend(Ext.ux.Wiz.Card, {

    hostField     : null,
    usernameField : null,
    passwordField : null,
    accountStore  : null,
    portField     : null,

    connectionSslRadio      : null,
    connectionTslRadio      : null,
    connectionUnsecureRadio : null,

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
            value           : 110,
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
            name       : 'inboxConnectionType'
        });

        this.connectionSslRadio = new Ext.form.Radio({
            boxLabel   : 'SSL',
            inputValue : 'SSL',
            hideLabel  : true,
            itemCls    : 'com-conjoon-float-left com-conjoon-margin-l-25',
            name       : 'inboxConnectionType'
        });

        this.connectionTslRadio = new Ext.form.Radio({
            boxLabel   : 'TSL',
            inputValue : 'TSL',
            hideLabel  : true,
            itemCls    : 'com-conjoon-float-left com-conjoon-margin-l-25',
            name       : 'inboxConnectionType'
        });

        this.items = [
            new com.conjoon.groupware.util.FormIntro({
                style     : 'margin:10px 0 5px 0;',
                labelText : com.conjoon.Gettext.gettext("Inbox server"),
                text      : com.conjoon.Gettext.gettext("Specify the host address of the inbox server here (e.g. pop3.provider.de) and your user credentials for authentication.")
            }),
            this.hostField,
            this.portField,
            this.usernameField,
            this.passwordField,
            new Ext.BoxComponent({
                autoEl : {
                    tag   : 'div',
                    html  : 'Use secure connection:',
                    cls   : 'com-conjoon-margin-t-25 com-conjoon-margin-b-5'
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