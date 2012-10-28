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

Ext.namespace('com.conjoon.groupware.workbench.tools.bugReport');

/**
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 *
 * @class com.conjoon.groupware.workbench.tools.bugReport.WelcomeCard
 * @extends Ext.ux.Wiz.Card
 */
com.conjoon.groupware.workbench.tools.bugReport.WelcomeCard = Ext.extend(Ext.ux.Wiz.Card, {

    /**
     * @type {Ext.form.Checkbox} publicCheckbox
     */
    publicCheckbox : null,

    initComponent : function()
    {
        this.publicCheckbox = new Ext.form.Checkbox({
            fieldLabel  : com.conjoon.Gettext.gettext("Make my bug report public"),
            checked   : true,
            name      : 'public'
        });

        Ext.apply(this, {
            title      : com.conjoon.Gettext.gettext("Privacy Information"),
            cls        : 'welcomeCard',
            border     : false,
            baseCls    : 'x-small-editor',
            labelWidth : 180,
            defaults   : {
                labelStyle : 'font-size:11px',
                anchor     : '100%'
            },
            items : [
                new com.conjoon.groupware.util.FormIntro({
                    style     : 'margin:10px 0 15px 0',
                    labelText : com.conjoon.Gettext.gettext("Privacy Information"),
                    text      : com.conjoon.Gettext.gettext("The data you are about to send will be submitted to the bug forum at <a href=\"http://conjoon.org/forum\" target=\"_blank\">http://conjoon.org/forum</a>.<br /> No personal data of you will be collected other than the bug report you are about to file. Once the report has been sent, it has to be approved by the moderators first before it will be made public. However, you can choose if you would like to keep this report private, i.e. whether it will be made public or not.")
                }),
                this.publicCheckbox
            ]
        });

        com.conjoon.groupware.workbench.tools.bugReport.WelcomeCard.superclass.initComponent.call(this);
    }

});
