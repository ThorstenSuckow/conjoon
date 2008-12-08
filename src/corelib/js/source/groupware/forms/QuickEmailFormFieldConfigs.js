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

com.conjoon.groupware.forms.QuickEmailFormFieldConfigs = {};

// shorthand
var fields = com.conjoon.groupware.forms.QuickEmailFormFieldConfigs;


fields.textField = {

    emailAddress :{
        fieldLabel: com.conjoon.Gettext.gettext("To"),
        name: 'emailaddress',
        emptyText: com.conjoon.Gettext.gettext("<Email address>"),
         vtype:'email',
        anchor: '100%'
    },

    subject : {
        fieldLabel: com.conjoon.Gettext.gettext("Subject"),
        emptyText: com.conjoon.Gettext.gettext("<Subject>"),
        name: 'subject',
        anchor: '100%'

    }
};

fields.textArea = {

    message :{
        emptyText: com.conjoon.Gettext.gettext("<Message>"),
        fieldLabel:com.conjoon.Gettext.gettext("Message"),
        anchor: '100% -40'
    }
};

fields.button = {

    save : {
        text : com.conjoon.Gettext.gettext("Send now")
    },

    cancel : {
        text : com.conjoon.Gettext.gettext("Cancel")
    }
}

delete fields;