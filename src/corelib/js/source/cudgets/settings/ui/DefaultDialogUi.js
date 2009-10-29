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

Ext.namespace('com.conjoon.cudgets.settings.ui');

/**
 * Builds and layouts the SettingsDialog's layout and its components.
 *
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 *
 * @class com.conjoon.cudgets.settings.ui.DefaultDialogUi
 */
com.conjoon.cudgets.settings.ui.DefaultDialogUi = function(config) {

    config = config || {};

    Ext.apply(this, config);
};

com.conjoon.cudgets.settings.ui.DefaultDialogUi.prototype = {

    /**
     * @cfg {Number} height The height for the dialog. Any setting for the dialog
     * will preceed this setting. If neither specified, defaults to 375.
     */
    height : 375,

    /**
     * @cfg {Number} width The width for the dialog. Any setting for the dialog
     * will preceed this setting. If neither specified, defaults to 550.
     */
    width : 550, 

    /**
     * @cfg {String} title The title for the dialog. Defaults to the localized
     * translation of "Settings"
     */
    title : com.conjoon.Gettext.gettext("Settings"),
    
    /**
     * @cfg {String} iconCls The iconCls to use for showing an icon in the
     * header. Defaults to 'settingsIcon'.
     */
    iconCls : 'settingsIcon',
    
    /**
     * @cfg {String} okButtonTitle The title for the ok button. Defaults to the 
     * localized translation of "OK".
     */
    okButtonTitle : com.conjoon.Gettext.gettext("OK"),
    
    /**
     * @cfg {String} cancelButtonTitle The title for the cancel button. Defaults 
     * to the localized translation of "Cancel".
     */
    cancelButtonTitle : com.conjoon.Gettext.gettext("Cancel"),    
    
    /**
     * @cfg {String} applyButtonTitle The title for the apply button. Defaults 
     * to the localized translation of "Apply".
     */
    applyButtonTitle : com.conjoon.Gettext.gettext("Apply"),        

    /**
     * @cfg {com.conjoon.cudgets.settings.listener.DefaultDialogListener} actionListener
     * The actionListener for the dialog this ui class manages. If not provided,
     * defaults to {com.conjoon.cudgets.settings.listener.DefaultDialogListener}
     */
    actionListener : null,

    /**
     * @type {Ext.Window} dialog The dialog this ui class manages. Gets assigned in the init()
     * method.
     */
    dialog : null,

    /**
     * Inits the layout of the dialog.
     * gets called from the initComponent's "initComponent()" method.
     *
     * @param {Ext.Dialog} dialog The dialog this ui will manage.
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
            this.actionListener = new com.conjoon.cudgets.settings.listener.DefaultDialogListener();
        }

        this.actionListener.init(this.dialog);
    },

    /**
     *
     * @protected
     */
    buildDialog : function()
    {
        Ext.apply(this.dialog, {
            layout  : 'fit',
            cls     : 'com-conjoon-cudgets-SettingsDialog',
            iconCls : this.iconCls,
            title   : this.title,
            height  : this.dialog.height ? this.dialog.height : this.height,
            width   : this.dialog.width  ? this.dialog.width  : this.width,
            items  : [
                this.dialog.getSettingsContainer()
            ], 
            buttons : [
                this.dialog.getOkButton(),
                this.dialog.getCancelButton(),
                this.dialog.getApplyButton()
            ]
        });
    },

    /**
     *
     * @return {Ext.Button}
     */
    buildOkButton : function()
    {
        return new Ext.Button({
            text : this.okButtonTitle    
        });
    },

    /**
     *
     * @return {Ext.Button}
     */
    buildCancelButton : function()
    {
        return new Ext.Button({
            text : this.cancelButtonTitle    
        });
    },
    
    /**
     *
     * @return {Ext.Button}
     */
    buildApplyButton : function()
    {
        return new Ext.Button({
            text     : this.applyButtonTitle,
            disabled : true
        });
    },    

    /**
     * Renderes the controls of the dialog eitehr enabled or disabled
     * to prevent further actions that would be invokec when interacting
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
    } 

};