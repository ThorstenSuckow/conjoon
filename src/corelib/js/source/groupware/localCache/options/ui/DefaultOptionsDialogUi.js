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


Ext.namespace('com.conjoon.groupware.localCache.options.ui');

/**
 * Builds and layouts the Local Cache options-dialog and its components.
 *
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 *
 * @class com.conjoon.groupware.localCache.options.ui.DefaultOptionsDialogUi
 */
com.conjoon.groupware.localCache.options.ui.DefaultOptionsDialogUi = function(config) {

    config = config || {};

    Ext.apply(this, config);
};

com.conjoon.groupware.localCache.options.ui.DefaultOptionsDialogUi.prototype = {

    /**
     * @cfg {com.conjoon.groupware.localCache.options.listener.DefaultOptionsDialogListener}
     * actionListener
     * The actionListener for the dialog this ui class manages. If not provided,
     * defaults to {com.conjoon.groupware.localCache.options.listener.DefaultOptionsDialogListener}
     */
    actionListener : null,

    /**
     * @type {com.conjoon.groupware.localCache.options.Dialog} dialog
     * The dialog this ui class manages. Gets assigned in the init() method.
     */
    dialog : null,

    /**
     * Inits the layout of the container.
     * Gets called from the initComponent's "initComponent()" method.
     *
     * @param {com.conjoon.groupware.localCache.options.Dialog} dialog
     * The dialog this ui will manage.
     */
    init : function(dialog)
    {
        if (this.dialog) {
            return;
        }

        this.dialog = dialog;

        this.buildDialog();
        this.installListeners();
    },

    /**
     *
     * @protected
     */
    installListeners : function()
    {
        if (!this.actionListener) {
            this.actionListener = new com.conjoon.groupware.localCache.options
                                      .listener.DefaultOptionsDialogListener();
        }

        this.actionListener.init(this.dialog);
    },

// -------- builders

    /**
     * Layouts this ui's "dialog".
     *
     * @protected
     */
    buildDialog : function()
    {
        Ext.apply(this.dialog, {
            title     : com.conjoon.Gettext.gettext("Local Cache"),
            items     : [
                this.dialog.getSettingsContainer()
            ],
            width     : 400,
            height    : 450,
            modal     : false,
            layout    : 'fit',
            cls       : 'com-conjoon-groupware-localCache-options-dialog',
            iconCls   : 'dialogIcon',
            resizable : false,
            buttons   : [
                this.dialog.getOkButton(),
                this.dialog.getCancelButton(),
                this.dialog.getApplyButton()
            ]
        });
    },

    /**
     * Builds and returns the "Ok"-button for the dialog.
     *
     * @return {Ext.Button}
     */
    buildOkButton : function()
    {
        return new Ext.Button({
            minWidth : 75,
            text     : com.conjoon.Gettext.gettext("OK")
        });
    },

    /**
     * Builds and returns the "Apply"-button for the dialog.
     *
     * @return {Ext.Button}
     */
    buildApplyButton : function()
    {
        return new Ext.Button({
            minWidth : 75,
            text     : com.conjoon.Gettext.gettext("Apply"),
            disabled : true
        });
    },

    /**
     * Builds and returns the "close"-button for the dialog.
     *
     * @return {Ext.Button}
     */
    buildCancelButton : function()
    {
        return new Ext.Button({
            minWidth : 75,
            text     : com.conjoon.Gettext.gettext("Cancel")
        });
    },

    /**
     * Builds and returns the settingsContainer for the dialog.
     *
     * @return {com.conjoon.groupware.localCache.options.SettingsContainer}
     */
    buildSettingsContainer : function()
    {
        return new com.conjoon.groupware.localCache.options.SettingsContainer();
    },

    /**
     * Renderes the controls of the dialog eitehr enabled or disabled
     * to prevent further actions that would be invoked when interacting
     * with those controls.
     *
     * @param {Boolean} disabled True to disable the controls, otherwise false.
     * @param {Boolean} applyButtonDisabled Whether to render the applyButton
     * disabled or enabled
     */
    setControlsDisabled : function(disabled, applyButtonDisabled)
    {
        var dialog = this.dialog;

        dialog.getApplyButton().setDisabled(applyButtonDisabled);
        dialog.getOkButton().setDisabled(disabled);
        dialog.getCancelButton().setDisabled(disabled);
    },

    /**
     * Shows an error dialog.
     *
     * @param {String} title The title for the dialog
     * @param {String} msg The msg for the dialog
     */
    buildErrorDialog : function(title, msg)
    {
        var msg = new com.conjoon.SystemMessage({
            title : title,
            text  : msg,
            type  : com.conjoon.SystemMessage.TYPE_WARNING
        });

        com.conjoon.SystemMessageManager.error(msg);
    }

};