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

Ext.namespace('com.conjoon.cudgets.tree');

/**
 * An override for Ext.tree.AsyncTreeNode with additional methods for convenient
 * access to often needed functionality.
 *
 * The same functionality applies to com.conjoon.cudgets.tree.TreeNode
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 *
 * @class com.conjoon.cudgets.tree.AsyncTreeNode
 * @extends Ext.tree.AsyncTreeNode
 */
com.conjoon.cudgets.tree.AsyncTreeNode = Ext.extend(Ext.tree.AsyncTreeNode, {

    /**
     * Overridden to consider the "loadFailed" attribute of the passed node.
     * If an exception occured during loading, the node will still be able to be
     * expanded.
     *
     * @inheritdoc
     * @ticket CN-883
     */
    loadComplete : function(deep, anim, callback, scope){

        this.loading = false;

        if (this.attributes.loadFailed) {
            return;
        }

        this.loaded = true;
        this.ui.afterLoad(this);
        this.fireEvent("load", this);
        this.expand(deep, anim, callback, scope);
    },

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
    },

    /**
     * Returns true if the passed object equals to this instance based
     * on the available attributes.
     *
     * @param {*} obj
     *
     * return {Boolean}
     *
     * @see com.conjoon.cudgets.tree.TreeNode
     */
    equalsTo : function(obj) {

        var myAttributes = this.attributes,
            targetAttributes = obj.attributes || obj;

        if (myAttributes.text !== targetAttributes.text) {
            return false;
        }

        if (myAttributes.id !== targetAttributes.id) {
            return false;
        }

        if (parseInt(myAttributes.childCount, 10) !== parseInt(targetAttributes.childCount, 10)) {
            return false;
        }

        return true;
    }


});
