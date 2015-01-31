/**
 * conjoon
 * (c) 2007-2015 conjoon.org
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