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

Ext.namespace('com.conjoon.cudgets.tree');

/**
 * An override for Ext.tree.TreeNode with additional methods for convenient
 * access to often needed functionality.
 *
 * The same fucntionality applies to com.conjoon.cudgets.tree.AsyncTreeNode
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 *
 * @class com.conjoon.cudgets.tree.TreeNode
 * @extends Ext.tree.TreeNode
 */
com.conjoon.cudgets.tree.TreeNode = Ext.extend(Ext.tree.TreeNode, {

    /**
     * Similiar to getPath, this method returns the path for this node, but
     * instead of a string it returns the path as an array, whereas the first
     * index points to the root, and the last index to "this" node.
     *
     * @param {String} attr (optional) The attr to use for the path
     * (defaults to the node's id)
     *
     * @return {Array} The path
     */
    getPathAsArray : function(attr)
    {
        attr = attr || "id";
        var p = this.parentNode;
        var b = [this.attributes[attr]];
        while(p){
            b.unshift(p.attributes[attr]);
            p = p.parentNode;
        }

        return b;
    },

    /**
     * Similiar to getPathAsArray, returns the result as json encoded string.
     *
     * @param {String} attr (optional) The attr to use for the path
     * (defaults to the node's id)
     *
     * @return {String} An array with the path parts, json encoded
     */
    getPathAsJson : function(attr)
    {
        return Ext.util.JSON.encode(this.getPathAsArray(attr));
    }



});