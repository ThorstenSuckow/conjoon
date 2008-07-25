Ext.namespace('de.intrabuild.groupware.forms');

de.intrabuild.groupware.forms.QuickContactFormFieldConfigs = {};

// shorthand
var fields = de.intrabuild.groupware.forms.QuickContactFormFieldConfigs;


fields.textField = {
    
    firstname :{
        fieldLabel: de.intrabuild.Gettext.gettext("First name"),
        name: 'first',
        emptyText:de.intrabuild.Gettext.gettext("<First name>"),
        anchor: '100%'
    },
    
    
    lastname : {
        fieldLabel: de.intrabuild.Gettext.gettext("Last name"),
        emptyText:de.intrabuild.Gettext.gettext("<Last name>"),
        name: 'true',
        anchor: '100%'
        
    },
    
    
    email : {
        fieldLabel: de.intrabuild.Gettext.gettext("Email"),
        name: 'email',
        emptyText:de.intrabuild.Gettext.gettext("<Email address>"),
        vtype:'email',
        anchor: '100%'
    }
    
};

fields.checkbox = {
    
    switchToEdit :{
        boxLabel: de.intrabuild.Gettext.gettext("switch to edit mode")
    }
};

fields.button = {
    
    save : {
        text : de.intrabuild.Gettext.gettext("Save")
    },
    
    cancel : {
        text : de.intrabuild.Gettext.gettext("Cancel")    
    }
}

delete fields;