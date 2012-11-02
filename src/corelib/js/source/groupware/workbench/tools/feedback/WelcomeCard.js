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

Ext.namespace('com.conjoon.groupware.workbench.tools.feedback');

/**
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 *
 * @class com.conjoon.groupware.workbench.tools.feedback.WelcomeCard
 * @extends Ext.ux.Wiz.Card
 */
com.conjoon.groupware.workbench.tools.feedback.WelcomeCard = Ext.extend(Ext.ux.Wiz.Card, {

    /**
     * @type {Ext.form.Checkbox} publicCheckbox
     */
    publicCheckbox : null,

    /**
     * @type {Ext.form.ComboBox}
     */
    typeCombo : null,

    initComponent : function()
    {
        this.publicCheckbox = new Ext.form.Checkbox({
            fieldLabel : com.conjoon.Gettext.gettext("Make it public"),
            checked    : true,
            name       : 'public'
        });

        this.typeCombo = new Ext.form.ComboBox({
            fieldLabel    : com.conjoon.Gettext.gettext("Type of feedback"),
            name          : 'feedbackType',
            value         : 'bug',
            triggerAction : 'all',
            editable      : false,
            lazyRender    : true,
            mode          : 'local',
            store: new Ext.data.ArrayStore({
                idIndex : 0,
                fields  : [
                    'id',
                    'displayText'
                ],
                data: [[
                    'bug',
                    com.conjoon.Gettext.gettext("Bug Report")
                ], [
                    'feature',
                    com.conjoon.Gettext.gettext("Feature Suggestion")
                ], [
                    'imhappy',
                    com.conjoon.Gettext.gettext("conjoon made me happy :)")
                ]]
            }),
            valueField   : 'id',
            displayField : 'displayText'

        });

        Ext.apply(this, {
            title      : com.conjoon.Gettext.gettext("Information on your Feedback"),
            cls        : 'welcomeCard',
            border     : false,
            baseCls    : 'x-small-editor',
            labelWidth : 120,
            defaults   : {
                labelStyle : 'font-size:11px',
                anchor     : '100%'
            },
            items : [
                new com.conjoon.groupware.util.FormIntro({
                    style     : 'margin:10px 0 15px 0',
                    labelText : com.conjoon.Gettext.gettext("Privacy Information"),
                    text      : com.conjoon.Gettext.gettext("The data you are about to send will be submitted to the Feature Request- or Bug-Forum (based on the type of your feedback) at <a href=\"http://conjoon.org/forum\" target=\"_blank\">http://conjoon.org/forum</a>.<br /> No personal data of you will be collected other than the feedback you are about to send. Once the feedback has been sent, it has to be approved by the moderators first before it will be made public. However, you can choose if you would like to keep this feedback private, i.e. whether it will be made public or not.")
                }),
                this.typeCombo,
                this.publicCheckbox
            ]
        });

        com.conjoon.groupware.workbench.tools.feedback.WelcomeCard.superclass.initComponent.call(this);
    }

});
