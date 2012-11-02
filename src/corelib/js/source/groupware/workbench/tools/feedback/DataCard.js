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
 * @class com.conjoon.groupware.workbench.tools.feedback.DataCard
 * @extends Ext.ux.Wiz.Card
 */
com.conjoon.groupware.workbench.tools.feedback.DataCard = Ext.extend(Ext.ux.Wiz.Card, {

    /**
     * @type {Ext.form.ComboBox} suggestionTypeComboBox
     */
    suggestionTypeComboBox : null,

    /**
     * @type {Ext.form.TextArea} suggestionDescriptionTextArea
     */
    suggestionDescriptionTextArea : null,

    /**
     * @type {Ext.form.TextField} emailField
     */
    emailField : null,

    /**
     * @type {Ext.form.TextField} nameField
     */
    nameField : null,

    initComponent : function()
    {
        var user = com.conjoon.groupware.Reception.getUser();

        this.componentComboBox = new Ext.form.ComboBox({
            fieldLabel     : com.conjoon.Gettext.gettext("Component"),
            allowBlank     : false,
            editable       : false,
            forceSelection : true,
            triggerAction  : 'all',
            store          :  [
                'Email', 'Feeds', 'Twitter', 'Youtube', 'Other'
            ],
            emptyText : com.conjoon.Gettext.gettext("Which component is subject of your feedback?"),
            name      : 'component'
        });

        this.emailField = new Ext.form.TextField({
            fieldLabel : com.conjoon.Gettext.gettext("E-Mail (optional)"),
            name       : 'emailAddress',
            emptyText  : com.conjoon.Gettext.gettext("Optionally provide your E-Mail Address"),
            value      : user.emailAddress
        });

        this.nameField = new Ext.form.TextField({
            fieldLabel : com.conjoon.Gettext.gettext("Name (optional)"),
            name       : 'name',
            emptyText  : com.conjoon.Gettext.gettext("Optionally provide your Name"),
            value      : user.firstname+" "+user.lastname
        });

        this.feedbackTextArea = new Ext.form.TextArea({
            fieldLabel : com.conjoon.Gettext.gettext("Feedback"),
            allowBlank : false,
            height     : 200,
            emptyText  : com.conjoon.Gettext.gettext("Please be as specific as you can in the allotted space"),
            name       : 'feedbackDescription'
        });

        Ext.apply(this, {
            monitorValid : true,
            title        : com.conjoon.Gettext.gettext("Provide feedback"),
            cls          : 'dataCard',
            border       : false,
            baseCls      : 'x-small-editor',
            //labelWidth : 180,
            defaults   : {
                labelStyle : 'font-size:11px',
                anchor     : '100%'
            },
            items : [
                new com.conjoon.groupware.util.FormIntro({
                    style     : 'margin:10px 0 15px 0',
                    labelText : com.conjoon.Gettext.gettext("Feedback")
                }),
                this.componentComboBox,
                this.feedbackTextArea,
                this.nameField,
                this.emailField
            ]
        });

        com.conjoon.groupware.workbench.tools.feedback.DataCard.superclass.initComponent.call(this);
    }

});
