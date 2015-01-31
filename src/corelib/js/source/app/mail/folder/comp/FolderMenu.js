/**
 * conjoon
 * (c) 2007-2015 conjoon.org
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
 * Default context menu for the folder panel
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
Ext.defineClass('conjoon.mail.folder.comp.FolderMenu', {

    extend : 'Ext.menu.Menu',

    /**
     * @type {conjoon.mail.comp.folderPanel.FolderMenuListener}
     */
    folderMenuListener : null,

    /**
     * @type {Ext.menu.Item}
     */
    menuItemNewFolder : null,

    /**
     * @type {Ext.menu.Item}
     */
    menuItemRenameFolder : null,

    /**
     * @type {Ext.menu.Item}
     */
    menuItemDeleteFolder : null,

    /**
     * @type {conjoon.mail.comp.folderPanel.compModel.FolderService}
     */
    folderService : null,

    /**
     * @inheritdoc
     *
     * @throws {cudgets.base.InvalidPropertyException}
     */
    initComponent : function() {

        var me = this;

        if (!me.folderService ||
            !(me.folderService instanceof conjoon.mail.folder.data.FolderService)) {
            throw new cudgets.base.InvalidPropertyException("folderService not properly configured for menu");
        }

        me.addEvents(
            /**
             * @event beforeshowforfolder
             * Fires before this menu gets shown for the folder passed in the
             * arguments. Return false to cancel showing the menu.
             * @param {Menu} menu the menu
             * @param {Folder} folder The folder for which the menu should be shown
             */
            'beforeshowforfolder'
        );

        me.menuItemNewFolder = Ext.createInstance('Ext.menu.Item',{
            id   : 'com.conjoon.groupware.email.EmailTree.nodeContextMenu.newItem',
            text : com.conjoon.Gettext.gettext("New folder")
        });

        me.menuItemRenameFolder = Ext.createInstance('Ext.menu.Item',{
            id   : 'com.conjoon.groupware.email.EmailTree.nodeContextMenu.renameItem',
            text : com.conjoon.Gettext.gettext("Rename folder")
        });

        me.menuItemDeleteFolder = Ext.createInstance('Ext.menu.Item',{
            id   : 'com.conjoon.groupware.email.EmailTree.nodeContextMenu.deleteItem',
            text : com.conjoon.Gettext.gettext("Delete...")
        });

        me.items = [
            me.menuItemNewFolder,
            me.menuItemRenameFolder,
            '-',
            me.menuItemDeleteFolder
        ];

        me.installListeners();

        conjoon.mail.folder.comp.FolderMenu.superclass.initComponent.call(me);
    },

// -------- API

    /**
     * Show the menu for the specified folder at the specified
     * position
     *
     * @param {Ext.tree.TreeNode} folder
     * @param {Object} position
     */
    showForFolderAt : function(folder, position) {

        var me = this;

        if (!me.fireEvent('beforeshowforfolder', me, folder) === false) {
            me.showAt(position);
        }

    },

    /**
     * Installs the listener for this component.
     *
     *
     */
    installListeners : function() {

        var me = this;

        me.getFolderMenuListener().init();
    },

    /**
     * Returns the FolderMenuListener as the folderMenuLister for
     * this instance
     *
     * @return {conjoon.mail.folder.comp.listener.FolderMenuListener}
     */
    getFolderMenuListener : function() {

        var me = this;

        if (!me.folderMenuListener) {
            me.folderMenuListener = Ext.createInstance(
                'conjoon.mail.folder.comp.listener.FolderMenuListener', {
                    menu : me,
                    folderService : me.folderService
                });
        }

        return me.folderMenuListener;
    }

});
