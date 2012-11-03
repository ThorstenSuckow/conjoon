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

com.conjoon.groupware.email.EmailAccountWizard = Ext.extend(Ext.ux.Wiz, {

    /**
     * @param {Object} requestId The current id of the ajax request that stores the
     * form values.
     */
    requestId : null,

    /**
     * When opened from the EmailAccountDialog, the dialog will pass on a list
     * of recently removed records (if available) to the wizard. The wizard can
     * then use the data to decide whether added data is already available,i.e.
     * in a pending state of removal, and accordingly forbid to create this data
     * because it was not confirmed to be destroyed in the EmailAccountDialog
     * yet.
     * @param {Array} pendingRemovedRecords
     */
    pendingRemovedRecords : null,

    /**
     * @cfg {Number} height The height of the dialog. Defaults to "420".
     */
    height : 420,

    /**
     * Inits this component.
     */
    initComponent : function()
    {

        this.cards = [
            new Ext.ux.Wiz.Card({
                title        : com.conjoon.Gettext.gettext("Welcome"),
                header       : false,
                border       : false,
                monitorValid : false,
                items        : [{
                    border    : false,
                    bodyStyle : 'background-color:#F6F6F6;',
                    html      : '<div style="margin-top:20px;">'
                                +com.conjoon.Gettext.gettext("You need to have an email account configured for sending and receiving email messages.<br /><br />This assistant will guide you through the neccessary steps for collecting the needed account information. If you are unsure about specific information asked by this assistant, please contact your email provider.")
                                +'</div>'
                }]
            }),

            //new com.conjoon.groupware.email.wizard.ServerTypeCard(),
            new com.conjoon.groupware.email.EmailAccountWizardNameCard(),
            new com.conjoon.groupware.email.wizard.ServerInboxCard(),
            new com.conjoon.groupware.email.wizard.ServerOutboxCard(),
            new com.conjoon.groupware.email.EmailAccountWizardAccountNameCard({
                pendingRemovedRecords : this.pendingRemovedRecords
            }),
            new com.conjoon.groupware.email.EmailAccountWizardFinishCard()
        ];
        this.cls = 'com-conjoon-groupware-email-EmailAccountWizard-panelBackground';
        this.title        = com.conjoon.Gettext.gettext("Email account assistant");
        this.headerConfig = {
            title : com.conjoon.Gettext.gettext("Create a new Email-Account")
        };

        com.conjoon.groupware.email.EmailAccountWizard.superclass.initComponent.call(this);
    },

    /**
     * Callback for the "finish" button. Collects all form values and sends them to the server
     * to create a new email account.
     */
    onFinish : function()
    {
        var values = {};
        var formValues = {};
        for (var i = 0, len = this.cards.length; i < len; i++) {
            formValues = this.cards[i].form.getValues(false);
            for (var a in formValues) {
                values[a] = formValues[a];
            }
        }

        // remove me
        values['protocol'] = 'POP3';

        values['isOutboxAuth'] = values['isOutboxAuth'] == 'on' ? true : false;

        if (!values['isOutboxAuth']) {
            values['usernameOutbox'] = '';
            values['passwordOutbox'] = '';
        }

        if (values['inboxConnectionType'] != 'SSL' && values['inboxConnectionType'] != 'TLS') {
            values['inboxConnectionType'] = null;
        }

        if (values['outboxConnectionType'] != 'SSL' && values['outboxConnectionType'] != 'TLS') {
            values['outboxConnectionType'] = null;
        }

        this.switchDialogState(false);

        this.requestId = Ext.Ajax.request({
            url               : './groupware/email.account/add.email.account/format/json',
            params            : values,
            success           : this.onAddSuccess,
            failure           : this.onAddFailure,
            scope             : this
        });
    },

    /**
     * Callback for a succesfull ajax request. Successfull means that the server
     * could handle the request and that no connection problems occured.
     * The response may report a failure though, due to connection problems
     * to the database or similiar.
     * The response will return the interger-value of the newly added account in
     * the database, otherwise it will be empty or hoolding an error message.
     * If the key of the newly created data is being returned, the account will be
     * added to the accountstore.
     *
     * @param {Object} response The response object returned by the server.
     * @param {Object} options The options used to initiate the request.
     */
    onAddSuccess : function(response, options)
    {
        var json = com.conjoon.util.Json;

        // first off, check the response if it contains any error
        if (json.isError(response.responseText)) {
            this.onAddFailure(response, options);
            return;
        }

        // fetch the response values
        var responseValues = json.getResponseValues(response.responseText);
        var account        = responseValues.account;
        var rootFolder     = responseValues.rootFolder;
        var accountStore   = com.conjoon.groupware.email.AccountStore.getInstance();

        var rec = com.conjoon.util.Record.convertTo(
            com.conjoon.groupware.email.AccountRecord,
            account, account.id
        );

        accountStore.addSorted(rec);

        if (rootFolder && rootFolder.id) {
            Ext.ux.util.MessageBus.publish(
                'com.conjoon.groupware.email.account.added',
                {account : rec, rootFolder : rootFolder}
            );
        }

        this.switchDialogState(true);
        this.requestId = null;
        this.close();
    },

    /**
     * Callback for an errorneous Ajax request. This method gets called
     * whenever the request couldn't be processed, due to connection or
     * server problems.
     *
     * @param {Object} response The response object returned by the server.
     * @param {Object} options The options used to initiate the request.
     */
    onAddFailure : function(response, options)
    {
        this.switchDialogState(true);

        com.conjoon.groupware.ResponseInspector.handleFailure(response);

        this.requestId = null;
    }


});

com.conjoon.groupware.email.EmailAccountWizardNameCard = Ext.extend(Ext.ux.Wiz.Card, {

    nameField : null,
    addressField : null,

    initComponent : function()
    {
        this.monitorValid = true;

        this.baseCls    = 'x-small-editor';
        this.labelWidth = 80;

        this.defaultType = 'textfield';
        this.title = com.conjoon.Gettext.gettext("Personal data");
        this.defaults = {
            labelStyle : 'width:80px;font-size:11px',
            anchor: '100%'
         };


        this.nameField = new Ext.form.TextField({
            fieldLabel : com.conjoon.Gettext.gettext("Your name"),
            allowBlank : false,
            name       : 'userName'
        });

        this.addressField = new Ext.form.TextField({
            fieldLabel : com.conjoon.Gettext.gettext("Email address"),
            allowBlank : false,
            validator  : Ext.form.VTypes.email,
            name       : 'address'
        });

        this.items = [
            new com.conjoon.groupware.util.FormIntro({
                style     : 'margin:10px 0 15px 0;',
                labelText : com.conjoon.Gettext.gettext("Personal data"),
                text      : com.conjoon.Gettext.gettext("Specify your real name and your email address here. This information will be visible to the recipients of your messages.")
            }),
            this.nameField,
            this.addressField
        ];

        com.conjoon.groupware.email.EmailAccountWizardNameCard.superclass.initComponent.call(this);
    }
});


com.conjoon.groupware.email.EmailAccountWizardAccountNameCard = Ext.extend(Ext.ux.Wiz.Card, {

    nameField    : null,
    accountStore : null,

    templateContainer : null,

    invalidTemplate : null,

    pendingRemovedRecords : null,

    initComponent : function()
    {
        this.monitorValid = true;

        this.accountStore = com.conjoon.groupware.email.AccountStore.getInstance();

        this.baseCls    = 'x-small-editor';
        this.labelWidth = 75;

        this.defaultType = 'textfield';
        this.title = com.conjoon.Gettext.gettext("Account name");
        this.defaults = {
            labelStyle : 'width:75px;font-size:11px',
            anchor: '100%'
         };

        this.nameField = new Ext.form.TextField({
            fieldLabel : com.conjoon.Gettext.gettext("Name"),
            allowBlank : false,
            validator  : this.validateAccountName.createDelegate(this),
            name       : 'name',
            listeners  : {
                invalid : {
                    fn : function(field, msg) {
                        var value = field.getValue(), html;

                        if (value.trim() === "") {
                            return;
                        }

                        html = this.invalidTemplate.apply({
                            name : value.toLowerCase()
                        });
                        this.templateContainer.el.update(html);
                        this.templateContainer.show();
                    },
                    scope : this
                },
                valid : {
                    fn : function() {
                        this.templateContainer.hide();
                    },
                    scope : this
                }
            }
        });

        this.invalidTemplate = new Ext.Template(
            '<div style="margin-top:15px;">'+
                String.format(
                    com.conjoon.Gettext.gettext("There is already an account named \"{0}\". Please choose another name."),
                    "{name:htmlEncode}"
                )+
                '</div>'
        );

        this.templateContainer = new Ext.BoxComponent({
            autoEl : {
                tag : 'div'
            }
        });

        this.items = [
            new com.conjoon.groupware.util.FormIntro({
                style     : 'margin:10px 0 15px 0;',
                labelText : com.conjoon.Gettext.gettext("Account name"),
                text      : com.conjoon.Gettext.gettext("Specify a unique name for this account. This name will be used later on to identify this account. The name must not be already existing.")
            }),
            this.nameField,
            this.templateContainer
        ];

        com.conjoon.groupware.email.EmailAccountWizardAccountNameCard.superclass.initComponent.call(this);
    },

    validateAccountName : function(value)
    {
        value = value.trim().toLowerCase();

        if (value === "") {
            return false;
        }

        var fn = function(rec){
            return rec.get('name').toLowerCase() == value;
        }, removed = this.pendingRemovedRecords;

        if (this.accountStore.findBy(fn) > -1) {
            return false;
        }

        // check removed records of store. We need to do this because a user
        // might have removed an account but NOT synced the store with the
        // server yet
        if (removed) {
            for (var i = 0, len = removed.length; i < len; i++) {
                if (removed[i].get('name').toLowerCase() == value) {
                    return false;
                }
            }
        }

        return true;
    }

});


com.conjoon.groupware.email.EmailAccountWizardFinishCard = Ext.extend(Ext.ux.Wiz.Card, {

    templates    : null,
    contentPanel : null,


    initComponent : function()
    {
        this.templates =  {
            master : new Ext.Template(
                '<table style="margin-top:15px;" border="0", cellspacing="2" cellpadding="2">'+
                    '<tbody>'+
                    '<tr><td>'+com.conjoon.Gettext.gettext("Server type")+':</td><td>{protocol}</td></tr>'+
                    '<tr><td>'+com.conjoon.Gettext.gettext("Account name")+':</td><td>{name:htmlEncode}</td></tr>'+
                    '<tr><td>'+com.conjoon.Gettext.gettext("Your name")+':</td><td>{userName:htmlEncode}</td></tr>'+
                    '<tr><td>'+com.conjoon.Gettext.gettext("Email address")+':</td><td>{address:htmlEncode}</td></tr>'+
                    '<tr><td>'+com.conjoon.Gettext.gettext("Inbox host")+':</td><td>{serverInbox:htmlEncode}:{portInbox}</td></tr>'+
                    '<tr><td>'+com.conjoon.Gettext.gettext("Inbox user name")+':</td><td>{usernameInbox:htmlEncode}</td></tr>'+
                    '<tr><td>'+com.conjoon.Gettext.gettext("Inbox password")+':</td><td>{passwordInbox}</td></tr>'+
                    '<tr><td>'+com.conjoon.Gettext.gettext("Secure connection for inbox")+':</td><td>{inboxConnectionType}</td></tr>'+
                    '<tr><td>'+com.conjoon.Gettext.gettext("Outbox host")+':</td><td>{serverOutbox:htmlEncode}:{portOutbox}</td></tr>'+
                    '<tr><td>'+com.conjoon.Gettext.gettext("Outbox authentication")+':</td><td>{isOutboxAuth}</td></tr>'+
                    '{auth_template}'+
                    '</tbody>'+
                '</table>'
            ),
            auth : new Ext.Template(
                '<tr><td>'+com.conjoon.Gettext.gettext("Outbox user name")+':</td><td>{usernameOutbox:htmlEncode}</td></tr>'+
                '<tr><td>'+com.conjoon.Gettext.gettext("Outbox password")+':</td><td>{passwordOutbox}</td></tr>'+
                '<tr><td>'+com.conjoon.Gettext.gettext("Secure connection for outbox")+':</td><td>{outboxConnectionType}</td></tr>'
            )
        };

        var ts = this.templates;

        for(var k in ts){
            ts[k].compile();
        }


        this.border = false;
        this.monitorValid = false;

        this.title = com.conjoon.Gettext.gettext("Confirm");

        this.contentPanel = new Ext.Panel({
            style : 'margin:0 0 0 20px'
        });

        this.items = [{
                border    : false,
                html      : "<div>"+com.conjoon.Gettext.gettext("The new account can now be created.<br />Please verify your submitted data and correct them if neccessary.")+"</div>",
                bodyStyle : 'background-color:#F6F6F6;margin:10px 0 0px 0'
            },
            this.contentPanel
        ];

        this.contentPanel.on('render', this.addContent, this, {single : true});

        com.conjoon.groupware.email.EmailAccountWizardFinishCard.superclass.initComponent.call(this);
    },

    addContent : function()
    {
        var ts = this.templates;

        var authTemplate = "";

        var items = this.ownerCt.items;

        var values = {};
        var formValues = {};
        for (var i = 0, len = items.length; i < len; i++) {
            formValues = items.get(i).form.getValues(false);
            for (var a in formValues) {
                values[a] = formValues[a];
            }
        }

        if (values.isOutboxAuth == 'on') {
            authTemplate = ts.auth.apply({
                usernameOutbox       : values.usernameOutbox,
                passwordOutbox       : "****",
                outboxConnectionType : values.outboxConnectionType == 'SSL'
                                        || values.outboxConnectionType == 'TLS'
                                      ? String.format(
                                            com.conjoon.Gettext.gettext("Uses {0}"),
                                            values.outboxConnectionType
                                        )
                                      : com.conjoon.Gettext.gettext("No")
            });
        }

        var html = ts.master.apply({
            portInbox     : values.portInbox,
            portOutbox    : values.portOutbox,
            protocol      : 'POP',//values.protocol,
            name          : values.name,
            userName      : values.userName,
            address       : values.address,
            serverInbox   : values.serverInbox,
            usernameInbox : values.usernameInbox,
            passwordInbox : "****",
            serverOutbox  : values.serverOutbox,
            isOutboxAuth  : values.isOutboxAuth == 'on'
                            ? com.conjoon.Gettext.gettext("Yes")
                            : com.conjoon.Gettext.gettext("No"),
            inboxConnectionType :  values.inboxConnectionType == 'SSL'
                                   || values.inboxConnectionType == 'TLS'
                                   ? String.format(
                                         com.conjoon.Gettext.gettext("Uses {0}"),
                                         values.inboxConnectionType
                                     )
                                   : com.conjoon.Gettext.gettext("No"),
            auth_template : authTemplate
        });

        this.contentPanel.el.update(html);

        this.on('show', this.addContent, this);
    }



});