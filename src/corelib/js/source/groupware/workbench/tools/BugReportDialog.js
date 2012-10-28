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
 * @class com.conjoon.groupware.workbench.tools.BugReportDialog
 * @extends Ext.ux.Wiz
 */
com.conjoon.groupware.workbench.tools.BugReportDialog = Ext.extend(Ext.ux.Wiz, {

    /**
     * Inits this component.
     */
    initComponent : function()
    {
        Ext.apply(this, {
            finishButtonText : 'Send Report',
            height           : 475,
            width            : 480,
            cls              : 'com-conjoon-groupware-workbench-tools-BugReportDialog',
            title            : com.conjoon.Gettext.gettext("Report a Bug"),
            headerConfig     : {
                title : com.conjoon.Gettext.gettext("File an Issue")
            },
            cards  : [
                new com.conjoon.groupware.workbench.tools.bugReport.WelcomeCard(),
                new com.conjoon.groupware.workbench.tools.bugReport.DataCard()
            ]
        });

        com.conjoon.groupware.workbench.tools.BugReportDialog.superclass.initComponent.call(this);
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
            url    : './default/index/post.bug.report/format/json',
            params : values
        });

        this.close();
    }

});