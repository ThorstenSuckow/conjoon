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

    /**
     * @type {Boolean} isNoticeShown Set to true if the confirm dialog is shown via
     * "showNotice()"
     */
    var isNoticeShown = false;

    /**
     * Listener for the dialogs "close" event. Will set the dialog property of
     * this singleton to "null".
     *
     * @param {Ext.Window} panel
     */
    var onDialogClose = function(panel)
    {
        dialog = null;
    };

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
            return dialog !== null;
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

            dialog.on('close', onDialogClose, this);

            dialog.show();
        },

        /**
         * Shows a notice that for the specified accountId and the specified type
         * was no default folder found. The user is shown a confirm dialog that
         * he can map a folder for the specified type.
         *
         * @param {Number} accountId
         * @param {String} type
         */
        showNotice : function(accountId, type)
        {
            if (!this.isDialogActive() && !isNoticeShown) {
                isNoticeShown = true;
                var name = com.conjoon.groupware.email.AccountStore.getInstance()
                           .getById(accountId).get('name');

                var title = "";
                var msg   = "";

                switch (type) {
                    case 'INBOX':
                        title = com.conjoon.Gettext.gettext("No \"Inbox\" folder found");
                        msg = String.format(
                            com.conjoon.Gettext.gettext("There was no valid \"Inbox\" folder found for the account \"{0}\". Before you can fetch messages for this account, you need to specify a folder that will be used as the default \"Inbox\" folder. <br />Do you want to choose an \"Inbox\" folder now?"),
                            name
                        );
                    break;

                    case 'TRASH':
                        title = com.conjoon.Gettext.gettext("No \"Trash\" folder found");
                        msg = String.format(
                            com.conjoon.Gettext.gettext("There was no valid \"Trash\" folder found for the account \"{0}\". Before you can fetch messages for this account, you need to specify a folder that will be used as the default \"Trash\" folder. <br />Do you want to choose a \"Trash\" folder now?"),
                            name
                        );
                    break;

                    case 'SENT':
                        title = com.conjoon.Gettext.gettext("No \"Sent\" folder found");
                        msg = String.format(
                            com.conjoon.Gettext.gettext("There was no valid \"Sent\" folder found for the account \"{0}\". Before you can fetch messages for this account, you need to specify a folder that will be used as the default \"Sent\" folder. <br />Do you want to choose a \"Sent\" folder now?"),
                            name
                        );
                    break;

                    case 'OUTBOX':
                        title = com.conjoon.Gettext.gettext("No \"Outbox\" folder found");
                        msg = String.format(
                            com.conjoon.Gettext.gettext("There was no valid \"Outbox\" folder found for the account \"{0}\". Before you can fetch messages for this account, you need to specify a folder that will be used as the default \"Outbox\" folder. <br />Do you want to choose an \"Outbox\" folder now?"),
                            name
                        );
                    break;

                    case 'DRAFT':
                        title = com.conjoon.Gettext.gettext("No \"Draft\" folder found");
                        msg = String.format(
                            com.conjoon.Gettext.gettext("There was no valid \"Draft\" folder found for the account \"{0}\". Before you can fetch messages for this account, you need to specify a folder that will be used as the default \"Draft\" folder. <br />Do you want to choose a \"Draft\" folder now?"),
                            name
                        );
                    break;

                    default:
                        throw(
                            "Type \""+type+"\" is unknown to "
                            +"com.conjoon.groupware.email.options.FoldermappingBaton"
                        );
                    break;
                }

                com.conjoon.SystemMessageManager.confirm(
                    new com.conjoon.SystemMessage({
                        title : title,
                        text  : msg,
                        type  : com.conjoon.SystemMessage.TYPE_CONFIRM
                    }), {
                        fn : function(buttonString) {
                            if (buttonString === 'yes') {
                                this.showDialog(accountId, 'INBOX');
                            }
                            isNoticeShown = false;
                        },
                        scope : this
                    }
                );
            }
        }

    };


}();