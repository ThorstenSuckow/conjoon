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

Ext.namespace('com.conjoon.groupware.email.options.folderMapping.ui');

/**
 * Builds and layouts the FolderMapping dialog and its components.
 *
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 *
 * @class com.conjoon.groupware.email.options.folderMapping.ui
 */
com.conjoon.groupware.email.options.folderMapping.ui.DefaultDialogUi = function(config) {

    config = config || {};

    Ext.apply(this, config);
};

com.conjoon.groupware.email.options.folderMapping.ui.DefaultDialogUi.prototype = {

    /**
     * @cfg {com.conjoon.groupware.email.options.folderMapping.listener.DefaultDialogListener}
     * actionListener
     * The actionListener for the dialog this ui class manages. If not provided,
     * defaults to {com.conjoon.groupware.email.options.folderMapping.listener.DefaultDialogListener}
     */
    actionListener : null,

    /**
     * @type {com.conjoon.groupware.email.options.folderMapping.Dialog} dialog
     * The dialog this ui class manages. Gets assigned in the init() method.
     */
    dialog : null,

    /**
     * Inits the layout of the container.
     * gets called from the initComponent's "initComponent()" method.
     *
     * @param {com.conjoon.groupware.email.options.folderMapping.Dialog} dialog
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
            this.actionListener = new com.conjoon.groupware.email.options
                                      .folderMapping.listener.DefaultDialogListener();
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
            title     : com.conjoon.Gettext.gettext("Folder Mappings"),
            width     : 500,
            height    : 400,
            modal     : true,
            layout    : 'border',
            cls       : 'com-conjoon-groupware-email-options-folderMapping-dialog',
            iconCls   : 'dialogIcon',
            resizable : false,
            items     : [
                this.dialog.getListView(),
                this.dialog.getSettingsContainer()
            ],
            buttons   : [
                this.dialog.getOkButton(),
                this.dialog.getCancelButton(),
                this.dialog.getApplyButton()
            ]
        });
    },

    /**
     * Builds and returns the settings container.
     *
     * @return {com.conjoon.groupware.email.options.folderMapping.SettingsContainer}
     *
     * @protected
     */
    buildSettingsContainer : function()
    {
        return new com.conjoon.groupware.email.options
                   .folderMapping.SettingsContainer({
            region : 'center'
        });
    },

    /**
     * Builds the component that is responsible for showing configurable
     * entries.
     * The default implementation will return a {com.conjoon.cudgets.ListView} component.
     *
     * Override for custom implementation.
     *
     * @return {com.conjoon.cudgets.ListView}
     *
     * @protected
     */
    buildListView : function()
    {
        var store = new Ext.data.Store();

        var recs = com.conjoon.groupware.email.AccountStore.getInstance().getRange();

        var rec = null;
        for (var i = 0, len = recs.length; i < len; i++) {
            rec = recs[i];
            if (rec.get('protocol').toLowerCase() == 'imap') {
                store.add(rec.copy());
            }
        }

        return new com.conjoon.cudgets.ListView({
            region       : 'west',
            width        : 150,
            cls          : 'listView',
            margins      : '5 5 5 5',
            store        : store,
            multiSelect  : false,
            singleSelect : true,
            emptyText    : com.conjoon.Gettext.gettext("No IMAP accounts available"),
            hideHeaders  : true,
            columns      : [{
                dataIndex : 'name'
            }]
        });
    },

    /**
     * Builds and returns the "ok"-button for the dialog.
     *
     * @return {Ext.Button}
     */
    buildOkButton : function()
    {
        return new Ext.Button({
            text : com.conjoon.Gettext.gettext("OK")
        });
    },

    /**
     * Builds and returns the "cancel"-button for the dialog.
     *
     * @return {Ext.Button}
     */
    buildCancelButton : function()
    {
        return new Ext.Button({
            text : com.conjoon.Gettext.gettext("Cancel")
        });
    },

    /**
     * Builds and returns the "apply"-button for the dialog.
     *
     * @return {Ext.Button}
     */
    buildApplyButton : function()
    {
        return new Ext.Button({
            text : com.conjoon.Gettext.gettext("Apply")
        });
    }

};