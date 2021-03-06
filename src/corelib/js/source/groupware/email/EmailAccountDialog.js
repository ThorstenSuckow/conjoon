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
 * $Author$
 * $Id$
 * $Date$
 * $Revision$
 * $LastChangedDate$
 * $LastChangedBy$
 * $URL$
 */

Ext.namespace('com.conjoon.groupware.email');

com.conjoon.groupware.email.EmailAccountDialog = Ext.extend(Ext.Window, {

    /**
     * @param {com.conjoon.groupware.email.AccountRecord} clkRecord The record
     * in the grid that is currently selected
     */
    clkRecord : null,

    /**
     * @param {Number} clkRowIndex The index of the currently selected record
     */
    clkRowIndex : -1,

    /**
     * @param {Ext.Panel} cardPanel The panel holding the mainTabPanel and the
     * introPanel
     */
    cardPanel : null,

    /**
     * @param {Ext.TabPanel} mainTabPanel the mainTabPanel that holds the panels
     * with the various configuration options
     */
    mainTabPanel : null,

    /**
     * @param {Ext.Panel} introPanel The panel that is shown when no record is
     * available for editing or when no record is currently being selected in
     * the grid.
     */
    introPanel : null,

    /**
     * @param {Ext.Button} addAccountButton The button for initiating the wizard
     * dialog for adding a new account
     */
    addAccountButton : null,

    /**
     * @param {Ext.Button} removeAccountButton The button for removing an account
     */
    removeAccountButton : null,

    /**
     * @param {Ext.Button} setAccountAsStandardButton The button for setting an account
     * as the default account
     */
    setAccountAsStandardButton : null,

    /**
     * @param {Object} fields An object containing all fields available in the form.
     */
    fields : null,

    /**
     * @param {Ext.grid.GridPanel} accountGridPanel The grid showing all accounts
     * available in com.conjoon.groupware.email.AccountStore
     */
    accountGridPanel : null,

    /**
     * @param {Ext.Button} okButton The ok-button for this dialog
     */
    okButton : null,

    /**
     * @param {Ext.Button} cancelButton The cancel-button for this dialog
     */
    cancelButton : null,

    /**
     * @param {Ext.Button} applyButton The apply-button for this dialog
     */
    applyButton : null,

    /**
     * @param {com.conjoon.groupware.email.AccountStore} accountStore Shortcut
     * to the globally available account store
     */
    accountStore : null,

    /**
     * @param {Number} modifiedRecordCount Stores the number of modified records
     * during this dialog lifetime. Increases everytime a record was modified
     * in the form.
     */
    modifiedRecordCount : 0,

    /**
     * @param {Object} requestId The current processed Ajax request
     */
    requestId : null,

    /**
     * @param {Object} deletedRecords Caches all records that where removed from the store
     * and thus are marked for deletion in the underlying data model.
     * If deleting fails, the changes can be rejected and the records which could
     * not be deleted can be added to the data store again
     */
    deletedRecords : null,

    /**
     * @type {com.conjoon.groupware.email.account.view.ActionFolderMappingPanel}
     */
    actionFolderMappingPanel : null,

    initComponent : function()
    {
        this.fields         = {};
        this.deletedRecords = {};

        this.accountStore = com.conjoon.groupware.email.AccountStore.getInstance();

        var accountGridPanel = new Ext.grid.GridPanel({
            cls            : 'com-conjoon-groupware-email-EmailAccountDialog-accountGrid',
            height         : 266,
            border         : true,
            sortInfo       : {field: 'name', direction: "DESC"},
            store          : this.accountStore,
            hideHeaders    : true,
            trackMouseOver : false,
            columns       : [{
                header    : com.conjoon.Gettext.gettext("Account name"),
                width     : 173,
                sortable  : true,
                dataIndex : 'name',
                sortable  : true,
                renderer  : function(value, metadata, record, rowIndex, colIndex, store) {
                    if (record.get('name') == "") {
                        return com.conjoon.Gettext.gettext("(no account name set)");
                    }

                    return record.get('name');
                }
            }],
            viewConfig : {
                getRowClass : function(record, index, rowParams, store) {
                    if (record.data.isStandard) {
                        return 'com-conjoon-groupware-email-EmailAccountDialog-grid-standardAccountRow';
                    }

                    return '';
                }
            },
            sm : new Ext.grid.RowSelectionModel({singleSelect:true})
        });
        this.accountGridPanel = accountGridPanel;

        var Checkbox  = Ext.form.Checkbox;
        var TextField = Ext.form.TextField;
        var Radio     = Ext.form.Radio;

        this.fields = {
            'name' : new TextField({
                fieldLabel      : com.conjoon.Gettext.gettext("Account name"),
                allowBlank      : false,
                validator       : this.isAccountNameValid.createDelegate(this),
                enableKeyEvents : true
            }),
            'userName' : new TextField({
                fieldLabel      : com.conjoon.Gettext.gettext("Your name"),
                allowBlank      : false,
                enableKeyEvents : true
            }),
            'address' : new TextField({
                fieldLabel      : com.conjoon.Gettext.gettext("Email address"),
                allowBlank      : false,
                validator       : Ext.form.VTypes.email,
                enableKeyEvents : true
            }),
            'replyAddress'      : new TextField({
                fieldLabel      : com.conjoon.Gettext.gettext("Reply to"),
                allowBlank      : true,
                vtype           : 'email',
                enableKeyEvents : true
            }),
            'protocol1' : new Ext.form.DisplayField({
                labelStyle : 'width:102px;font-size:11px',
                ctCls      : 'com-conjoon-smalleditor',
                fieldLabel : com.conjoon.Gettext.gettext("Protocol"),
                value      : 'POP',
                width      : 225
            }),
            'serverInbox' : new TextField({
                fieldLabel      : com.conjoon.Gettext.gettext("Host"),
                width           : 255,
                labelStyle      : 'width:102px;font-size:11px',
                allowBlank      : false,
                validator       : this.isServerInboxValid.createDelegate(this),
                enableKeyEvents : true
            }),
            'portInbox' : new TextField({
                fieldLabel      : com.conjoon.Gettext.gettext("Port"),
                width           : 50,
                labelStyle      : 'width:102px;font-size:11px',
                allowBlank      : false,
                validator       : this.isPortValid.createDelegate(this),
                enableKeyEvents : true
            }),
            'usernameInbox' : new TextField({
                fieldLabel      : com.conjoon.Gettext.gettext("User name"),
                width           : 255,
                labelStyle      : 'width:102px;font-size:11px',
                allowBlank      : false,
                enableKeyEvents : true
            }),
            'passwordInbox' : new TextField({
                inputType       : 'password',
                width           : 255,
                labelStyle      : 'width:102px;font-size:11px',
                fieldLabel      : com.conjoon.Gettext.gettext("Password"),
                allowBlank      : false,
                enableKeyEvents : true
            }),

            'inboxConnectionTypeUnsecure' : new Radio({
                hideLabel  : true,
                itemCls    : 'com-conjoon-float-left',
                boxLabel   : com.conjoon.Gettext.gettext("Never"),
                name       : 'inboxConnectionType',
                inputValue : 'never'
            }),

            'inboxConnectionTypeSsl' : new Radio({
                hideLabel  : true,
                boxLabel   : 'SSL',
                itemCls    : 'com-conjoon-float-left com-conjoon-margin-l-25',
                name       : 'inboxConnectionType',
                inputValue : 'SSL'
            }),

            'inboxConnectionTypeTls' : new Radio({
                hideLabel  : true,
                boxLabel   : 'TLS',
                itemCls    : 'com-conjoon-float-left com-conjoon-margin-l-25',
                name       : 'inboxConnectionType',
                inputValue : 'TLS'
            }),

            'outboxConnectionTypeUnsecure' : new Radio({
                hideLabel  : true,
                boxLabel   : com.conjoon.Gettext.gettext("Never"),
                itemCls    : 'com-conjoon-float-left',
                name       : 'outboxConnectionType',
                inputValue : 'never'
            }),

            'outboxConnectionTypeSsl' : new Radio({
                hideLabel  : true,
                boxLabel   : 'SSL',
                itemCls    : 'com-conjoon-float-left com-conjoon-margin-l-25',
                name       : 'outboxConnectionType',
                inputValue : 'SSL'
            }),

            'outboxConnectionTypeTls' : new Radio({
                hideLabel  : true,
                boxLabel   : 'TLS',
                itemCls    : 'com-conjoon-float-left com-conjoon-margin-l-25',
                name       : 'outboxConnectionType',
                inputValue : 'TLS'
            }),

            'serverOutbox' : new TextField({
                fieldLabel      : com.conjoon.Gettext.gettext("Host"),
                width           : 255,
                labelStyle      : 'width:102px;font-size:11px',
                allowBlank      : false,
                enableKeyEvents : true
            }),
            'portOutbox' : new TextField({
                fieldLabel      : com.conjoon.Gettext.gettext("Port"),
                width           : 50,
                allowBlank      : false,
                labelStyle      : 'width:102px;font-size:11px',
                validator       : this.isPortValid.createDelegate(this),
                enableKeyEvents : true
            }),
            'isOutboxAuth' : new Checkbox({
                fieldLabel : com.conjoon.Gettext.gettext("Authorization"),
                labelStyle : 'width:102px;font-size:11px'
            }),
            'usernameOutbox' : new TextField({
                fieldLabel      : com.conjoon.Gettext.gettext("User name"),
                width           : 255,
                labelStyle      : 'width:102px;font-size:11px',
                disabled        : true,
                allowBlank      : false,
                enableKeyEvents : true
            }),
            'passwordOutbox' : new TextField({
                inputType       : 'password',
                width           : 255,
                labelStyle      : 'width:102px;font-size:11px',
                fieldLabel      : com.conjoon.Gettext.gettext("Password"),
                disabled        : true,
                allowBlank      : false,
                enableKeyEvents : true
            }),
            'isCopyLeftOnServer' : new Checkbox({
                labelStyle : 'width:165px;font-size:11px',
                fieldLabel : com.conjoon.Gettext.gettext("Delete messages from server")
            }),
            'hasSeparateFolderHierarchy' : new Checkbox({
                labelStyle : 'width:220px;font-size:11px',
                fieldLabel : com.conjoon.Gettext.gettext("Use separate folder hierarchy for account")
            }),
            'isSignatureUsed' : new Checkbox({
                fieldLabel : com.conjoon.Gettext.gettext("Use signature"),
                labelStyle : 'width:105px;font-size:11px'
            }),
            'signature' : new Ext.form.TextArea({
                disabled        : true,
                labelStyle      : 'width:105px;font-size:11px',
                fieldLabel      : com.conjoon.Gettext.gettext("Signature"),
                style           : 'font-family:Courier new, Times new Roman, helvetica, Arial, Verdana',
                width           : 250,
                height          : 95,
                enableKeyEvents : true
            })
        };

        var fields = this.fields;

        var identitySettingsPanel = new Ext.FormPanel({
            title       : com.conjoon.Gettext.gettext("Identity"),
            bodyStyle   : 'padding:5px 15px 5px 15px;background-color:#F6F6F6',
            defaultType : 'textfield',
            baseCls     : 'x-small-editor',
            labelWidth  : 115,
            labelAlign : 'right',
            defaults   : {
                labelStyle : 'width:115px;font-size:11px',
                anchor: '100%'
             },
            items       : [new com.conjoon.groupware.util.FormIntro({
                style     : 'margin:10px 0 5px 0;',
                labelText : com.conjoon.Gettext.gettext("Account name"),
                text      : com.conjoon.Gettext.gettext("This name is used to identify the account later on.")
            }),
            fields['name'],
            new com.conjoon.groupware.util.FormIntro({
                style     : 'margin:25px 0 5px 0;',
                labelText : com.conjoon.Gettext.gettext("Personal Informations"),
                text      : com.conjoon.Gettext.gettext("The following informations will be visible to the recipients of your email messages.")
            }),
            fields['address'],
            fields['userName'],
            fields['replyAddress']
            ]
        });
        var incomingMailPanel = new Ext.FormPanel({
            title       : com.conjoon.Gettext.gettext("Incoming Mail"),
            bodyStyle   : 'padding:5px 15px 5px 15px;background-color:#F6F6F6',
            defaultType : 'textfield',
            baseCls     : 'x-small-editor',
            labelAlign : 'left',
            labelWidth : 30,
            items       : [
                new com.conjoon.groupware.util.FormIntro({
                    style     : 'margin:10px 0 15px 0;',
                    labelText : com.conjoon.Gettext.gettext("Inbox"),
                    text      : com.conjoon.Gettext.gettext("These settings will be used to connect to the inbox server and retrieve new email messages.")
                }),
                fields['serverInbox'],
                new com.conjoon.groupware.util.Clear({style : 'height:15px'}),
                fields['protocol1'],
                new Ext.BoxComponent({
                    autoEl : {
                        tag   : 'div',
                        html  : 'Secure connection:',
                        style : 'line-height:18px;margin-right:15px;padding-top:2px',
                        cls   : 'com-conjoon-float-left'
                    }}),
                fields['inboxConnectionTypeUnsecure'],
                fields['inboxConnectionTypeSsl'],
                fields['inboxConnectionTypeTls'],
                new com.conjoon.groupware.util.Clear({style : 'height:5px'}),
                fields['portInbox'],
                new com.conjoon.groupware.util.Clear({style : 'height:25px'}),
                fields['usernameInbox'],
                fields['passwordInbox'],
                new com.conjoon.groupware.util.Clear()
            ]
        });
        var outgoingMailPanel = new Ext.FormPanel({
            title       : com.conjoon.Gettext.gettext("Outgoing Mail"),
            bodyStyle   : 'padding:5px 15px 5px 15px;background-color:#F6F6F6',
            defaultType : 'textfield',
            baseCls     : 'x-small-editor',
            labelAlign : 'left',
            labelWidth : 30,
            items       : [
                new com.conjoon.groupware.util.FormIntro({
                        style     : 'margin:10px 0 15px 0;',
                        labelText : com.conjoon.Gettext.gettext("Outbox"),
                        text      : com.conjoon.Gettext.gettext("These settings will be used to connect to the outbox server to send email messages.")
                }),
                fields['serverOutbox'],
                new com.conjoon.groupware.util.Clear({style : 'height:15px'}),
                new Ext.BoxComponent({
                    autoEl : {
                        tag   : 'div',
                        html  : 'Secure connection:',
                        style : 'line-height:18px;margin-right:15px;padding-top:2px',
                        cls   : 'com-conjoon-float-left'
                    }}),
                fields['outboxConnectionTypeUnsecure'],
                fields['outboxConnectionTypeSsl'],
                fields['outboxConnectionTypeTls'],
                new com.conjoon.groupware.util.Clear({style : 'height:5px'}),
                fields['portOutbox'],
                new com.conjoon.groupware.util.Clear({style : 'height:25px'}),
                fields['isOutboxAuth'],
                fields['usernameOutbox'],
                fields['passwordOutbox'],
                new com.conjoon.groupware.util.Clear()

            ]
        });
        var commonSettingsPanel = new Ext.FormPanel({
            title       : com.conjoon.Gettext.gettext("Settings"),
            bodyStyle   : 'padding:5px 15px 5px 15px;background-color:#F6F6F6',
            defaultType : 'textfield',
            baseCls     : 'x-small-editor',
            labelAlign : 'left',
            items       : [
                new com.conjoon.groupware.util.FormIntro({
                    style     : 'margin:10px 0 10px 0;',
                    labelText : com.conjoon.Gettext.gettext("Fetch messages"),
                    text      : com.conjoon.Gettext.gettext("You can delete each message from the inbox server after downloading them. If you use additional software to organize your emails, it is suggested to leave this option off.<br />Note:<br />This option is not available for IMAP based accounts.")
                }),
                fields['isCopyLeftOnServer'],
                new com.conjoon.groupware.util.FormIntro({
                    style     : 'margin:25px 0 10px 0;',
                    labelText : com.conjoon.Gettext.gettext("Separate Folder Hierarchy"),
                    text      : com.conjoon.Gettext.gettext("This account can be managed in the \"Local Folders\" together with other Email Accounts, or you can create a separate folder hierarchy for this account.")
                }),
                fields['hasSeparateFolderHierarchy']
            ]
        });

        var signatureSettingsPanel = new Ext.FormPanel({
            title       : com.conjoon.Gettext.gettext("Signature"),
            bodyStyle   : 'padding:5px 15px 5px 15px;background-color:#F6F6F6',
            defaultType : 'textfield',
            baseCls     : 'x-small-editor',
            labelAlign : 'left',
            items       : [new com.conjoon.groupware.util.FormIntro({
                style     : 'margin:10px 0 10px 0;',
                labelText : com.conjoon.Gettext.gettext("Create signature"),
                text      : com.conjoon.Gettext.gettext("You can create a signature which will then be appended to each message you send using this account.")
            }),
                fields['isSignatureUsed'],
                fields['signature']
            ]
        });

        this.actionFolderMappingPanel = new com.conjoon.groupware.email.account.view.ActionFolderMappingPanel({
            bodyStyle   : 'padding:5px 15px 5px 15px;background-color:#F6F6F6',
            defaultType : 'textfield',
            baseCls     : 'x-small-editor',
            labelAlign  : 'left'
        });

        this.introPanel = new Ext.Panel({
            border   : false,
            hideMode : 'offsets',
            cls      : 'com-conjoon-groupware-email-EmailAccountDialog-introPanel',
            items    : [
                new com.conjoon.groupware.util.FormIntro({
                    style     : 'margin:10px 0 5px 0;',
                    labelText : com.conjoon.Gettext.gettext("Email accounts"),
                    text      : com.conjoon.Gettext.gettext("Chose an existing account for editing or create a new one.")
                })
            ]
        });

        this.mainTabPanel = new Ext.TabPanel({
            activeItem : 0,
            deferredRender : false,
            cls        : 'com-conjoon-groupware-email-EmailAccountDialog-TabHeader',
            border     : true,
            plain      : true,
            hideMode   : 'offsets',
            style      : 'background:none',
            items      : [
                identitySettingsPanel,
                incomingMailPanel,
                outgoingMailPanel,
                commonSettingsPanel,
                signatureSettingsPanel,
                this.actionFolderMappingPanel
            ]
        });

        this.cardPanel = new Ext.Panel({
            layout         : 'card',
            deferredRender : false,
            bodyStyle      : 'background-color:#F6F6F6;',
            border         : false,
            activeItem     : 0,
            items          : [
                this.introPanel,
                this.mainTabPanel
            ]
        });

        /**
         * Button for adding an account
         */
        this.addAccountButton = new Ext.Button({
            text     : com.conjoon.Gettext.gettext("Add account..."),
            cls      : 'com-conjoon-margin-b-5',
            minWidth : 175,
            handler  : function(){

                var r = [], w, i;

                for (i in this.deletedRecords) {
                    r.push(this.deletedRecords[i]);
                }

                w = new com.conjoon.groupware.email.EmailAccountWizard({
                    pendingRemovedRecords : r
                });
                w.show();
            },
            scope : this
        });

        /**
         * Button for removing an account
         */
        this.setAccountAsStandardButton = new Ext.Button({
            text     : com.conjoon.Gettext.gettext("Use as standard"),
            cls      : 'com-conjoon-margin-b-5',
            minWidth : 175,
            disabled : true,
            handler  : this.onAccountAsStandard,
            scope    : this
        });

        /**
         * Button for removing an account
         */
        this.removeAccountButton = new Ext.Button({
            text     : com.conjoon.Gettext.gettext("Remove account"),
            minWidth : 175,
            disabled : true,
            handler  : this.onRemoveAccount,
            scope    : this
        });

        this.items = [{
            region    : 'west',
            bodyStyle : 'background:none',
            border    : false,
            width     : 175,
            margins   : '5 5 5 5',
            items     : [
                accountGridPanel,
                this.addAccountButton,
                this.setAccountAsStandardButton,
                this.removeAccountButton
            ]
        }, {
            layout    : 'fit',
            region    : 'center',
            bodyStyle : 'background:none',
            border    : false,
            margins   : '5 5 5 0',
            items     : [
                this.cardPanel
            ]
        }];

        this.modal = true;
        this.resizable = false;
        this.closable  = true;
        this.height    = 425;
        this.width     = 675;
        this.title     = com.conjoon.Gettext.gettext("Accounts");
        this.layout    = 'border';
        this.bodyStyle = 'background-color:#F6F6F6';

        this.okButton = new Ext.Button({
            text : com.conjoon.Gettext.gettext("OK"),
            minWidth : 75
        });

        this.cancelButton = new Ext.Button({
            text     : com.conjoon.Gettext.gettext("Cancel"),
            minWidth : 75
        });

        this.applyButton = new Ext.Button({
            text     : com.conjoon.Gettext.gettext("Apply"),
            disabled : true,
            minWidth : 75
        });

        this.defaultButton = this.okButton;

        this.buttons = [
            this.okButton,
            this.cancelButton,
            this.applyButton
        ];

        this.installListeners();
        com.conjoon.groupware.email.EmailAccountDialog.superclass.initComponent.call(this);
    },

// -------- validators
    /**
     * Checks the value for the serverInbox field.
     * Returns true if the value is not empty and not already in the
     * current account storage, otherwise false.
     *
     * @param {String} value The value for the server-inbox field
     * @return {Boolean} true, if the value was valid, otherwise false
     */
    isServerInboxValid : function(value)
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
                if (recs[i].id != this.clkRecord.id && recs[i].get('serverInbox').toLowerCase() === value) {
                    return false;
                }
            }

            return true;*/
        }


        return true;
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

    /**
     * Validator to check if the specified account name is valid. Will return false
     * if either the value defaults to empty or if the account name already exists.
     * This method will also look up the currently pending removed records to
     * see if there's a duplicate.
     *
     * @param {String} value The value to validate
     * @return {Boolean} true if the passed value was valid, otherwise false.
     */
    isAccountNameValid : function(value)
    {
        var value = value.trim().toLowerCase();
        if (value == "") {
            return false;
        }

        var recs = this.accountStore.getRange();

        var rec = null;
        var clkRecord = this.clkRecord;
        for (var i = 0, len = recs.length; i < len; i++) {
            var rec = recs[i];
            if (clkRecord && rec.id == clkRecord.id) {
                continue;
            }
            if (rec.get('name').trim().toLowerCase() == value) {
                return false;
            }
        }

        // check deleted
        var r = [], i;
        for (var i in this.deletedRecords) {
            if (this.deletedRecords[i].get('name').toLowerCase() == value) {
                return false;
            }
        }



        return true;
    },


// -------- utilities

    /**
     * Switches the state of this dialog between disabled/enabled.
     * A disabled dialog will have a load mask to prevent user input and mask all
     * dialog elements as disabled,s uch as the buttons and the close-icon.
     *
     * @param {Boolean} enabled true to enable the dialog, otherwise false.
     */
    switchDialogState : function(enabled)
    {
        this.showLoadMask(!enabled);

        this.okButton.setDisabled(!enabled);
        this.cancelButton.setDisabled(!enabled);
        this.applyButton.setDisabled(true);

        if (enabled) {
            this.tools['close'].unmask();
        } else {
            this.tools['close'].mask();
        }
    },

    /**
     * Rejects all modifications that have been made to the records in the
     * accountStore since the last commit operation.
     * This method is API intern called usually before any commit operation is
     * being made.
     * If an error occurs while updating data, this method will be called to
     * revert the changes, and another attempt to commit can be made.
     * The method will also update the current fields of the form to the restored
     * values.
     * Additionally, this method will set the first record in the grid as the
     * standard account, if no other record has the property is_standard set
     * to true.
     *
     * @param {Array} deletedToReject An array containg ids of the records
     * within deletedRecords to reject and add again to the store
     * @param {Array} updatedToReject An array containg ids of the records
     * within the store to reject their changes
     */
    rejectChanges : function(deletedToReject, updatedToReject)
    {
        var store = this.accountStore;

        if (deletedToReject) {
            for (var i = 0, max_i = deletedToReject.length; i < max_i; i++) {
                store.addSorted(this.deletedRecords[deletedToReject[i]]);
            }
        } else {
            for (var i in this.deletedRecords) {
                store.addSorted(this.deletedRecords[i]);
            }
        }
        var id = null;
        var rec = null;
        if (updatedToReject) {
            for (var i = 0, max_i = updatedToReject.length; i < max_i; i++) {
                rec = store.getById(updatedToReject[i]);
                if (rec) {
                    id = rec.id;
                    rec.reject();
                }
            }
        } else {
            store.rejectChanges();
        }

        this.deletedRecords = {};

        var standardIndex = store.find('isStandard', true);
        if (standardIndex == -1) {
            var rec = store.getAt(0);
            if (rec) {
                rec.set('isStandard', true);
                if (this.clkRecord && this.clkRecord.id == rec.id) {
                    this.setAccountAsStandardButton.setDisabled(true);
                }
            }
        }


    },

    /**
     * Shows the load mask for this dialog.
     *
     * @param {Boolean} show true to show the load mask, otherwise false.
     */
    showLoadMask : function(show)
    {
        if (show) {
            if (this.loadMask == null) {
                this.loadMask = new Ext.LoadMask(this.body, {
                    msg : com.conjoon.Gettext.gettext("Saving, please wait...")
                });
            }
            this.loadMask.show();
        } else {
            if (this.loadMask) {
                this.loadMask.hide();
            }
        }
    },


    /**
     * Dialog for representing an error.
     *
     * @param {String} errorType The error that caused the dialog to popup.
     */
    showErrorDialog : function(errorType)
    {
        var message = "";

        switch (errorType) {
            case 'name':
                message = com.conjoon.Gettext.gettext("Check the account name. It is either missing or the specified name does already exist for another account.");
            break;
            case 'userName':
                message = com.conjoon.Gettext.gettext("You have to specify your name for this account.");
            break;
            case 'address':
                message = com.conjoon.Gettext.gettext("The specified email address does not seem to be in a valid format.");
            break;
            case 'replyAddress':
                message = com.conjoon.Gettext.gettext("The specified reply email address does not seem to be in a valid format.");
            break;
            case 'serverInbox':
                message = com.conjoon.Gettext.gettext("You must specify the host for the inbox server.");
            break;
            case 'portInbox':
                message = com.conjoon.Gettext.gettext("You must specify a valid value for the port of the inbox server.");
            break;
            case 'usernameInbox':
                message = com.conjoon.Gettext.gettext("You must specify a user name for the inbox server.");
            break;
            case 'passwordInbox':
                message = com.conjoon.Gettext.gettext("You must specify a password for the inbox server.");
            break;
            case 'serverOutbox':
                message = com.conjoon.Gettext.gettext("You must specify the host for the outbox server.");
            break;
            case 'portOutbox':
                message = com.conjoon.Gettext.gettext("You must specify a valid value for the port of the outbox server.");
            break;
            case 'usernameOutbox':
                message = com.conjoon.Gettext.gettext("You must specify a user name for the outbox server.");
            break;
            case 'passwordOutbox':
                message = com.conjoon.Gettext.gettext("You must specify a password for the outbox server.");
            break;
            default:
                message = com.conjoon.Gettext.gettext("An unknown error occured.");
            break;
        }

        var msg = Ext.MessageBox;

        var fields = this.fields;
        var at;
        if (fields[errorType]) {
            at = fields[errorType].el.dom.id;
        }
        msg.show({
            title         : com.conjoon.Gettext.gettext("Error"),
            msg           : message,
            buttons       : msg.OK,
            icon          : msg.WARNING,
            animateTarget : at,
            cls           :'com-conjoon-msgbox-warning',
            width         : 400
        });
    },

    /**
     * Will check field values to determine if the properties entered by the user
     * are valid according to the specified validators of the field elements.
     *
     * @param {Boolean} showPopup Wether to show a message dialog given textual
     * information about the invalid field value.
     * @param {Boolean} activateTab Wether to set the active tab of the main tab panel
     * to the tab that holds the fields with the invalid values.
     */
    valuesValid : function(showPopup, activateTab)
    {
        var errorField = null;
        var tab = -1;
        var valid = true;

        var fields = this.fields;

        if (!fields['name'].isValid()) {
            errorField = 'name';
            tab = 0;
        } else if (!fields['userName'].isValid()) {
            errorField = 'userName';
            tab = 0;
        } else if (!fields['address'].isValid()) {
            errorField = 'address';
            tab = 0;
        } else if (!fields['replyAddress'].isValid()) {
            errorField = 'replyAddress';
            tab = 0;
        } else if (!fields['serverInbox'].isValid()) {
            errorField = 'serverInbox';
            tab = 1;
        } else if (!fields['portInbox'].isValid()) {
            errorField = 'portInbox';
            tab = 1;
        } else if (!fields['usernameInbox'].isValid()) {
            errorField = 'usernameInbox';
            tab = 1;
        } else if (!fields['passwordInbox'].isValid()) {
            errorField = 'passwordInbox';
            tab = 1;
        } else if (!fields['serverOutbox'].isValid()) {
            errorField = 'serverOutbox';
            tab = 2;
        } else if (!fields['portOutbox'].isValid()) {
            errorField = 'portOutbox';
            tab = 2;
        } else if (!fields['usernameOutbox'].isValid()) {
            errorField = 'usernameOutbox';
            tab = 2;
        } else if (!fields['passwordOutbox'].isValid()) {
            errorField = 'passwordOutbox';
            tab = 2;
        }

        if (errorField !== null) {
            if (activateTab) {
                this.cardPanel.getLayout().setActiveItem(1);
                this.mainTabPanel.setActiveTab(tab);
            }

            if (showPopup) {
                this.showErrorDialog.defer(0.001, this, [errorField]);
            }

            return false;
        }

        return true;
    },

    /**
     * Installs the listener for the components rendered into this window.
     */
    installListeners : function()
    {
        this.mon(this.okButton,     'click', this.onOkButton, this);
        this.mon(this.applyButton,  'click', this.onApplyButton, this);
        this.mon(this.cancelButton, 'click', this.onCancelButton, this);

        var selModel = this.accountGridPanel.selModel;
        this.mon(selModel, 'rowselect',       this.onRowSelect,       this);
        this.mon(selModel, 'beforerowselect', this.onBeforeRowSelect, this);
        this.mon(selModel, 'rowdeselect',     this.onRowDeselect,     this);

        var fields = this.fields;

        this.mon(fields['isOutboxAuth'], 'check', function(checkbox, checked) {

            fields['usernameOutbox'].setDisabled(!checked);
            fields['passwordOutbox'].setDisabled(!checked);
            fields['usernameOutbox'].allowBlank = !checked;
            fields['passwordOutbox'].allowBlank = !checked;
            fields['usernameOutbox'].isValid();
            fields['passwordOutbox'].isValid();

            if (!checked) {
                fields['usernameOutbox'].clearInvalid();
                fields['passwordOutbox'].clearInvalid();
            }

            this.onConfigChange();
        }, this);

        this.mon(fields['outboxConnectionTypeUnsecure'], 'check', function(radio, checked) {
            this.onConfigChange();
        }, this);
        this.mon(fields['outboxConnectionTypeSsl'], 'check', function(radio, checked) {
            this.onConfigChange();
        }, this);
        this.mon(fields['outboxConnectionTypeTls'], 'check', function(radio, checked) {
            this.onConfigChange();
        }, this);

        this.mon(fields['inboxConnectionTypeUnsecure'], 'check', function(radio, checked) {
            this.onConfigChange();
        }, this);
        this.mon(fields['inboxConnectionTypeSsl'], 'check', function(radio, checked) {
            this.onConfigChange();
        }, this);
        this.mon(fields['inboxConnectionTypeTls'], 'check', function(radio, checked) {
            this.onConfigChange();
        }, this);

        this.mon(fields['isSignatureUsed'], 'check', function(checkbox, checked) {
            fields['signature'].setDisabled(!checked);
            this.onConfigChange();
        }, this);

        this.mon(fields['isCopyLeftOnServer'], 'check', this.onConfigChange, this);
        this.mon(fields['hasSeparateFolderHierarchy'], 'check', this.onConfigChange, this);

        var lConfig = {
            keyup    : this.onConfigChange,
            keypress : this.onConfigChange,
            keydown  : this.onConfigChange,
            scope    : this
        };

        this.mon(fields['name'],           lConfig);
        this.mon(fields['userName'],       lConfig);
        this.mon(fields['address'],        lConfig);
        this.mon(fields['replyAddress'],   lConfig);
        this.mon(fields['serverInbox'],    lConfig);
        this.mon(fields['portInbox'],      lConfig);
        this.mon(fields['usernameInbox'],  lConfig);
        this.mon(fields['passwordInbox'],  lConfig);
        this.mon(fields['serverOutbox'],   lConfig);
        this.mon(fields['portOutbox'],     lConfig);
        this.mon(fields['usernameOutbox'], lConfig);
        this.mon(fields['passwordOutbox'], lConfig);
        this.mon(fields['signature'],      lConfig);

        this.mon(this.actionFolderMappingPanel, 'fieldchange', this.onConfigChange, this);

        this.mon(this.accountStore, 'update', this.onUpdate, this);

        this.on('beforeclose', this.onBeforeClose, this);
        this.on('show',        this._onShow,       this);

    },

// -------- saving/ AJAX callbacks

    /**
     * Callback for a succesfull ajax request. Successfull means that the server
     * could handle the request and that no connection problems occured.
     * The response may report a failure though, due to connection problems
     * to the database or similiar.
     * The response will hold an array value containing to arrays, indexed with
     * deleted_failed and updated_failed, each being an array containing all keys of
     * records that could not be deleted or updated.
     *
     * @param {Object} response The response object returned by the server.
     * @param {Object} options The options used to initiate the request.
     */
    onUpdateSuccess : function(response, options)
    {
        var json = com.conjoon.util.Json;

        // first off, check the response value property for being
        // an array.
        if (json.isError(response.responseText)) {
            this.onUpdateFailure(response, options);
            return;
        }

        // fetch the response value and check for indexes that could not be
        // deleted
        var responseValue = json.getResponseValues(response.responseText);

        var deletedFailed = responseValue['deletedFailed'],
            updatedFailed = responseValue['updatedFailed'],
            failureDel    = false,
            failureUpd    = false,
            failureBoth   = false,
            message       = "",
            accountStore  = this.accountStore,
            /**
             * folders which have been removed/created due to hasSeparateFolderHierarchy
             * setting.
             */
            createdLocalRootMailFolders = responseValue['createdLocalRootMailFolders'],
            removedLocalRootMailFolders = responseValue['removedLocalRootMailFolders'];

        if (deletedFailed.length > 0) {
            failureDel = true;
        }

        if (updatedFailed.length > 0) {
            failureUpd = true;
        }

        if (failureDel && failureUpd) {
            failureBoth = true;
        }

        if (failureBoth) {
            message = com.conjoon.Gettext.gettext("Some email accounts could neither be deleted nor updated. All changes have been reset.");
        } else if (failureDel) {
            message = com.conjoon.Gettext.gettext("Some email accounts could not be deleted. All changes have been reset.");
        } else if (failureUpd) {
            message = com.conjoon.Gettext.gettext("Some email accounts could not be upated. The changes have been reset.");
        }

        this.requestId = null;

        if (failureDel || failureUpd) {
            this.switchDialogState(true);
            this.rejectChanges(deletedFailed, updatedFailed);
            var msg  = Ext.MessageBox;
            msg.show({
                title   : com.conjoon.Gettext.gettext("Error"),
                msg     : message,
                buttons : msg.OK,
                icon    : msg.WARNING,
                cls     :'com-conjoon-msgbox-error',
                width   : 400
            });
            return;
        }

        if (!failureDel) {

            var accountId,
                localRootMailFolder,
                ind,
                accountRec;


            for (var i in removedLocalRootMailFolders) {

                if (!removedLocalRootMailFolders.hasOwnProperty(i)) {
                    continue;
                }

                accountId = parseInt(i, 10);

                ind = accountStore.findExact('id', accountId);

                if (ind !== -1) {
                    accountRec = accountStore.getAt(ind);
                    accountRec.set('localRootMailFolder', {});
                    Ext.ux.util.MessageBus.publish(
                        /**
                         * This message tells that an account record has lost its
                         * localRootMailFolder. the passed account is not guaranteed to
                         * hold the actual or new localRootMailFolder
                         */
                        'com.conjoon.groupware.email.account.localRootMailFolderRemoved',
                        {accountId : accountRec.get('id'),
                         // create new object, otherwise references will be passes
                        localRootMailFolder : Ext.apply({}, removedLocalRootMailFolders[i])}
                    );
                }
            }

            for (var i in createdLocalRootMailFolders) {

                if (!createdLocalRootMailFolders.hasOwnProperty(i)) {
                    continue;
                }

                accountId = parseInt(i, 10);

                ind = accountStore.findExact('id', accountId);
                if (ind !== -1) {
                    accountRec = accountStore.getAt(ind);
                    accountRec.set('localRootMailFolder', createdLocalRootMailFolders[i]);
                    Ext.ux.util.MessageBus.publish(
                        /**
                         * This message tells that an account record has gained a
                         * new localRootMailFolder. the passed account is not
                         * guaranteed to hold the actual or new localRootMailFolder
                         */
                        'com.conjoon.groupware.email.account.localRootMailFolderAdded',
                        {accountId : accountRec.get('id'),
                         // create new object, otherwise references will be passes
                         localRootMailFolder : Ext.apply({}, createdLocalRootMailFolders[i])}
                    );
                }
            }

            var delRecCopyData;

            for (var i in this.deletedRecords) {

                // create copy to prevent passing references
                delRecCopyData = this.deletedRecords[i].copy();
                delRecCopyData = delRecCopyData.data;
                delRecCopyData.localRootMailFolder =
                    Ext.apply({}, this.deletedRecords[i].get('localRootMailFolder'));

                Ext.ux.util.MessageBus.publish(
                    'com.conjoon.groupware.email.account.removed',
                    {account : delRecCopyData}
                );
            }

            this.deletedRecords = {};
        }

        accountStore.commitChanges();

        this.switchDialogState(true);
        if (options.closeAfterSuccess === true) {
            this.close();
        }
    },

    /**
     * Callback for an errorneous Ajax request. This method gets called
     * whenever the request couldn't be processed, due to connection or
     * server problems.
     *
     * @param {Object} response The response object returned by the server.
     * @param {Object} options The options used to initiate the request.
     */
    onUpdateFailure : function(response, options)
    {
        this.rejectChanges();
        this.switchDialogState(true);

        com.conjoon.groupware.ResponseInspector.handleFailure(response);

        this.requestId = null;
    },

    /**
     * Collects all modified and deleted records and sends them via AJAX
     * to the server to update the data model.
     *
     * @param {Boolean} closeAfterSuccess Wether the dialog should be closed
     * after an successfull update has been made
     */
    saveConfiguration : function(closeAfterSuccess)
    {
        // make sure the last selected record gets saved
        this.saveRecord();

        var records = this.accountStore.getModifiedRecords();
        var recordset = {deleted : [], updated : []};

        // merge deleted, if any
        var del = recordset.deleted;
        var deletedRecords = this.deletedRecords;
        var currDel = null;
        var checkDelCount = 0;
        for (var i in deletedRecords) {
            currDel = deletedRecords[i];
            del.push(currDel.id);
            checkDelCount++;
        }

        var upd = recordset.updated;
        var currUpd = null;
        for (var i = 0, max_i = records.length; i < max_i; i++) {
            currUpd = records[i];
            upd.push({
                id                   : currUpd.id,
                isStandard           : currUpd.get('isStandard'),
                name                 : currUpd.get('name'),
                userName             : currUpd.get('userName'),
                address              : currUpd.get('address'),
                replyAddress         : currUpd.get('replyAddress'),
                serverInbox          : currUpd.get('serverInbox'),
                portInbox            : currUpd.get('portInbox'),
                usernameInbox        : currUpd.get('usernameInbox'),
                passwordInbox        : currUpd.get('passwordInbox'),
                serverOutbox         : currUpd.get('serverOutbox'),
                portOutbox           : currUpd.get('portOutbox'),
                isOutboxAuth         : currUpd.get('isOutboxAuth'),
                usernameOutbox       : currUpd.get('usernameOutbox'),
                passwordOutbox       : currUpd.get('passwordOutbox'),
                isCopyLeftOnServer   : currUpd.get('isCopyLeftOnServer'),
                isSignatureUsed      : currUpd.get('isSignatureUsed'),
                signature            : currUpd.get('signature'),
                inboxConnectionType  : currUpd.get('inboxConnectionType'),
                outboxConnectionType : currUpd.get('outboxConnectionType'),
                folderMappings       : currUpd.get('folderMappings'),
                hasSeparateFolderHierarchy : currUpd.get('hasSeparateFolderHierarchy')
            });
        }

        // nothing to do?
        if (checkDelCount == 0 && max_i == 0) {
            if (closeAfterSuccess) {
                this.close();
            }
            return;
        }

        this.switchDialogState(false);

        this.requestId = Ext.Ajax.request({
            url               : './groupware/email.account/update.email.accounts/format/json',
            closeAfterSuccess : closeAfterSuccess,
            params            : {
                deleted : Ext.encode(recordset.deleted),
                updated : Ext.encode(recordset.updated)
            },
            success           : this.onUpdateSuccess,
            failure           : this.onUpdateFailure,
            scope             : this,
            disableCaching    : true
        });

    },

// -------- store listeners

    /**
     * Listener for the accountStore update event along with the operation
     * 'reject'. Will reset the form values of the current visible form if
     * the record rejected equals to the currently being displayed record.
     *
     * @param {Ext.data.Store} store
     * @param {com.conjoon.groupware.email.AccountRecord} record
     * @param {String} operation
     */
    onUpdate : function(store, record, operation)
    {
        if (operation == Ext.data.Record.REJECT) {
            var clkRecord = this.clkRecord;
            if (clkRecord && record.id == clkRecord.id) {
                this.fillFormFields(record);
            }
        }
    },


// -------- internal listeners
    /**
     * Checks if the current account store used by the logged in user is empty.
     * If the store is empty, an instance of com.conjoon.groupware.email.EmailAccountWizard
     * will be created and shown.
     *
     */
    _onShow : function()
    {
        var rec = this.accountStore.getRange();

        if (rec.length == 0) {
            var w = new com.conjoon.groupware.email.EmailAccountWizard();
            w.show();
        }
    },

    /**
     * Listener for a beforeclose operation.
     * Will return false if there is currently an ajax-request being made, otherwise
     * the changes made to the records in the store will be rejected.
     *
     * @return {Boolean} true to allow close operation, otherwise false
     */
    onBeforeClose : function()
    {
        if (this.requestId !== null) {
            return false;
        } else {
            this.rejectChanges();
            return true;
        }

    },

// -------- form elements key listeners
    /**
     * gets called whenever a config option changed. Renders the applyButton as enabled.
     */
    onConfigChange : function()
    {
        this.applyButton.setDisabled(false);
    },

// -------- window button listeners
    /**
     * Listener for the ok button
     */
    onOkButton : function(button, eventObject)
    {
        if (this.clkRecord === null || this.valuesValid(true, true)) {
            this.un('beforeclose', this.onBeforeClose, this);
            this.saveConfiguration(true);
        }
    },

    /**
     * Listener for the cancel button
     */
    onCancelButton : function(button, eventObject)
    {
        this.close();
    },

    /**
     * Listener for the apply button
     */
    onApplyButton : function(button, eventObject)
    {
        button.setDisabled(true);

        if (this.clkRecord === null || this.valuesValid(true, true)) {
            this.saveConfiguration();
        }
    },

// -------- grid button listener
    /**
     * listener for the removeAccountButton.
     * The method will be temporarily removed from the store and saved in
     * the deletedRecords . If an update-operation via AJAX fails (i.e. delteing
     * teh selected record server-side), the record will be appended to the store again.
     * Before deleting, all modifications done to this record will be rejected.
     * Additionally, the isStandard property will be set to false, and the first record
     * in the grid will have it's isStandard property set to true (in case the
     * isStandard property of the removed button equals to true).
     * The method will also check first if there is currently an email being edited that
     * uses the account that is about to be removed. If that is the case, a message box
     * will notify the user that the account is in use. The method will then return,
     * without deleting the record.
     *
     *
     * @param {Ext.Button} button The button that initiated a call to this method.
     */
    onRemoveAccount : function(button)
    {
        var record = this.clkRecord;

        if (!record) {
            return;
        }

        var msg  = Ext.MessageBox;

        // check if the account is currently being used by an email
        // being written / edited
        if (com.conjoon.groupware.email.EmailEditorManager.isAccountUsed(record.id)) {

            msg.show({
                title   : com.conjoon.Gettext.gettext("Remove email account - account is locked"),
                msg     : com.conjoon.Gettext.gettext("The account you want to delete is currently in use. Please finish all work related with this account first and then try again."),
                buttons : msg.OK,
                scope   : this,
                icon    : msg.INFO,
                cls     :'com-conjoon-msgbox-info',
                width   :400
            });

            return;
        }

        var msgTxt = "",
            folder = record.get('localRootMailFolder');

        if (folder && folder.type == 'root' || folder.type == 'root_remote') {
            msgTxt = String.format(com.conjoon.Gettext.gettext("Do you really want to remove the account \"{0}\"? <br />All related folders and messages which are stored locally will be removed, too!"), record.get('name'));
        } else {
            msgTxt = String.format(com.conjoon.Gettext.gettext("Do you really want to remove the account \"{0}\"?"), record.get('name'));
        }

        msg.show({
            title   : com.conjoon.Gettext.gettext("Remove email account"),
            msg     : msgTxt,
            buttons : msg.YESNO,
            fn      : function(b) {
                        if (b == 'yes') {
                            var store = this.accountStore;
                            var isStandard = record.get('isStandard');
                            record.reject(true);
                            var recordCopy = record.copy();

                            store.remove(record);

                            recordCopy.set('isStandard', false);

                            if (isStandard) {
                                var rec = store.getAt(0);
                                if (rec) {
                                    rec.set('isStandard', true);
                                }
                            }

                            this.deletedRecords[recordCopy.id] = recordCopy;
                            this.clkRowIndex = -1;
                            this.clkRecord   = null;
                            this.setAccountAsStandardButton.setDisabled(true);
                            this.removeAccountButton.setDisabled(true);
                            this.applyButton.setDisabled(false);
                            this.cardPanel.getLayout().setActiveItem(0);
                        }
            },
            scope   : this,
            icon    : msg.QUESTION,
            cls     :'com-conjoon-msgbox-question',
            width   :400
        });
    },

    /**
     * Listener for the setAccountAsStandard button. Tries to set the
     * selected record as standard.
     *
     * @param {Ext.Button} button The button that triggered a call to this method
     */
    onAccountAsStandard : function(button)
    {
        if (this.clkRecord == null) {
            return;
        }

        var store = this.accountStore;

        // find the record currently set as standard
        var index = store.find('isStandard', true);
        if (index != -1) {
            store.getAt(index).set('isStandard', false);
        }

        var rec = store.getById(this.clkRecord.id);
        if (rec) {
            rec.set('isStandard', true);
        }
        this.setAccountAsStandardButton.setDisabled(true);
        this.applyButton.setDisabled(false);
    },

// -------- grid listeners

    /**
     * Callback before a row is about to be selected
     *
     * @param {Ext.grid.RowSelectionModel} The current row selection model of the
     *                                     grid.
     * @param {Number} The rowIndex that represents the record that is about to
     *                 be selected in the grid.
     * @param {Boolean}
     * @param {Ext.data.Record} The record that is mapped to the selection
     *                          that is about to be made in the grid.
     */
    onBeforeRowSelect : function(selModel, rowIndex, keepExisting, record)
    {
        // check current selection. We can bubble the event and allow selection
        // without additional checks if no record is currently selected
        if (this.clkRowIndex == -1) {
            return true;
        }

        if (!this.valuesValid(true, true)) {
            return false;
        }

        // before we switch to another panel, set the last selected record fields
        // and update the grid's row according to the new field name.
        this.saveRecord();
    },

    /**
     * Callback when a row was selected. Fills the form elements with the according
     * data out of the record the selected item in the grid represents.
     * Will render the removeAccount-Button and setAsStandard-Button to enabled and
     * show the main tab panel if it was previously set to hidden.
     */
    onRowSelect : function(selModel, rowIndex, record)
    {
        if (this.clkRowIndex == rowIndex) {
            return;
        }

        this.clkRowIndex = rowIndex;
        this.clkRecord   = record;

        this.cardPanel.getLayout().setActiveItem(1);

        this.fillFormFields(record);

        this.setAccountAsStandardButton.setDisabled(this.clkRecord.get('isStandard'));
        this.removeAccountButton.setDisabled(false);

        if (this.mainTabPanel.getActiveTab() === this.actionFolderMappingPanel
            && record.get('protocol') != 'IMAP') {
            this.mainTabPanel.setActiveTab(0);
        }

        var prot = record.get('protocol');
        if (prot == 'IMAP') {
            this.actionFolderMappingPanel.setAccountRecord(record);
        }
        this.actionFolderMappingPanel.setDisabled(prot != 'IMAP');
    },

    /**
     * Callback for row deselection.
     * If the current data is detected to be invalid, the row won't be deselected
     * an the user will be informed that he has to input valid data.
     * The current deselected record will be passed to the onBeforeRowSelect
     * method and the current selected rowIndex will be reselected again,
     * surpressing all events. Thus, as long as the user did not enter a valid feed
     * name, the application focus on the record that is currently selected/wants to
     * be deselected.
     * Additionally, the setAsStandardButton and removeAccount button will re rendered
     * as disabled and the main tab panel will be hidden.
     */
    onRowDeselect : function(selModel, rowIndex, record)
    {
        if (!this.valuesValid(true, true)) {
            return false;
        }

        // save the last selected record before it gets deselected
        this.saveRecord();

        this.clkRowIndex = -1;
        this.clkRecord   = null;
        this.setAccountAsStandardButton.setDisabled(true);
        this.removeAccountButton.setDisabled(true);
        this.cardPanel.getLayout().setActiveItem(0);
        return true;
    },

// -------- data

    /**
     * Fills the form fields according to the data found in the passed
     * record.
     *
     * @param {com.conjoon.groupware.email.EmailAccountRecord} record The record
     * to visually represent with the form
     */
    fillFormFields : function(record)
    {
        var data   = record.data;
        var fields = this.fields;

        fields['isCopyLeftOnServer'].suspendEvents();
        fields['hasSeparateFolderHierarchy'].suspendEvents();
        fields['isOutboxAuth'].suspendEvents();
        fields['isSignatureUsed'].suspendEvents();

        fields['outboxConnectionTypeUnsecure'].suspendEvents();
        fields['outboxConnectionTypeSsl'].suspendEvents();
        fields['outboxConnectionTypeTls'].suspendEvents();

        fields['inboxConnectionTypeUnsecure'].suspendEvents();
        fields['inboxConnectionTypeSsl'].suspendEvents();
        fields['inboxConnectionTypeTls'].suspendEvents();

        fields['usernameOutbox'].allowBlank = !data.isOutboxAuth;
        fields['passwordOutbox'].allowBlank = !data.isOutboxAuth;
        fields['name'].setValue(data.name);
        fields['userName'].setValue(data.userName);
        fields['address'].setValue(data.address);
        fields['replyAddress'].setValue(data.replyAddress);
        fields['protocol1'].setValue(data.protocol);
        fields['serverInbox'].setValue(data.serverInbox);
        fields['portInbox'].setValue(data.portInbox);
        fields['usernameInbox'].setValue(data.usernameInbox);
        fields['passwordInbox'].setValue(data.passwordInbox);
        fields['serverOutbox'].setValue(data.serverOutbox);
        fields['portOutbox'].setValue(data.portOutbox);
        fields['isOutboxAuth'].setValue(data.isOutboxAuth);
        fields['usernameOutbox'].setValue(data.usernameOutbox);
        fields['passwordOutbox'].setValue(data.passwordOutbox);
        fields['isCopyLeftOnServer'].setValue(!data.isCopyLeftOnServer);
        fields['hasSeparateFolderHierarchy'].setValue(
            data.localRootMailFolder.type === 'root'
        );

        if (data.outboxConnectionType == 'SSL') {
            fields['outboxConnectionTypeSsl'].setValue(true);
        } else if (data.outboxConnectionType == 'TLS') {
            fields['outboxConnectionTypeTls'].setValue(true);
        } else {
            fields['outboxConnectionTypeUnsecure'].setValue(true);
        }

        if (data.inboxConnectionType == 'SSL') {
            fields['inboxConnectionTypeSsl'].setValue(true);
        } else if (data.inboxConnectionType == 'TLS') {
            fields['inboxConnectionTypeTls'].setValue(true);
        } else {
            fields['inboxConnectionTypeUnsecure'].setValue(true);
        }

        if (data.protocol === 'IMAP') {
            fields['isCopyLeftOnServer'].setDisabled(true);
            fields['hasSeparateFolderHierarchy'].setDisabled(true);
        } else {
            fields['isCopyLeftOnServer'].setDisabled(false);
            fields['hasSeparateFolderHierarchy'].setDisabled(false);
        }

        fields['isSignatureUsed'].setValue(data.isSignatureUsed);
        fields['signature'].setValue(data.signature);

        fields['signature'].setDisabled(!data.isSignatureUsed);
        fields['usernameOutbox'].setDisabled(!data.isOutboxAuth);
        fields['passwordOutbox'].setDisabled(!data.isOutboxAuth);

        fields['outboxConnectionTypeUnsecure'].resumeEvents();
        fields['outboxConnectionTypeSsl'].resumeEvents();
        fields['outboxConnectionTypeTls'].resumeEvents();

        fields['inboxConnectionTypeUnsecure'].resumeEvents();
        fields['inboxConnectionTypeSsl'].resumeEvents();
        fields['inboxConnectionTypeTls'].resumeEvents();

        fields['isCopyLeftOnServer'].resumeEvents();
        fields['hasSeparateFolderHierarchy'].resumeEvents();
        fields['isOutboxAuth'].resumeEvents();
        fields['isSignatureUsed'].resumeEvents();
    },


    /**
     * Saves the configuration for the last selected record.
     *
     * Passwords will only be modified if they do not equal to their default value,
     * which is a string "*" according to the length of the original value.
     *
     * @todo do not rely on a string of "*" - better update the value if a change in the
     * field was detected
     */
    saveRecord : function()
    {
        if (this.clkRecord == null) {
            return;
        }

        var fields = this.fields;
        var record = this.clkRecord;

        var isOutboxAuth = fields['isOutboxAuth'].getValue();

        record.set('name',               fields['name'].getValue());
        record.set('userName',           fields['userName'].getValue());
        record.set('address',            fields['address'].getValue());
        record.set('replyAddress',       fields['replyAddress'].getValue());
        record.set('serverInbox',        fields['serverInbox'].getValue());
        record.set('portInbox',          fields['portInbox'].getValue());
        record.set('usernameInbox',      fields['usernameInbox'].getValue());
        record.set('serverOutbox',       fields['serverOutbox'].getValue());
        record.set('portOutbox',         fields['portOutbox'].getValue());
        record.set('isOutboxAuth',       isOutboxAuth);
        record.set('isCopyLeftOnServer', !fields['isCopyLeftOnServer'].getValue());
        record.set('hasSeparateFolderHierarchy', fields['hasSeparateFolderHierarchy'].getValue());
        record.set('isSignatureUsed',    fields['isSignatureUsed'].getValue());
        record.set('signature',          fields['signature'].getValue());

        var passwordInbox  = fields['passwordInbox'].getValue();
        var passwordOutbox = fields['passwordOutbox'].getValue();

        if (!isOutboxAuth) {
            record.set('usernameOutbox',  "");
            passwordOutbox = "";
        } else {
            record.set('usernameOutbox',  fields['usernameOutbox'].getValue());
        }

        if (fields['outboxConnectionTypeSsl'].getValue() === true) {
            record.set('outboxConnectionType', 'SSL');
        } else if (fields['outboxConnectionTypeTls'].getValue() === true) {
            record.set('outboxConnectionType', 'TLS');
        } else {
            record.set('outboxConnectionType', "");
        }

        if (fields['inboxConnectionTypeSsl'].getValue() === true) {
            record.set('inboxConnectionType', 'SSL');
        } else if (fields['inboxConnectionTypeTls'].getValue() === true) {
            record.set('inboxConnectionType', 'TLS');
        } else {
            record.set('inboxConnectionType', "");
        }

        if (passwordInbox.trim() == "" || passwordInbox.replace(/\*/g, '').trim() != "") {
            passwordInbox = passwordInbox.trim() == "" ? "" : passwordInbox;
            record.set('passwordInbox',  passwordInbox);
        }

        if (passwordOutbox.trim() == "" || passwordOutbox.replace(/\*/g, '').trim() != "") {
            passwordOutbox = passwordOutbox.trim() == "" ? "" : passwordOutbox;
            record.set('passwordOutbox', passwordOutbox);
        }

        if (record.get('protocol') == 'IMAP') {
            this.actionFolderMappingPanel.saveToRecord(record);
        }

        this.modifiedRecordCount = this.accountStore.getModifiedRecords().length;
    }



});