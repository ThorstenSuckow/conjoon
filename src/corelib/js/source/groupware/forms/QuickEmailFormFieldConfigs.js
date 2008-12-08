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

de.intrabuild.groupware.forms.QuickEmailFormFieldConfigs = {};

// shorthand
var fields = de.intrabuild.groupware.forms.QuickEmailFormFieldConfigs;


fields.textField = {

    emailAddress :{
        fieldLabel: de.intrabuild.Gettext.gettext("To"),
        name: 'emailaddress',
        emptyText: de.intrabuild.Gettext.gettext("<Email address>"),
         vtype:'email',
        anchor: '100%'
    },

    subject : {
        fieldLabel: de.intrabuild.Gettext.gettext("Subject"),
        emptyText: de.intrabuild.Gettext.gettext("<Subject>"),
        name: 'subject',
        anchor: '100%'

    }
};

fields.textArea = {

    message :{
        emptyText: de.intrabuild.Gettext.gettext("<Message>"),
        fieldLabel:de.intrabuild.Gettext.gettext("Message"),
        anchor: '100% -40'
    }
};

fields.button = {

    save : {
        text : de.intrabuild.Gettext.gettext("Send now")
    },

    cancel : {
        text : de.intrabuild.Gettext.gettext("Cancel")
    }
}

delete fields;