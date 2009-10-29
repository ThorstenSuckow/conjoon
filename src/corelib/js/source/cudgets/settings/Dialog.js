/**
 * conjoon
 * (c) 2002-2009 siteartwork.de/conjoon.org
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

Ext.namespace('com.conjoon.cudgets.settings');

/**
 * A base class for a dialog window that holds a settings container.
 * The dialog has 3 buttons - ok, apply, cancel. For each button additional listeners
 * will be installed.
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 *
 * @class com.conjoon.cudgets.settings.Dialog
 * @extends Ext.Window
 * @constructor
 */
com.conjoon.cudgets.settings.Dialog = Ext.extend(Ext.Window, {

    /**
     * @type {Ext.Button} cancelButton
     */
    cancelButton : null,

    /**
     * @type {Ext.Button} okButton
     */
    okButton : null,

    /**
     * @type {Ext.Button} applyButton
     */
    applyButton : null,

    /**
     * @type {Ext.Button} cancelButton
     */
    entriesComponent : null,

    /**
     * @cfg {com.conjoon.cudgets.settings.Container} settingsContainer
     */
    settingsContainer : null,

    /**
     * @cfg {com.conjoon.cudgets.settings.ui.DefaultDialogUi} ui
     * The ui for this dialog. If not provided, defaults to
     * {com.conjoon.cudgets.settings.ui.DefaultDialogUi}
     */
    ui : null,

// -------- overrides

    /**
     * Inits this component.
     *
     */
    initComponent : function()
    {
        if (!this.ui) {
            this.ui = new com.conjoon.cudgets.settings.ui.DefaultDialogUi();
        }
        
        this.ui.init(this);

        com.conjoon.cudgets.settings.Dialog.superclass.initComponent.call(this);
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

// --------- component getters

    /**
     * Returns the settingscontainer that this dialog manages.
     *
     * @return {com.conjoon.cudgets.settings.Container}
     */
    getSettingsContainer : function()
    {
        if (!this.settingsContainer) {
            throw(
                "com.conjoon.cudgets.settings.Dialog.getSettingsContainer() - "
                +"no settingsContainer configured"
            );
        }

        return this.settingsContainer;
    }, 

    /**
     * Returns the ok button for this dialog.
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
     * Returns the cancel button for this dialog.
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
     * Returns the apply button for this dialog.
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

});