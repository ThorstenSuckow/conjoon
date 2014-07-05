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
 * Service class for mail folder panel.
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
Ext.defineClass('conjoon.mail.folder.data.FolderService', {

    /**
     * @type {conjoon.mail.folder.comp.StatefulFolderPanel}
     */
    folderPanel : null,

    /**
     * Creates a new instance.
     *
     * @throws {cudgets.base.InvalidPropertyException}
     */
    constructor : function(config) {

        if (!config || !config.folderPanel) {
            throw new cudgets.base.InvalidPropertyException(
                "no valid folderPanel configured for service."
            );
        }

        if (!(config.folderPanel instanceof conjoon.mail.folder.comp.StatefulFolderPanel)) {
            throw new cudgets.base.InvalidPropertyException(
                "menu not instance of conjoon.mail.folder.comp.StatefulFolderPanel"
            );
        }

        var me = this;

        me.folderPanel = config.folderPanel;
    },

    /**
     * Returns true if any of the specified node's parentNode is currently
     * being synchronized as a proxy node, i.e. isProxyNodeLoading() returns true.
     *
     * Please be aware that this method should not be used to check whether the
     * specified node is part of a proxy subtree.
     *
     * @param folder
     *
     * @return {*} The first node found being loaded, or false if none found
     */
    isAnyParentProxyNodeLoading : function (folder) {

        var me = this;

        while (folder) {
            if (me.isProxy(folder) && folder.isProxyNodeLoading()) {
                return folder;
            }

            folder = folder.parentNode;
        }

        return false;
    },

    /**
     * Inits loadProxyNode() on the next available parentNode which is a proxy in the
     * subtree of the specified node.
     *
     * WARNING! If the method returns null, it is possible that the requested node was
     * part of a proxy subtree AND that there was already a loadProxyNode() initiated
     * on this or any other node in this subtree, which is still prcessing.
     * If the method returns null it is no indicator for the case that no proxyNode is
     * available in the folder subtree.
     *
     * @param folder
     *
     * @return {*} Returns the node which triggered a proxy load, or null if none was found
     */
    loadNextParentProxyNode : function(folder) {

        var me = this;

        while (folder) {
            if (me.isProxy(folder)) {
                return folder.loadProxyNode();
            }

            folder = folder.parentNode;
        }

        return null;
    },

    /**
     * Returns true if the specified folder belongs to a subtree which consists
     * of at least one proxied parent node
     *
     * @param {Ext.tree.TreeNode} folder
     *
     * @return {Boolean}
     */
    isPartOfProxySubtree : function(folder) {

        var me = this;

        while (folder) {

            if (me.isProxy(folder)) {
                return true;
            }

            folder = folder.parentNode;
        }

        return false;
    },

    /**
     * Returns true if the specified node is a proxy node, otherwise false.
     *
     * @param {Ext.tree.TreeNode} folder
     *
     * @return {Boolean}
     */
    isProxy : function(folder) {

        return (folder instanceof cudgets.tree.data.ProxyTreeNode) &&
                folder.isProxyNode();

    },

    /**
     * Returns true if the current state of the specified folder allows for
     * adding children, otherwise false.
     *
     * @param {Ext.tree.TreeNode} folder
     *
     * @return {Boolean}
     */
    isAddingChildrenAllowed : function(folder) {

        var me = this,
            attrs = folder.attributes,
            lock = folder.disabled;

        if (me.isPartOfProxySubtree(folder) || !attrs.allowChildren || lock) {
            return false;
        }

        return true;
    },

    /**
     * Returns true if the current state of the specified folder allows for
     * renaming it, otherwise false.
     *
     * @param {Ext.tree.TreeNode} folder
     *
     * @return {Boolean}
     */
    isRenamingAllowed : function(folder) {

        var me = this,
            attrs = folder.attributes,
            lock = folder.disabled;


        if (me.isPartOfProxySubtree(folder) || attrs.isLocked || lock) {
            return false;
        }

        return true;
    },

    /**
     * Returns true if the current state of the specified folder allows for
     * removing it from its parent node, otherwise false.
     *
     * @param {Ext.tree.TreeNode} folder
     *
     * @return {Boolean}
     */
    isRemovingAllowed : function(folder) {

        var me = this,
            attrs = folder.attributes,
            lock = folder.disabled;

        if (me.isPartOfProxySubtree(folder) || attrs.isLocked || lock) {
            return false;
        }

        return true;
    },

    /**
     * Returns true if pathToFolder1 is a parent folder of pathToFolder2.
     * Returns true if pathToFolder2 is a child folder of pathToFolder1
     *
     * @param {Array} pathToFolder1
     * @param {Array} pathToFolder2
     *
     * @return {Boolean}
     *
     * @throws {cudgets.base.InvalidArgumentException} if any of the specified arguments
     * is not an array
     */
    isParentPath : function(pathToFolder1, pathToFolder2) {

        if ((Object.prototype.toString.call(pathToFolder1)).toLowerCase() !== '[object array]') {
            throw new cudgets.base.InvalidArgumentException("pathToFolder1 is not of type Array");
        }

        if ((Object.prototype.toString.call(pathToFolder2)).toLowerCase() !== '[object array]') {
            throw new cudgets.base.InvalidArgumentException("pathToFolder2 is not of type Array");
        }

        if (pathToFolder1.length > pathToFolder2.length) {
            return false;
        }

        for (var i = 0, len = pathToFolder1.length; i < len; i++) {
            if (pathToFolder1[i] != pathToFolder2[i]) {
                return false;
            }
        }

        return true;
    }

});
