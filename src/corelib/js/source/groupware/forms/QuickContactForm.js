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

com.conjoon.groupware.forms.QuickContactForm = function() {

    // shorthands
    var _fieldConfigs    = com.conjoon.groupware.forms.QuickContactFormFieldConfigs;
    var _textFieldConfig = _fieldConfigs.textField;
    var _buttonConfig    = _fieldConfigs.button;
    var _checkboxConfig  = _fieldConfigs.checkbox;

    var _firstname = null;
    var _lastname = null;
    var _emailAddress = null;

    var _switchToEdit = null;

    var _submitButton = null;
    var _cancelButton = null;

    var _form = null;

    var _createLayout = function()
    {
        _form.add(_firstname);
        _form.add(_lastname);
        _form.add(_emailAddress);

        _form.add(_switchToEdit);

        _form.addButton(_saveButton);
        _form.addButton(_cancelButton);

    };

    var _installListeners = function()
    {

    };

    var _initComponents = function()
    {
        _firstname    = new Ext.form.TextField(_textFieldConfig.firstname);
        _lastname     = new Ext.form.TextField(_textFieldConfig.lastname);
        _emailAddress = new Ext.form.TextField(_textFieldConfig.email);

        _switchToEdit = new Ext.form.Checkbox(_checkboxConfig.switchToEdit);
        _switchToEdit.ctCls = 'com-conjoon-groupware-quickpanel-SmallEditorFont';

        _saveButton   = new Ext.Button(_buttonConfig.save);
        _cancelButton = new Ext.Button(_buttonConfig.cancel);

        _installListeners.call(this);
        _createLayout.call(this);
    };

    return {

        getComponent : function()
        {
            if (_form !== null) {
                return _form;
            }

             _form = new Ext.FormPanel({
                labelWidth  : 0,
                buttonAlign : 'center',
                frame       : false,
                labelAlign  : 'left',
                title       : com.conjoon.Gettext.gettext("Contact"),
                bodyStyle   : 'background:#DFE8F6;padding:5px;',
                cls         : 'x-small-editor',
                labelPad    : 0,
                defaultType : 'textfield',
                hideLabels  : true
            });

            _form.on('beforerender', _initComponents, this, {single : true});

            return _form;
        },


        render : function()
        {
            if (_form.rendered) {
                return;
            }

            _form.render();

        }


    };


}();