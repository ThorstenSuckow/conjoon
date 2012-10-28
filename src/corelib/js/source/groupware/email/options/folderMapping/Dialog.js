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

Ext.namespace('com.conjoon.groupware.email.options.folderMapping');

/**
 * Dialog shows the user a ListView with names of the current IMAP
 * accounts configured and a TreePanel, where he can map a folder to a
 * specific type.
 *
 *
 * @class com.conjoon.groupware.email.options.folderMapping.Dialog
 * @extends Ext.Window
 */
com.conjoon.groupware.email.options.folderMapping.Dialog = Ext.extend(Ext.Window, {

    /**
     * @cfg {Number} accountId The account id for which a mapping whould be made.
     * If provided, the entry in the ListView with this id will be pre-selected.
     */

    /**
     * @cfg {String} type The type of mapping that should be made. If available
     * and if an accountId was specified, this type will be preselected in the
     * "type" field.
     */

    /**
     * @type {Ext.Button} okButton Will be available after the initComponent
     * method was called.
     */
    okButton : null,

    /**
     * @type {Ext.Button} cancelButton Will be available after the initComponent
     * method was called.
     */
    cancelButton : null,

    /**
     * @type {Ext.Button} applyButton Will be available after the initComponent
     * method was called.
     */
    applyButton : null,

    /**
     * @type {com.conjoon.cudgets.ListView} listView The listView for the
     * available accounts. Will be available after the initComponent method
     * was called.
     */
    listView : null,

    /**
     * @type {com.conjoon.groupware.email.options.folderMapping.ui.DefaultDialogUi} ui
     */
    ui : null,

    /**
     * @type {com.conjoon.groupware.email.options.folderMapping.SetingsContainer}
     * settingsContainer
     */
    settingsContainer : null,

    /**
     * Inits this component.
     */
    initComponent : function()
    {
        if (!this.ui) {
            this.ui = new com.conjoon.groupware.email.options.folderMapping
                          .ui.DefaultDialogUi();
        }

        this.ui.init(this);

        com.conjoon.groupware.email.options.folderMapping.Dialog.superclass
        .initComponent.call(this);
    },

// -------- api

    /**
     * Returns the settings containerfor this dialog.
     *
     * @return {com.conjoon.groupware.email.options.folderMapping.SetingsContainer}
     */
    getSettingsContainer : function()
    {
        if (!this.settingsContainer) {
            this.settingsContainer = this.ui.buildSettingsContainer();
        }

        return this.settingsContainer;
    },

    /**
     * Returns the listView to show the available accounts.
     *
     * @return {com.conjoon.cudgets.ListView}
     */
    getListView : function()
    {
        if (!this.listView) {
            this.listView = this.ui.buildListView();
        }

        return this.listView;
    },

    /**
     * Returns the "ok" button for this dialog.
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
     * Returns the "cancel" button for this dialog.
     *
     * @return {Ext.Button}
     *
     * @see _getCancelButton
     */
    getCancelButton : function()
    {
        if (!this.cancelButton) {
            this.cancelButton = this.ui.buildCancelButton();
        }

        return this.cancelButton;
    },

    /**
     * Returns the "apply" button for this dialog.
     *
     * @return {Ext.Button}
     *
     * @see getApplyButton
     */
    getApplyButton : function()
    {
        if (!this._applyButton) {
            this._applyButton = this.ui.buildApplyButton();
        }

        return this._applyButton;
    }

});