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
            fieldLabel : com.conjoon.Gettext.gettext("Name"),
            allowBlank : false,
            validator  : this.validateAccountName.createDelegate(this),
            name       : 'name'
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
                text      : com.conjoon.Gettext.gettext("Enter your Twitter name and your password here. You need to have this account already registered at the Twitter service.<br />Make sure you did not already import this account.")
            }),
            this.nameField,
            this.passwordField
        ];

        com.conjoon.service.twitter.wizard.AccountCard.superclass.initComponent.call(this);
    },

    validateAccountName : function(value)
    {
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
