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
