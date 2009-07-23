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
            formBind : true
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
        return new Ext.form.TextField({
            fieldLabel : com.conjoon.Gettext.gettext("To"),
            name       : 'emailaddress',
            emptyText  : com.conjoon.Gettext.gettext("<Email address>"),
            vtype      : 'email',
            anchor     : '100%',
            allowBlank : false
        })
    };

    var getMessageField = function()
    {
        return new Ext.form.TextArea({
            emptyText  : com.conjoon.Gettext.gettext("<Message>"),
            fieldLabel : com.conjoon.Gettext.gettext("Message"),
            anchor     : '100% -40'
        });
    };



    return {

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

