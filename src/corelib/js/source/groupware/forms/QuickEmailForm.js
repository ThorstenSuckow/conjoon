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

Ext.namespace('de.intrabuild.groupware.forms');

/**
 * de.intrabuild.groupware.forms.QuickContactFormFieldConfigs
 * de.intrabuild.groupware.forms.QuickEmailFormFieldConfigs
 */

de.intrabuild.groupware.forms.QuickEmailForm = function() {

    // shorthands
    var _fieldConfigs    = de.intrabuild.groupware.forms.QuickEmailFormFieldConfigs;
    var _textFieldConfig = _fieldConfigs.textField;
    var _textAreaConfig  = _fieldConfigs.textArea;
    var _buttonConfig    = _fieldConfigs.button;


    var _form = null;


    return {

        getComponent : function()
        {
            if (_form !== null) {
                return _form;
            }

            var decorate  = de.intrabuild.groupware.email.decorator.AccountActionComp.decorate;
            _submitButton = decorate(new Ext.Button(_buttonConfig.save));
            _cancelButton = decorate(new Ext.Button(_buttonConfig.cancel));

            _form = new Ext.FormPanel({
                labelWidth: 0,
                frame:false,
                labelAlign:'left',
                title: de.intrabuild.Gettext.gettext("Email"),
                bodyStyle:'background:#DFE8F6;padding:5px;',
                cls: 'x-small-editor',
                labelPad: 0,
                defaultType: 'textfield',
                hideLabels:true,
                items : [
                    new Ext.form.TextField(_textFieldConfig.emailAddress),
                    new Ext.form.TextField(_textFieldConfig.subject),
                    new Ext.form.TextArea(_textAreaConfig.message)
                ],
                buttons : [
                    decorate(new Ext.Button(_buttonConfig.save)),
                    decorate(new Ext.Button(_buttonConfig.cancel))
                ]

            });

             return _form;
        }


    };


}();

