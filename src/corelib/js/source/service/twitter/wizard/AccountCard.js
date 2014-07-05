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

Ext.namespace('com.conjoon.service.twitter.wizard');

/**
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 *
 * @class com.conjoon.service.twitter.wizard.AccountCard
 * @extends Ext.ux.Wiz.Card
 */
com.conjoon.service.twitter.wizard.AccountCard = Ext.extend(Ext.ux.Wiz.Card, {

    nameField : null,

    twitterIdField : null,

    oauthTokenField : null,

    oauthTokenSecretField : null,

    template : null,

    accountExistsTemplate : null,

    templateContainer : null,

    twitterButton : null,

    initComponent : function()
    {
        this.template = new Ext.Template(
            '<div style="margin-top:15px;">'+
                String.format(
                    com.conjoon.Gettext.gettext("Click \"next\" to add \"@{0}\" to the list of your Twitter accounts, or select another one."),
                    "{name:htmlEncode}"
                )+
            '</div>'
        );

        this.accountExistsTemplate = new Ext.Template(
            '<div style="margin-top:15px;">'+
                String.format(
                    com.conjoon.Gettext.gettext("The account \"@{0}\" is already available and cannot be added a second time. Please choose another account."),
                    "{name:htmlEncode}"
                )+
                '</div>'
        );

        var ts = this.templates;

        for(var k in ts){
            ts[k].compile();
        }

        this.monitorValid = true;

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
            name       : 'name',
            hidden     : true
        });

        this.twitterIdField = new Ext.form.TextField({
            fieldLabel : "",
            allowBlank : false,
            name       : 'twitterId',
            hidden     : true
        });

        this.oauthTokenField = new Ext.form.TextField({
            fieldLabel : "",
            allowBlank : false,
            name       : 'oauthToken',
            hidden     : true
        });

        this.oauthTokenSecretField = new Ext.form.TextField({
            fieldLabel : "",
            allowBlank : false,
            name       : 'oauthTokenSecret',
            hidden     : true
        });

        this.templateContainer = new Ext.BoxComponent({
            autoEl : {
                tag : 'div'
            }
        });

        this.twitterButton = new Ext.Button({
            text      : com.conjoon.Gettext.gettext("Click here to start authorization at Twitter"),
            listeners : {
                click : function(button) {
                    button.setDisabled(true);
                    window.open('./service/twitter.account/authorize.account');
                }
            }
        });

        this.items = [
            new com.conjoon.groupware.util.FormIntro({
                style     : 'margin:10px 0 15px 0',
                labelText : com.conjoon.Gettext.gettext("Account settings"),
                text      : com.conjoon.Gettext.gettext("By clicking on the button below, you will be redirected to the Twitter service, where you can chose whether conjoon may access your Twitter account or not. If you decide to allow conjoon access to your Twitter account, only a security token will be stored along with your username, but not your password.")
            }),
            this.twitterButton,
            this.templateContainer,
            this.nameField,
            this.twitterIdField,
            this.oauthTokenField,
            this.oauthTokenSecretField
        ];

        com.conjoon.service.twitter.wizard.AccountCard.superclass.initComponent.call(this);
    },

    applyDataFromOauth : function(accountData)
    {
        this.twitterButton.setDisabled(false);

        if (this._isAccountAlreadyAdded(accountData.twitterId)) {

            // RESET fields in case the user cycles through his list
            // of accounts right now. If there is one account that was
            // successfully added, but he decides to select another account
            // which cannot be added, the "next" button has to be invalidated
            // again
            this.nameField.setValue(null);
            this.twitterIdField.setValue(null);
            this.oauthTokenField.setValue(null);
            this.oauthTokenSecretField.setValue(null);

            var html = this.accountExistsTemplate.apply({
                name : accountData.name
            });

            this.templateContainer.el.update(html);
            return;
        }

        var html = this.template.apply({
            name : accountData.name
        });

        this.templateContainer.el.update(html);

        this.nameField.setValue(accountData.name);
        this.twitterIdField.setValue(accountData.twitterId);
        this.oauthTokenField.setValue(accountData.oauthToken);
        this.oauthTokenSecretField.setValue(accountData.oauthTokenSecret);
    },

    /**
     * Checks if an account is already added in the global Twitter Account Store.
     *
     * @param {String} twitterId The id of the Twitter Account to look up.
     *
     * @return {Boolean} true if this account is already existing, otherwise
     * false
     *
     * @protected
     */
    _isAccountAlreadyAdded : function(twitterId)
    {
        var store = com.conjoon.service.twitter.data.AccountStore.getInstance();

        if (store.findBy(function(rec){
            return rec.get('twitterId') == twitterId;
        }) > -1) {
            return true;
        }

        return false;
    }

});
