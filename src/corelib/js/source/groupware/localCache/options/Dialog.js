/**
 * conjoon
 * (c) 2002-2010 siteartwork.de/conjoon.org
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

Ext.namespace('com.conjoon.groupware.localCache.options');

/**
 * This options dialog lets the user manage his local Google Gear cache for static
 * resources from conjoon.
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
     */
    saveSettings : function()
    {
        this.getSettingsContainer().saveSettings();
    }

});

