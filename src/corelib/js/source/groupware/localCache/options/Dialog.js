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

Ext.namespace('com.conjoon.groupware.localCache.options');

/**
 * This options dialog lets the user manage his local Google Gear cache for static
 * resources from conjoon.
 *
 * There may only be one instance opened of this dialog at a time.See code
 * at the bottom of the file.
 *
 * @class com.conjoon.groupware.localCache.options.Dialog
 * @extends Ext.Window
 */
com.conjoon.groupware.localCache.options.Dialog = Ext.extend(Ext.Window, {

    /**
     * @type {Ext.Button} okButton The "Ok" button that will be shown
     * for this dialog.
     */
    okButton : null,

    /**
     * @type {Ext.Button} applyButton The "apply" button that will be shown
     * for this dialog.
     */
    applyButton : null,

    /**
     * @type {Ext.Button} cancelButton The "cancel" button that will be shown
     * for this dialog.
     */
    cancelButton : null,

    /**
     * @type {com.conjoon.groupware.localCache.options.ui.DefaultOptionsDialogUi}
     * The ui that manges this dialog. Defaults to
     * {com.conjoon.groupware.localCache.options.ui.DefaultOptionsDialogUi}
     */
    ui : null,

    /**
     * @type {com.conjoon.groupware.localCache.options.SettingsContainer}
     * The settingscontainer that holds tha cual ui for editing Local Cache
     * options.
     */
    settingsContainer : null,

// -------- Ext.Window

    /**
     * Inits this component.
     *
     */
    initComponent : function()
    {
        if (!this.ui) {
            this.ui = new com.conjoon.groupware.localCache.options.ui
                          .DefaultOptionsDialogUi();
        }

        this.ui.init(this);

        com.conjoon.groupware.localCache.options.Dialog.superclass.initComponent.call(this);
    },

// -------- api

    /**
     * Sets the controls either disabled or enabled to prevent further actions
     * that would be triggered when clicking a button or a tool button.
     *
     * @param {Boolean} disabled True to disable the controls, otherwise false
     * @param {Boolean} applyButtonDisabled Whether to render the applyButton
     * disabled or enabled
     */
    setControlsDisabled : function(disabled, applyButtonDisabled)
    {
        this.ui.setControlsDisabled(disabled, applyButtonDisabled);
    },

    /**
     * Returns the settings container for this dialog.
     *
     * @return {com.conjoon.groupware.localCache.options.SettingsContainer}
     */
    getSettingsContainer : function()
    {
        if (!this.settingsContainer) {
            this.settingsContainer = this.ui.buildSettingsContainer();
        }

        return this.settingsContainer;
    },


    /**
     * Returns the "Ok" button for this dialog.
     *
     * @return {Ext.Button}
     */
    getOkButton : function()
    {
        if (!this.okButton) {
            this.okButton = this.ui.buildOkButton();
        }

        return this.okButton;
    },

    /**
     * Returns the "apply" button for this dialog.
     *
     * @return {Ext.Button}
     */
    getApplyButton : function()
    {
        if (!this.applyButton) {
            this.applyButton = this.ui.buildApplyButton();
        }

        return this.applyButton;
    },

    /**
     * Returns the "cancel" button for this dialog.
     *
     * @return {Ext.Button}
     */
    getCancelButton : function()
    {
        if (!this.cancelButton) {
            this.cancelButton = this.ui.buildCancelButton();
        }

        return this.cancelButton;
    },

    /**
     * Closes this dialog.
     *
     */
    closeDialog : function()
    {
        this.close();
    },

    /**
     * Tells the settinsgContainer to save the settings.
     *
     * @return {Boolean} true if there are values which have to be saved
     * server-seide, otherwise false
     */
    saveSettings : function()
    {
        return this.getSettingsContainer().saveSettings();
    },

    /**
     * Shows an error dialog with the specified message.
     *
     * @param {String} title The title for the dialog
     * @param {String} msg The error message to display
     */
    showErrorDialog : function(title, msg)
    {
        this.ui.buildErrorDialog(title, msg);
    },

    /**
     * Shows a confirm dialog with the specified message.
     *
     * @param {String} title The title for the dialog
     * @param {String} msg The message to display
     * @param {Function} callback The callback for confirmation
     *                            (ok button clicked)
     * @param {Object} scope The scope for the callback
     */
    showConfirmDialog : function(title, msg, callback, scope)
    {
        this.ui.buildConfirmDialog(title, msg, callback, scope);
    }

});

var __dlg = com.conjoon.groupware.localCache.options.Dialog;

__dlg.__dialog;

__dlg.showDialog = function() {
    if (__dlg.__dialog) {
        __dlg.__dialog.show();
        return;
    }

    __dlg.__dialog = new com.conjoon.groupware.localCache.options.Dialog();
    __dlg.__dialog.on('close', function() {
        __dlg.__dialog = null;
    });
    __dlg.__dialog.show();
};

