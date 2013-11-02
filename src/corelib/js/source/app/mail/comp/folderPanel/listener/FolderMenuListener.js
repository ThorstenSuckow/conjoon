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


/**
 * Default listener for the stateful folder panel context menu
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
Ext.defineClass('conjoon.mail.comp.folderPanel.listener.FolderMenuListener', {

    /**
     * @type {Boolean}
     */
    isInit : false,

    /**
     * @type {conjoon.mail.comp.folderPanel.FolderMenu}
     */
    menu : null,

    /**
     * @type {conjoon.mail.comp.folderPanel.compModel.FolderService}
     */
    folderService : null,

    /**
     * Creates a new instance.
     *
     * @throws {cudgets.base.InvalidPropertyException}
     */
    constructor : function(config) {

        var me = this;

        if (!config || !config.menu) {
            throw new cudgets.base.InvalidPropertyException(
                "no valid menu configured for listener."
            );
        }

        if (!(config.menu instanceof conjoon.mail.comp.folderPanel.FolderMenu)) {
            throw new cudgets.base.InvalidPropertyException(
                "menu not instance of conjoon.mail.comp.folderPanel.FolderMenu."
            );
        }

        if (!config.folderService ||
            !(config.folderService instanceof conjoon.mail.comp.folderPanel.compModel.FolderService)) {
            throw new cudgets.base.InvalidPropertyException("folderService not properly configured for menuListener");
        }

        me.menu = config.menu;
        me.folderService = config.folderService;
    },

    /**
     * Attaches this listener to the events of the menu.
     */
    init : function() {

        var me = this;

        if (me.isInit) {
            return;
        }

        me.isInit = true;

        me.menu.on('beforeshowforfolder', me.onBeforeShowForFolder, me);
    },

// listeners --------

    /**
     * Callback for the onbeforeshowforfolder.
     * Enables/disables menu entries based on the attributes of the
     * specified folder
     *
     * @param {conjoon.mail.comp.folderPanel.FolderMenu}
     * @param {Ext.tree.TreeNode}
     */
    onBeforeShowForFolder : function(menu, folder)
    {
        var me = this,
            attrs = folder.attributes,
            lock = folder.disabled;

        menu.menuItemNewFolder.setDisabled(false);
        menu.menuItemRenameFolder.setDisabled(false);
        menu.menuItemDeleteFolder.setDisabled(false);

        if (!me.folderService.isAddingChildrenAllowed(folder)) {
            menu.menuItemNewFolder.setDisabled(true);
        }

        if (!me.folderService.isRenamingAllowed(folder)) {
            menu.menuItemRenameFolder.setDisabled(true);
        }

        if (!me.folderService.isRemovingAllowed(folder)) {
            menu.menuItemDeleteFolder.setDisabled(true);
        }

    }


});
