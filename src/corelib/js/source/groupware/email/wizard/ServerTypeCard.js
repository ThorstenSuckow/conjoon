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

Ext.namespace('com.conjoon.groupware.email.wizard');

/**
 *
 *
 * @class com.conjoon.groupware.email.wizard.ServerTypeCard
 * @extends Ext.ux.Wiz.Card
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
com.conjoon.groupware.email.wizard.ServerTypeCard = Ext.extend(Ext.ux.Wiz.Card, {

    radioPopField  : null,
    radioImapField : null,

    initComponent : function()
    {
        this.monitorValid = true;

        this.baseCls    = 'x-small-editor';
        this.labelWidth = 80;

        this.title = com.conjoon.Gettext.gettext("Server type");
        this.defaults = {
            labelStyle : 'width:80px;font-size:11px',
            anchor     : '100%'
        };

        this.radioPopField = new Ext.form.Radio({
            boxLabel   : com.conjoon.Gettext.gettext("POP"),
            name       : 'protocol',
            inputValue : 'POP',
            checked    : true
        });

        this.radioImapField = new Ext.form.Radio({
            boxLabel   : com.conjoon.Gettext.gettext("IMAP"),
            name       : 'protocol',
            inputValue : 'IMAP'
        });

        this.items = [
            new com.conjoon.groupware.util.FormIntro({
                style     : 'margin:10px 0 15px 0;',
                labelText : com.conjoon.Gettext.gettext("Server type"),
                text      : com.conjoon.Gettext.gettext("Specify the protocol used by the server this account works with. Please contact your email account's administrator if you are unsure which protocol to use.")
            }),
            this.radioPopField,
            this.radioImapField
        ];

        com.conjoon.groupware.email.wizard.ServerTypeCard.superclass.initComponent.call(this);
    }

});
