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

com.conjoon.groupware.forms.QuickContactFormFieldConfigs = {};

// shorthand
var fields = com.conjoon.groupware.forms.QuickContactFormFieldConfigs;


fields.textField = {

    firstname :{
        fieldLabel: com.conjoon.Gettext.gettext("First name"),
        name: 'first',
        emptyText:com.conjoon.Gettext.gettext("<First name>"),
        anchor: '100%'
    },


    lastname : {
        fieldLabel: com.conjoon.Gettext.gettext("Last name"),
        emptyText:com.conjoon.Gettext.gettext("<Last name>"),
        name: 'true',
        anchor: '100%'

    },


    email : {
        fieldLabel: com.conjoon.Gettext.gettext("Email"),
        name: 'email',
        emptyText:com.conjoon.Gettext.gettext("<Email address>"),
        vtype:'email',
        anchor: '100%'
    }

};

fields.checkbox = {

    switchToEdit :{
        boxLabel: com.conjoon.Gettext.gettext("switch to edit mode")
    }
};

fields.button = {

    save : {
        text : com.conjoon.Gettext.gettext("Save")
    },

    cancel : {
        text : com.conjoon.Gettext.gettext("Cancel")
    }
}

delete fields;