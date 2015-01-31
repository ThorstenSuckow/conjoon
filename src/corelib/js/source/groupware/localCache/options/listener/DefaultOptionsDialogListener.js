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

Ext.namespace('com.conjoon.groupware.localCache.options.listener');

/**
 * An  base class that provides the interface for listeners for
 * {com.conjoon.groupware.localCache.options.Dialog}
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 *
 * @class com.conjoon.groupware.localCache.options.listener.DefaultOptionsDialogListener
 *
 * @constructor
 */
com.conjoon.groupware.localCache.options.listener.DefaultOptionsDialogListener = function() {

};

com.conjoon.groupware.localCache.options.listener.DefaultOptionsDialogListener.prototype = {

    /**
     * @type {com.conjoon.groupware.localCache.options.Dialog} dialog The dalog
     * this listener is bound to.
     */
    dialog : null,

    /**
     * @type {Boolean} closeAfterSave Set to true if the dialog should be closed
     * after settings have been saved successfully on the server, otherwise
     * false
     */
    closeAfterSave : false,

    /**
     * @type {Boolean} applyButtonState Saves the state of the applyButton before
     * an attempt to clear/build the cache is made.
     */
    applyButtonDisabled : true,

// -------- api

    /**
     * Installs the listeners for the elements found in the dialog.
     *
     * @param {com.conjoon.groupware.localCache.options.Dialog} dialog
     * The settings dialog this listener is bound to.
     *
     * @packageprotected
     */
    init : function(dialog)
    {
        if (this.dialog) {
            return;
        }

        this.dialog = dialog;

        this.dialog.mon(
            this.dialog.getCancelButton(), 'click', this.onCancelButtonClick, this
        );

        this.dialog.mon(
            this.dialog.getApplyButton(), 'click', this.onApplyButtonClick, this
        );

        this.dialog.mon(
            this.dialog.getOkButton(), 'click', this.onOkButtonClick, this
        );

        this.dialog.mon(
            this.dialog.getSettingsContainer().getCachingContainer()
            .getFileSettingsForm(), 'startedit',
            this.onFileSettingsStartEdit, this
        );

        this.dialog.mon(
            this.dialog.getSettingsContainer(),
            'beforeset',
            this.onSettingsContainerBeforeSet, this
        );

        this.dialog.mon(
            this.dialog.getSettingsContainer(),
            'setsuccess',
            this.onSettingsContainerSetSuccess, this
        );

        this.dialog.mon(
            this.dialog.getSettingsContainer(),
            'setfailure',
            this.onSettingsContainerSetFailure, this
        );

        var Api = com.conjoon.cudgets.localCache.Api;
        Api.onBeforeClear(this.onLocalCacheApiBeforeClear,   this);
        Api.onClearSuccess(this.onLocalCacheApiClearSuccess, this);
        Api.onClearFailure(this.onLocalCacheApiClearFailure, this);
        Api.onBeforeBuild(this.onLocalCacheApiBeforeBuild,   this);
        Api.onBuild(this.onLocalCacheApiBuild,               this);
        Api.onBuildSuccess(this.onLocalCacheApiBuildSuccess, this);
        Api.onBuildFailure(this.onLocalCacheApiBuildFailure, this);

        this.dialog.on('destroy', this.onDialogDestroy, this);


        this.dialog.on(
            'beforeclose',
            this.onBeforeClose, this
        );
    },

// -------- helper

// ------- listeners

    /**
     * Listener for the destroy event of the dialog.
     *
     * @param {Ext.Window} dialog
     */
    onDialogDestroy : function(dialog)
    {
        var Api = com.conjoon.cudgets.localCache.Api;
        Api.unBeforeClear(this.onLocalCacheApiBeforeClear,   this);
        Api.unClearSuccess(this.onLocalCacheApiClearSuccess, this);
        Api.unClearFailure(this.onLocalCacheApiClearFailure, this);
        Api.unBeforeBuild(this.onLocalCacheApiBeforeBuild,   this);
        Api.unBuild(this.onLocalCacheApiBuild,               this);
        Api.unBuildSuccess(this.onLocalCacheApiBuildSuccess, this);
        Api.unBuildFailure(this.onLocalCacheApiBuildFailure, this);
    },

    /**
     * Listener for the settingsContainer's setsuccess event.
     *
     * @param {com.conjoon.groupware.localCache.options.SettingsContainer}
     * container
     * @param {Object} provider
     * @param {Object} response
     */
    onSettingsContainerSetSuccess : function()
    {
        if (this.closeAfterSave) {
            this.dialog.close();
            return;
        }

        this.dialog.setControlsDisabled(false, true);
    },

    /**
     * Listener for the settingsContainer's setfailure event.
     *
     * @param {com.conjoon.groupware.localCache.options.SettingsContainer}
     * container
     * @param {Object} provider
     * @param {Object} response
     */
    onSettingsContainerSetFailure : function()
    {
        this.closeAfterSave = false;
        this.dialog.setControlsDisabled(false, true);
    },

    /**
     * Listener for the dialog's "beforeclose" event.
     * Will prevent closing the dialog if the settingsContainer
     * is currently busy updating localCache settings.
     *
     * @param {Ext.Window} dialog
     */
    onBeforeClose : function(dialog)
    {
        if (this.dialog.getSettingsContainer().isRequestPending()) {
            return false;
        }
    },

    /**
     * Listener for the local cache Api's "beforeclear" event.
     *
     * @param {com.conjoon.cudgets.localCache.Adapter} adapter
     */
    onLocalCacheApiBeforeClear : function(adapter)
    {
        this.applyButtonDisabled = this.dialog.getApplyButton().disabled;
        this.dialog.setControlsDisabled(true, true);
    },

    /**
     * Listener for the local cache Api's "clearsuccess" event.
     *
     * @param {com.conjoon.cudgets.localCache.Adapter} adapter
     */
    onLocalCacheApiClearSuccess : function(adapter)
    {
        this.dialog.setControlsDisabled(false, this.applyButtonDisabled);
    },

    /**
     * Listener for the local cache Api's "clearfailure" event.
     *
     * @param {com.conjoon.cudgets.localCache.Adapter} adapter
     */
    onLocalCacheApiClearFailure : function(adapter)
    {
        this.dialog.setControlsDisabled(false, this.applyButtonDisabled);
    },

    /**
     * Listener for the local cache Api's "beforebuild" event.
     *
     * @param {com.conjoon.cudgets.localCache.Adapter} adapter
     */
    onLocalCacheApiBeforeBuild : function(adapter)
    {
        if (!this.dialog.getSettingsContainer().isCachingOptionDirty()) {
            return true;
        }

        this.dialog.showConfirmDialog(
            com.conjoon.Gettext.gettext("Confirm changes"),
            com.conjoon.Gettext.gettext("You have made changes to the cache. Do you wish to save those changes first?"),
            this.onConfirmSubmitChanges, this
        );

        return false;
    },

    /**
     * Callback for the confirm dialog.
     * buttonId "yes" will save the settings, then rebuild the cache. "no"
     * will simply rebuild the cache without considering the changes.
     *
     * @param {String} buttonId The id of the button that was clicked.
     *                          Either "yes" or "no"
     *
     * @throws Exception if buttonId did not equal to yes or no
     */
    onConfirmSubmitChanges : function(buttonId)
    {
        var settingsContainer = this.dialog.getSettingsContainer(),
            Api               = com.conjoon.cudgets.localCache.Api,
            func              = function() {
                Api.unBeforeBuild(this.onLocalCacheApiBeforeBuild, this);
                settingsContainer.getCachingContainer().rebuildCache();
                Api.onBeforeBuild(this.onLocalCacheApiBeforeBuild, this);
            },
            single            = {single : true},
            applyFunc         = function() {
                settingsContainer.on('setsuccess', func, this, single);
            };

        if (buttonId == 'yes') {

            settingsContainer.on('beforeset', applyFunc, this, single);
            settingsContainer.on('setfailure', function() {
                settingsContainer.un('setsuccess', func, this);
            }, this, single);

            this.dialog.getSettingsContainer().saveSettings();
            return;
        }

        if (buttonId == 'no') {
            func.call(this);
            return;
        }

        // not yes or no? throw exception to indicate that something went wrong
        throw("buttonId was \""+buttonId+"\"");
    },

    /**
     * Listener for the local cache Api's "build" event.
     *
     * @param {com.conjoon.cudgets.localCache.Adapter} adapter
     */
    onLocalCacheApiBuild : function(adapter)
    {
        this.applyButtonDisabled = this.dialog.getApplyButton().disabled;
        this.dialog.setControlsDisabled(true, true);
    },

    /**
     * Listener for the local cache Api's "buildsuccess" event.
     *
     * @param {com.conjoon.cudgets.localCache.Adapter} adapter
     */
    onLocalCacheApiBuildSuccess : function(adapter)
    {
        this.dialog.setControlsDisabled(false, this.applyButtonDisabled);
    },

    /**
     * Listener for the local cache Api's "buildfailure" event.
     *
     * @param {com.conjoon.cudgets.localCache.Adapter} adapter
     */
    onLocalCacheApiBuildFailure : function(adapter)
    {
        this.dialog.showErrorDialog(
            com.conjoon.Gettext.gettext("Error while trying to rebuild the cache"),
            com.conjoon.Gettext.gettext("The cache could not be rebuilt. It is possible that the browser's storage quota is exceeded or files are missing.")
        );
        this.dialog.setControlsDisabled(false, this.applyButtonDisabled);
    },

    /**
     * Listener for the settingsContainer's beforeset event.
     * Will render the controls of the dialog disabled to prevent further
     * interaction.
     *
     * @param {com.conjoon.groupware.localCache.options.SettingsContainer}
     * settingsContainer
     */
    onSettingsContainerBeforeSet : function(settingsContainer)
    {
        this.dialog.setControlsDisabled(true, true);
    },

    /**
     * Listener for the dialog's "cancel"-button "click" event.
     *
     * @param {Ext.Button} button
     */
    onCancelButtonClick : function(button)
    {
        this.dialog.closeDialog();
    },

    /**
     * Listener for the dialog's "apply"-button "click" event.
     *
     * @param {Ext.Button} button
     */
    onApplyButtonClick : function(button)
    {
        this.dialog.getApplyButton().setDisabled(true);
        this.dialog.getSettingsContainer().getCachingContainer()
            .getFileSettingsForm().installStartEditListener();
        this.dialog.saveSettings();
    },

    /**
     * Listener for the dialog's "apply"-button "click" event.
     *
     * @param {Ext.Button} button
     */
    onOkButtonClick : function(button)
    {
        this.closeAfterSave = true;
        if (!this.dialog.saveSettings()) {
            this.dialog.close();
        }
    },

    /**
     * Listener for the settingContainers cachingContainer fileSettingsForm
     * "startedit" event.
     *
     * @param {com.conjoon.cudgets.settings.Card} card The card that triggered
     * the event
     * @param {Ext.form.Field} field The field in the card that initially
     * triggered the event
     */
    onFileSettingsStartEdit : function(card, field)
    {
        this.dialog.getApplyButton().setDisabled(false);
    }


};