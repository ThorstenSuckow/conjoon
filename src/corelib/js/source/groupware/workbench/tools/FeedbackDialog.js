/**
 * conjoon
 * (c) 2002-2012 siteartwork.de/conjoon.org
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
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 *
 * @class com.conjoon.groupware.workbench.tools.FeedbackDialog
 * @extends Ext.ux.Wiz
 */
com.conjoon.groupware.workbench.tools.FeedbackDialog = Ext.extend(Ext.ux.Wiz, {

    /**
     * Inits this component.
     */
    initComponent : function()
    {
        Ext.apply(this, {
            finishButtonText : 'Send Feedback',
            height           : 475,
            width            : 480,
            cls              : 'com-conjoon-groupware-workbench-tools-FeedbackDialog',
            title            : com.conjoon.Gettext.gettext("Provide Feedback"),
            headerConfig     : {
                title : com.conjoon.Gettext.gettext("Your feedback helps us improve conjoon!")
            },
            cards  : [
                new com.conjoon.groupware.workbench.tools.feedback.WelcomeCard(),
                new com.conjoon.groupware.workbench.tools.feedback.DataCard()
            ]
        });

        com.conjoon.groupware.workbench.tools.FeedbackDialog.superclass.initComponent.call(this);
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
            formValues = this.cards[i].form.getFieldValues(false);
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
            url    : './default/index/post.feedback/format/json',
            params : values
        });

        this.close();
    }

});