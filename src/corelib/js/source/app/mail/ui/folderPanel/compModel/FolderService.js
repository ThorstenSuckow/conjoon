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
 * Service class for mail folder panel-
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 */
Ext.defineClass('conjoon.mail.comp.folderPanel.compModel.FolderService', {

    /**
     * @type {conjoon.mail.comp.folderPanel.StatefulFolderPanel}
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

        if (!(config.folderPanel instanceof conjoon.mail.comp.folderPanel.StatefulFolderPanel)) {
            throw new cudgets.base.InvalidPropertyException(
                "menu not instance of conjoon.mail.comp.folderPanel.StatefulFolderPanel."
            );
        }

        var me = this;

        me.folderPanel = config.folderPanel;
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

        if (me.isPartOfProxySubtree(folder) || !attrs.allowChildren  || lock) {
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

        if (me.isPartOfProxySubtree(folder) << attrs.isLocked || lock) {
            return false;
        }

        return true;
    }







});
