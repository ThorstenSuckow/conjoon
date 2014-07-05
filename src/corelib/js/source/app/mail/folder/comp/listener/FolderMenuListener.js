/**
 * conjoon
 * (c) 2007-2014 conjoon.org
 * licensing@conjoon.org
 *
 * conjoon
 * Copyright (C) 2014 Thorsten Suckow-Homberg/conjoon.org
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
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
Ext.defineClass('conjoon.mail.folder.comp.listener.FolderMenuListener', {

    /**
     * @type {Boolean}
     */
    isInit : false,

    /**
     * @type {conjoon.mail.folder.comp.FolderMenu}
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

        if (!(config.menu instanceof conjoon.mail.folder.comp.FolderMenu)) {
            throw new cudgets.base.InvalidPropertyException(
                "menu not instance of conjoon.mail.folder.comp.FolderMenu"
            );
        }

        if (!config.folderService ||
            !(config.folderService instanceof conjoon.mail.folder.data.FolderService)) {
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
     * @param {conjoon.mail.folder.comp.FolderMenu}
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
