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

Ext.namespace('com.conjoon.service.twitter.optionsDialog');

/**
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 *
 * @class com.conjoon.service.twitter.optionsDialog.DialogListener
 * @extends cudgets.settings.listener.DefaultDialogListener
 */
com.conjoon.service.twitter.optionsDialog.DialogListener = Ext.extend(
    com.conjoon.cudgets.settings.listener.DefaultDialogListener, {

    /**
     * @type {String} clsId
     */
    clsId : '7afa3364-61ab-40a0-ad15-cfb4ab9dc37d',


// -------- api

    /**
     * Installs the listeners for the elements found in the dialog.
     *
     * @param {com.conjoon.cudgets.settings.Dialog} dialog The settings dialog
     * this listener is bound to.
     *
     * @packageprotected
     */
    init : function(dialog)
    {
        if (this.dialog) {
            return;
        }

        com.conjoon.service.twitter.optionsDialog.DialogListener.superclass.init.call(this, dialog);

        this.dialog.on('render', this.onRender, this);
    },

    /**
     * Listener for the render event. Checks if any accounts are available in the store.
     * If not, the Wizard pops up automatically.
     *
     */
    onRender : function()
    {
        if (com.conjoon.service.twitter.data.AccountStore.getInstance().getRange().length == 0) {
            com.conjoon.service.twitter.wizard.AccountWizardBaton.show({defer : 500});
        }
    }

});