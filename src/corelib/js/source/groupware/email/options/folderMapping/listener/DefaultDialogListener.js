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

Ext.namespace('com.conjoon.groupware.email.options.folderMapping.listener');

/**
 * An  base class that provides the interface for listeners for
 * {com.conjoon.groupware.email.options.folderMapping.Dialog}
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 *
 * @class com.conjoon.groupware.email.options.folderMapping.listener.DefaultDialogListener
 *
 * @constructor
 */
com.conjoon.groupware.email.options.folderMapping.listener.DefaultDialogListener = function() {

};

com.conjoon.groupware.email.options.folderMapping.listener.DefaultDialogListener.prototype = {

    /**
     * @type {com.conjoon.groupware.email.options.folderMapping.Dialog} dialog The dalog
     * this listener is bound to.
     */
    dialog : null,

// -------- api

    /**
     * Installs the listeners for the elements found in the dialog.
     *
     * @param {com.conjoon.groupware.email.options.folderMapping.Dialog} dialog
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

        dialog.mon(
            dialog.getListView(), 'entrydeselect',
            this.onListViewEntryDeselect, this
        );

        dialog.mon(
            dialog.getListView(), 'entryselect',
            this.onListViewEntrySelect, this
        );
    },

// -------- helper

// ------- listeners

    /**
     * Listener for the listView's "entryselect" event.
     *
     * @param {com.conjoon.cudgets.ListView} listView
     * @param {Ext.data.Record} record
     */
    onListViewEntrySelect : function(listView, record)
    {
        this.dialog.getSettingsContainer().showOptionsContainer();

    },

    /**
     * Listener for the listView's "entrydeselect" event.
     *
     * @param {com.conjoon.cudgets.ListView} listView
     * @param {Ext.data.Record} record
     */
    onListViewEntryDeselect : function(listView, record)
    {
        this.dialog.getSettingsContainer().showIntroductionCard();
    }

};