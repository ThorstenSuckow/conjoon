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

Ext.namespace('com.conjoon.service.twitter.optionsDialog');

/**
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 *
 * @class com.conjoon.service.twitter.optionsDialog.DialogListener
 * @extends cudgets.settings.listener.DefaultDialogListener
 */
com.conjoon.service.twitter.optionsDialog.DialogListener = Ext.extend(
    com.conjoon.cudgets.settings.listener.DefaultDialogListener, {

    /**
     * @type {String} clsId
     */
    clsId : '7afa3364-61ab-40a0-ad15-cfb4ab9dc37d',


// -------- api

    /**
     * Installs the listeners for the elements found in the dialog.
     *
     * @param {com.conjoon.cudgets.settings.Dialog} dialog The settings dialog
     * this listener is bound to.
     *
     * @packageprotected
     */
    init : function(dialog)
    {
        if (this.dialog) {
            return;
        }

        com.conjoon.service.twitter.optionsDialog.DialogListener.superclass.init.call(this, dialog);

        this.dialog.on('render', this.onRender, this);
    },

    /**
     * Listener for the render event. Checks if any accounts are available in the store.
     * If not, the Wizard pops up automatically.
     *
     */
    onRender : function()
    {
        if (com.conjoon.service.twitter.data.AccountStore.getInstance().getRange().length == 0) {
            com.conjoon.service.twitter.wizard.AccountWizardBaton.show({defer : 500});
        }
    }

});