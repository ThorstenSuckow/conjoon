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

Ext.namespace('com.conjoon.groupware.email.options');

/**
 * Baton for handling the Dialog for FolderMappings.
 * Instead of creating new instances of
 * com.conjoon.groupware.email.options.folderMapping.Dialog directly, the API
 * of this singleton should be used instead so that no more than one dialog
 * can be created at a time.
 *
 *
 * @class com.conjoon.groupware.email.options.FolderMappingDialog
 * @singleton
 */
com.conjoon.groupware.email.options.FolderMappingBaton = function() {

    /**
     * @type {com.conjoon.groupware.email.options.folderMapping.Dialog} dialog
     */
    var dialog = null;

    return {

        /**
         * Returns true if there is currently an instance of
         * com.conjoon.groupware.email.options.folderMapping.Dialog active,
         * otherwise false.
         *
         * @return {Boolean}
         */
        isDialogActive : function()
        {
            return false;
        },

        /**
         * Creates and shows an instance of
         * com.conjoon.groupware.email.options.folderMapping.Dialog.
         *
         * @param {Number} accountId The account id for which the dialog
         * is created.
         * @param {String} type The type that should be mapped to a folder,
         * "INBOX", "TRASH", "DRAFT", "SENT" or "OUTBOX".
         */
        showDialog : function(accountId, type)
        {
            if (this.isDialogActive()) {
                return;
            }

            dialog = new com.conjoon.groupware.email.options.folderMapping.Dialog({
                accountId : accountId,
                type      : type
            });

            dialog.show();
        }

    };


}();