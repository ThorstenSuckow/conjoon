Ext.namespace('de.intrabuild.groupware.forms');

de.intrabuild.groupware.forms.QuickEmailFormFieldConfigs = {};

// shorthand
var fields = de.intrabuild.groupware.forms.QuickEmailFormFieldConfigs;


fields.textField = {
    
    emailAddress :{
        fieldLabel: 'An',
        name: 'emailaddress',
        emptyText: '<Email-Adrese>',
         vtype:'email',
        anchor: '100%'
    },
    
    subject : {
        fieldLabel: 'Betreff',
        emptyText: '<Betreff>',
        name: 'subject',
        anchor: '100%'
        
    }
};

fields.textArea = {
    
    message :{
        emptyText: '<Nachricht>',
        fieldLabel:'Nachricht',
        anchor: '100% -40'
    }
};

fields.button = {
    
    save : {
        text : 'Senden'
    },
    
    cancel : {
        text : 'Abbrechen'    
    }
}

delete fields;