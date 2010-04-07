/**
 * conjoon
 * (c) 2002-2010 siteartwork.de/conjoon.org
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

Ext.namespace('com.conjoon.groupware.workbench.tools');

/**
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 *
 * @class com.conjoon.groupware.workbench.tools.SuggestionDialog
 * @extends Ext.ux.Wiz
 */
com.conjoon.groupware.workbench.tools.SuggestionDialog = Ext.extend(Ext.ux.Wiz, {

    /**
     * Inits this component.
     */
    initComponent : function()
    {
        Ext.apply(this, {
            finishButtonText : 'Send Suggestion',
            height           : 450,
            width            : 480,
            cls              : 'com-conjoon-groupware-workbench-tools-SuggestionDialog',
            title            : com.conjoon.Gettext.gettext("Make a Suggestion"),
            headerConfig     : {
                title : com.conjoon.Gettext.gettext("Submit a request for enhancing the functionality")
            },
            cards  : [
                new com.conjoon.groupware.workbench.tools.suggestion.WelcomeCard(),
                new com.conjoon.groupware.workbench.tools.suggestion.DataCard()
            ]
        });

        com.conjoon.groupware.workbench.tools.SuggestionDialog.superclass.initComponent.call(this);
    },

    /**
     * Callback for the "finish" button. Collects all form values and sends them
     * to the server.
     */
    onFinish : function()
    {
        var values = {};
        var formValues = {};
        for (var i = 0, len = this.cards.length; i < len; i++) {
            formValues = this.cards[i].form.getValues(false);
            for (var a in formValues) {
                values[a] = formValues[a];
            }
        }

        if (values['public']) {
            values['public'] = 1;
        } else {
            values['public'] = 0;
        }

        Ext.Ajax.request({
            url    : './default/index/post.suggestion/format/json',
            params : values
        });

        this.close();
    }

});