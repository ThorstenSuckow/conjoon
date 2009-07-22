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

    // shorthands
    var _fieldConfigs    = com.conjoon.groupware.forms.QuickEmailFormFieldConfigs;
    var _textFieldConfig = _fieldConfigs.textField;
    var _textAreaConfig  = _fieldConfigs.textArea;
    var _buttonConfig    = _fieldConfigs.button;

    var subjectField   = null;
    var recipientField = null;
    var messageField   = null;

    var _form = null;


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

            subjectField   = new Ext.form.TextField(_textFieldConfig.subject);
            recipientField = new Ext.form.TextField(_textFieldConfig.emailAddress);
            messageField   = new Ext.form.TextArea(_textAreaConfig.message);

            var cancelConfig = Ext.apply(_buttonConfig.cancel, {
                handler : this.reset,
                scope   : this
            });

            _form = new Ext.FormPanel({
                labelWidth  : 0,
                frame       : false,
                buttonAlign : 'center',
                labelAlign  : 'left',
                title       : com.conjoon.Gettext.gettext("Email"),
                bodyStyle   : 'background:#DFE8F6;padding:5px;',
                cls         : 'x-small-editor',
                labelPad    : 0,
                defaultType : 'textfield',
                hideLabels  : true,
                items       : [
                    recipientField,
                    subjectField,
                    messageField
                ],
                buttons : [
                    decorate(new Ext.Button(_buttonConfig.save)),
                    decorate(new Ext.Button(cancelConfig))
                ]

            });

             return _form;
        }


    };


}();

