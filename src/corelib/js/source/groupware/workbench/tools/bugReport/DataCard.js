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

Ext.namespace('com.conjoon.groupware.workbench.tools.bugReport');

/**
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 *
 * @class com.conjoon.groupware.workbench.tools.bugReport.DataCard
 * @extends Ext.ux.Wiz.Card
 */
com.conjoon.groupware.workbench.tools.bugReport.DataCard = Ext.extend(Ext.ux.Wiz.Card, {

    /**
     * @type {Ext.form.ComboBox} problemTypeComboBox
     */
    problemTypeComboBox : null,

    /**
     * @type {Ext.form.TextArea} problemDescriptionTextArea
     */
    problemDescriptionTextArea : null,

    /**
     * @type {Ext.form.TextField} emailField
     */
    emailField : null,

    initComponent : function()
    {
        this.problemTypeComboBox = new Ext.form.ComboBox({
            fieldLabel     : com.conjoon.Gettext.gettext("Component"),
            allowBlank     : false,
            editable       : false,
            forceSelection : true,
            triggerAction  : 'all',
            store          :  [
                'Email', 'Feeds', 'Twitter', 'Youtube', 'Other'
            ],
            emptyText : com.conjoon.Gettext.gettext("Which functionality is affected by the issue?"),
            name      : 'problemType'
        });

        this.emailField = new Ext.form.TextField({
            fieldLabel : com.conjoon.Gettext.gettext("E-Mail (optional)"),
            name       : 'emailAddress',
            emptyText  : com.conjoon.Gettext.gettext("Optionally provide your E-Mail Address")
        });

        this.problemDescriptionTextArea = new Ext.form.TextArea({
            fieldLabel : com.conjoon.Gettext.gettext("Problem Description"),
            allowBlank : false,
            height     : 200,
            emptyText  : com.conjoon.Gettext.gettext("How is this problem affecting the functionality?"),
            name       : 'problemDescription'
        });

        Ext.apply(this, {
            monitorValid : true,
            title        : com.conjoon.Gettext.gettext("File an Issue"),
            cls          : 'dataCard',
            border       : false,
            baseCls      : 'x-small-editor',
            //labelWidth : 180,
            defaults   : {
                labelStyle : 'font-size:11px',
                anchor     : '100%'
            },
            items : [
                this.problemTypeComboBox,
                this.problemDescriptionTextArea,
                this.emailField
            ]
        });

        com.conjoon.groupware.workbench.tools.bugReport.DataCard.superclass.initComponent.call(this);
    }

});
