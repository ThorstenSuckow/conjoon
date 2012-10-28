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

Ext.namespace('com.conjoon.groupware.workbench.tools.suggestion');

/**
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 *
 * @class com.conjoon.groupware.workbench.tools.suggestion.DataCard
 * @extends Ext.ux.Wiz.Card
 */
com.conjoon.groupware.workbench.tools.suggestion.DataCard = Ext.extend(Ext.ux.Wiz.Card, {

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

        this.suggestionTypeComboBox = new Ext.form.ComboBox({
            fieldLabel     : com.conjoon.Gettext.gettext("Component"),
            allowBlank     : false,
            editable       : false,
            forceSelection : true,
            triggerAction  : 'all',
            store          :  [
                'Email', 'Feeds', 'Twitter', 'Youtube', 'Other'
            ],
            emptyText : com.conjoon.Gettext.gettext("What would you like to see enhanced?"),
            name      : 'suggestionType'
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

        this.suggestionDescriptionTextArea = new Ext.form.TextArea({
            fieldLabel : com.conjoon.Gettext.gettext("Suggestion"),
            allowBlank : false,
            height     : 200,
            emptyText  : com.conjoon.Gettext.gettext("Briefly explain the functionality of your suggestion."),
            name       : 'suggestionDescription'
        });

        Ext.apply(this, {
            monitorValid : true,
            title        : com.conjoon.Gettext.gettext("Make a Suggestion"),
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
                    labelText : com.conjoon.Gettext.gettext("Suggestion")
                }),
                this.suggestionTypeComboBox,
                this.suggestionDescriptionTextArea,
                this.nameField,
                this.emailField
            ]
        });

        com.conjoon.groupware.workbench.tools.suggestion.DataCard.superclass.initComponent.call(this);
    }

});
