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

Ext.namespace('com.conjoon.groupware.email.options');

/**
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 *
 * @class com.conjoon.groupware.email.options.DialogListener
 * @extends cudgets.settings.listener.DefaultDialogListener
 */
com.conjoon.groupware.email.options.DialogListener = Ext.extend(
    com.conjoon.cudgets.settings.listener.DefaultDialogListener, {

    /**
     * Writes all settings to the registry.
     *
     */
    writeSettingsToRegistry : function() {

        var me = this,
            record,
            records = me.dialog.getSettingsContainer().getAllEntries(),
            readingKey = '/client/conjoon/modules/mail/options/reading/',
            settingsContainer = me.dialog.getSettingsContainer(),
            modified;

        if (settingsContainer.getInvalidFieldAndCard()) {
            throw("Unexpected error!");
        }
        settingsContainer.saveFormDataToSelectedEntry();

        for (var i = 0, len = records.length; i < len; i++) {
            record = records[i];

            if (record.get('optionName') == 'reading') {

                modified = com.conjoon.groupware.Registry.setValues({
                    values : [{
                        key   : readingKey+ 'preferred_format',
                        value : record.get('preferredFormat')
                    }, {
                        key   : readingKey + 'allow_externals',
                        value : record.get('allowExternals')
                    }],
                    beforewrite : function() {
                        me.dialog.setControlsDisabled(true, true);
                        settingsContainer.setServerRequestPending(true, Ext.data.Api.actions.update);
                    },
                    success : function() {
                        // reset first so that server request pending gets invalidated
                        // and beforeclose listener is not working
                        me.resetMe();

                        if (me.closeAfterSave) {
                            me.dialog.close();
                            return;
                        }
                    },
                    failure : function() {

                        me.resetMe();

                        // no rejecting of changes, due to the fact that one registry setting
                        // might get successfully changed, the other not
                        // we could deal with this by re-reading the commited registry values
                        // into the record, then re-apply teh record to the form. for now,
                        // this will do just fine
                        settingsContainer.showErrorMessage(
                            com.conjoon.Gettext.gettext("Could not successfully update all of the options."),
                            com.conjoon.Gettext.gettext("Error")
                        );
                    }
                });

                if (!modified) {
                    me.resetMe();
                    if (me.closeAfterSave) {
                        me.dialog.close();
                        return;
                    }
                }

            }
        }

    },

    /**
     * Helper function for resetting the dialog after a write operation.
     */
    resetMe : function() {

        var me = this,
            settingsContainer = me.dialog.getSettingsContainer();

        settingsContainer.setServerRequestPending(false);
        me.dialog.setControlsDisabled(false, true);
        var cards = settingsContainer.getFormCards();
        for (var i = 0, len = cards.length; i < len; i++) {
            cards[i].installStartEditListener(true);
        }
    },

    /**
     * @inheritdoc
     */
    onOkClick : function(button, e)
    {
        var me = this;

        me.closeAfterSave = true;
        me.writeSettingsToRegistry();
    },

    /**
     * @inheritdoc
     */
    onApplyClick : function(button, e)
    {
        var me = this;
        me.writeSettingsToRegistry();
    }

});
