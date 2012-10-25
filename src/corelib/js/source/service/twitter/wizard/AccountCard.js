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

    twitterIdField : null,

    oauthTokenField : null,

    oauthTokenSecretField : null,

    template : null,

    templateContainer : null,

    twitterButton : null,

    initComponent : function()
    {
        this.template= new Ext.Template(
            '<div style="margin-top:15px;">'+
                String.format(
                    com.conjoon.Gettext.gettext("Click \"next\" to add \"@{0}\" to the list of your Twitter accounts."),
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

        var html = this.template.apply({
            name : accountData.name
        });

        this.templateContainer.el.update(html);

        this.nameField.setValue(accountData.name);
        this.twitterIdField.setValue(accountData.twitterId);
        this.oauthTokenField.setValue(accountData.oauthToken);
        this.oauthTokenSecretField.setValue(accountData.oauthTokenSecret);
    }



});
