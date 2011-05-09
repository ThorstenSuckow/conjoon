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

Ext.namespace('com.conjoon.service.twitter.wizard');

/**
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 *
 * @class com.conjoon.service.twitter.wizard.AccountCard
 * @extends Ext.ux.Wiz.Card
 */
com.conjoon.service.twitter.wizard.AccountCard = Ext.extend(Ext.ux.Wiz.Card, {

    nameField : null,

    passwordField : null,

    accountStore : null,

    initComponent : function()
    {
        this.monitorValid = true;

        this.accountStore = com.conjoon.service.twitter.data.AccountStore.getInstance();

        this.baseCls    = 'x-small-editor';
        this.labelWidth = 75;

        this.defaultType = 'textfield';
        this.title = com.conjoon.Gettext.gettext("Account settings");
        this.defaults = {
            labelStyle : 'width:75px;font-size:11px',
            anchor: '100%'
         };

        this.nameField = new Ext.form.TextField({
            fieldLabel : "",
            allowBlank : false,
            validator  : this.validateAccountName.createDelegate(this),
            name       : 'name',
            hidden     : true
        });


        this.passwordField = new Ext.form.TextField({
            fieldLabel : com.conjoon.Gettext.gettext("Password"),
            allowBlank : false,
            inputType  : 'password',
            name       : 'password'
        });

        this.items = [
            new com.conjoon.groupware.util.FormIntro({
                style     : 'margin:10px 0 15px 0',
                labelText : com.conjoon.Gettext.gettext("Account settings"),
                text      : com.conjoon.Gettext.gettext("By clicking on the button below, you will be redirected to the Twitter service, where you can chose whether conjoon may access your Twitter account or not. If you decide to allow conjoon access to your Twitter account, only a security token will be stored along with your username, but not your password.")
            }),
            new Ext.Button({
                validator : this.validateAccountName.createDelegate(this),
                text      : com.conjoon.Gettext.gettext("Click here to start authorization at Twitter"),
                listeners  : {
                    click : function(button) {
                        button.setDisabled(true);
                        window.open('./service/twitter.account/authorize.account');
                    }
               },
            }), this.nameField
        ];

        com.conjoon.service.twitter.wizard.AccountCard.superclass.initComponent.call(this);
    },

    validateAccountName : function(value)
    {
        return false;
        value = value.trim();

        if (value === "") {
            return false;
        } else {
            /**
             * @ext-bug 2.0.2 seems to look for any match
             */
            //var index = this.accountStore.find('name', value, 0, false, false);
            var recs = this.accountStore.getRange();
            for (var i = 0, len = recs.length; i < len; i++) {
                if (recs[i].get('name').toLowerCase() === value) {
                    return false;
                }
            }

            return true;
        }


        return true;
    }

});
