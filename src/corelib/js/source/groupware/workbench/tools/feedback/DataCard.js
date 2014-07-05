/**
 * conjoon
 * (c) 2007-2014 conjoon.org
 * licensing@conjoon.org
 *
 * conjoon
 * Copyright (C) 2014 Thorsten Suckow-Homberg/conjoon.org
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
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
