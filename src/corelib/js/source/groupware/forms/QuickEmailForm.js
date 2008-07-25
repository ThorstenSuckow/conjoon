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
    
    
    var _emailAddress = null;
    var _subject      = null;
    var _message      = null;
    
    var _submitButton = null;
    var _cancelButton = null;
    
    var _form = null;
    
    var _createLayout = function() 
    {   
        _form.add(_emailAddress);   
        _form.add(_subject);
        _form.add(_message);
        
        _form.addButton(_saveButton);
        _form.addButton(_cancelButton);
        
    };
    
    var _installListeners = function()
    {
        
    };
    
    var _initComponents = function()
    {
        _emailAddress = new Ext.form.TextField(_textFieldConfig.emailAddress);
        _subject      = new Ext.form.TextField(_textFieldConfig.subject);
        
        _message = new Ext.form.TextArea(_textAreaConfig.message);
        
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
                title: de.intrabuild.Gettext.gettext("Email"),
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

