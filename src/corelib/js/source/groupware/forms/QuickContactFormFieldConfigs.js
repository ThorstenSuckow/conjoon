Ext.namespace('de.intrabuild.groupware.forms');

de.intrabuild.groupware.forms.QuickContactFormFieldConfigs = {};

// shorthand
var fields = de.intrabuild.groupware.forms.QuickContactFormFieldConfigs;


fields.textField = {
    
    firstname :{
        fieldLabel: 'Vorname',
        name: 'first',
        emptyText:'<Vorname>',
        anchor: '100%'
    },
    
    
    lastname : {
        fieldLabel: 'Nachname',
        emptyText:'<Nachname>',
        name: 'true',
        anchor: '100%'
        
    },
    
    
    email : {
        fieldLabel: 'Email',
        name: 'email',
        emptyText:'<Email-Adresse>',
        vtype:'email',
        anchor: '100%'
    }
    
};

fields.checkbox = {
    
    switchToEdit :{
        boxLabel: 'zum Bearbeiten wechseln'
    }
};

fields.button = {
    
    save : {
        text : 'Speichern'
    },
    
    cancel : {
        text : 'Abbrechen'    
    }
}

delete fields;