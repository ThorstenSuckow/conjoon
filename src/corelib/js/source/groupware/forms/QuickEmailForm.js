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

Ext.namespace('com.conjoon.groupware.forms');

/**
 * com.conjoon.groupware.forms.QuickContactFormFieldConfigs
 * com.conjoon.groupware.forms.QuickEmailFormFieldConfigs
 */

com.conjoon.groupware.forms.QuickEmailForm = function() {

    var subjectField   = null;
    var recipientField = null;
    var messageField   = null;

    var cancelButton = null;
    var submitButton = null;

    var _form = null;

    var getSubmitButton = function()
    {
        return new Ext.Button({
            text     : com.conjoon.Gettext.gettext("Send now"),
            formBind : true,
            handler  : send,
            scope    : com.conjoon.groupware.forms.QuickEmailForm
        });
    };

    var getCancelButton = function()
    {
        return new Ext.Button({
            text    : com.conjoon.Gettext.gettext("Cancel"),
            handler : com.conjoon.groupware.forms.QuickEmailForm.reset,
            scope   : com.conjoon.groupware.forms.QuickEmailForm
        });
    };

    var getSubjectField = function()
    {
        return new Ext.form.TextField({
            fieldLabel : com.conjoon.Gettext.gettext("Subject"),
            emptyText  : com.conjoon.Gettext.gettext("<Subject>"),
            name       : 'subject',
            anchor     : '100%'
        });
    };

    var getRecipientField = function()
    {
        return new com.conjoon.groupware.email.form.RecipientComboBox({
            anchor       : '100%',
            allowBlank   : false,
            emptyText    : com.conjoon.Gettext.gettext("<Email address>"),
            vtype        : 'email',
            displayField : 'address'
        });
    };

    var getMessageField = function()
    {
        return new Ext.form.TextArea({
            emptyText  : com.conjoon.Gettext.gettext("<Message>"),
            fieldLabel : com.conjoon.Gettext.gettext("Message"),
            anchor     : '100% -40'
        });
    };

    /**
     * Prepares the data to be sent by com.conjoon.groupware.email.Dispatcher
     *
     * @return {com.conjoon.groupware.email.data.Draft}
     */
    var prepareData = function()
    {
        var to  = [];
        var cc  = [];
        var bcc = [];

        var sngl = com.conjoon.groupware.forms.QuickEmailForm;

        to.push(sngl.getRecipient());

        var accountId = com.conjoon.groupware.email.AccountStore.getStandardAccount().id;

        var params = {
            format                   : 'text/plain', // can be 'text/plain', 'text/html' or 'multipart'
            id                       : -1,
            referencesId             : -1,
            type                     : 'new',
            inReplyTo                : '',
            references               : '',
            date                     : Math.floor((new Date().getTime())/1000),
            subject                  : sngl.getSubject(),
            message                  : sngl.getMessage(),
            to                       : Ext.encode(to),
            cc                       : '',
            bcc                      : '',
            groupwareEmailFoldersId  : -1,
            groupwareEmailAccountsId : accountId,
            /**
             * @ticket CN-897
             * This might be better placed in a service which returns or fills
             * an array with default values the MailSender in the backend
             * expects
             */
            path                     : '[]',
            referencedData           : '{"uId" : -1, "path" : []}',
            removedAttachments       : '[]',
            attachments              : '[]'
        };

        var rec = new com.conjoon.groupware.email.data.Draft(
            params, params.id
        );

        return rec;
    };

    var send = function()
    {
        var draftRecord = prepareData();

        com.conjoon.groupware.email.Dispatcher.sendEmail(
            draftRecord, null, {
                panelId : _form.getId(),
                setSubjectCallback : {
                    fn    : com.conjoon.groupware.forms.QuickEmailForm.setSubject,
                    scope : com.conjoon.groupware.forms.QuickEmailForm
                }
            }, true
        );
    };

    var sentFailure = function(subject, message)
    {
        if (message.options.panelId == _form.getId()) {
            _form.el.unmask();
            com.conjoon.groupware.forms.QuickEmailForm.reset();
        }
    };

    var beforeSent = function(subject, message)
    {
        if (message.options.panelId == _form.getId()) {
            _form.el.mask(
                com.conjoon.Gettext.gettext("Sending..."),
                'x-mask-loading'
            );
        }
    };

    var sentSuccess = function(subject, message)
    {
        if (message.options.panelId == _form.getId()) {
            _form.el.unmask();
            com.conjoon.groupware.forms.QuickEmailForm.reset();
        }
    };

    Ext.ux.util.MessageBus.subscribe(
        'com.conjoon.groupware.email.Smtp.emailSent',
        sentSuccess
    );
    Ext.ux.util.MessageBus.subscribe(
        'com.conjoon.groupware.email.Smtp.emailSentFailure',
        sentFailure
    );
    Ext.ux.util.MessageBus.subscribe(
        'com.conjoon.groupware.email.Smtp.beforeEmailSent',
        beforeSent
    );

    return {

        setSubject : function(value)
        {
            this.setValueFor('subject', value);
        },

        getSubject : function()
        {
            return this.getValueFor('subject');
        },

        getRecipient : function()
        {
            return this.getValueFor('recipient');
        },

        getMessage : function()
        {
            return this.getValueFor('message');
        },

        setValueFor : function(type, value)
        {
            if (!_form) {
                return "";
            }

            switch (type) {
                case 'subject':
                    subjectField.setValue(value);
                break;
            }
        },

        getValueFor : function(type)
        {
            if (!_form) {
                return "";
            }

            switch (type) {
                case 'subject':
                    return subjectField.getValue();
                case 'recipient':
                    return recipientField.getValue();
                case 'message':
                    return messageField.getValue();
                default:
                    return "";
            }
        },

        reset : function()
        {
            if (!_form) {
                return;
            }

            subjectField.blur();
            recipientField.blur();
            messageField.blur();
            _form.form.reset();

        },

        getComponent : function()
        {
            if (_form !== null) {
                return _form;
            }

            var decorate  = com.conjoon.groupware.email.decorator.AccountActionComp.decorate;

            subjectField   = getSubjectField();
            recipientField = getRecipientField();
            messageField   = getMessageField();

            cancelButton = getCancelButton();
            submitButton = getSubmitButton();


            _form = new Ext.FormPanel({
                labelWidth   : 0,
                monitorValid : true,
                frame        : false,
                buttonAlign  : 'center',
                labelAlign   : 'left',
                title        : com.conjoon.Gettext.gettext("Email"),
                bodyStyle    : 'background:#DFE8F6;padding:5px;',
                cls          : 'x-small-editor',
                labelPad     : 0,
                defaultType  : 'textfield',
                hideLabels   : true,
                items        : [
                    recipientField,
                    subjectField,
                    messageField
                ],
                buttons : [
                    decorate(submitButton),
                    decorate(cancelButton)
                ]

            });

             return _form;
        }


    };


}();

