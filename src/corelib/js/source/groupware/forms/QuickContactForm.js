Ext.namespace('de.intrabuild.groupware.forms');

/**
 * de.intrabuild.groupware.forms.QuickContactFormFieldConfigs
 * de.intrabuild.groupware.forms.QuickEmailFormFieldConfigs
 */

de.intrabuild.groupware.forms.QuickContactForm = function() {
    
    // shorthands
    var _fieldConfigs    = de.intrabuild.groupware.forms.QuickContactFormFieldConfigs;
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
        _switchToEdit.ctCls = 'de-intrabuild-groupware-quickpanel-SmallEditorFont';
        
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
                labelWidth: 0,
                frame:false,
                labelAlign:'left',
                title: 'Kontakt',
                //width: 220,
                bodyStyle:'background:#DFE8F6;padding:5px;',
                cls: 'x-small-editor',
                labelPad: 0,
                defaultType: 'textfield',
                hideLabels:true  
            });
            
            _form.on('beforerender', _initComponents, this);   
            
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