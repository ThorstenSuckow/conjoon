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
