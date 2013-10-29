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

Ext.namespace('com.conjoon.cudgets.tree.data');

/**
 * A specific implementation for an AsynctreeNode which can confirm it's state
 * by querying the backend before interaction with it is allowed.
 *
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 *
 * @class com.conjoon.cudgets.tree.data.ProxyTreeNode
 * @extends Ext.tree.AsyncTreeNode
 */
com.conjoon.cudgets.tree.data.ProxyTreeNode = Ext.extend(com.conjoon.cudgets.tree.AsyncTreeNode, {

    isProxyLoaded : false,

    /**
     * returns true if this node is still a proxy and was not
     * loaded from the server
     *
     * @return {Boolean}
     */
    isProxyNode : function() {
        return this.isProxyLoaded !== true;
    },

    /**
     *
     * @param {Object} config config object with:
     *  - cb: callback function
     *  - scope: scope of callback function
     *
     */
    loadAndSelectProxyNode : function(config) {

        if (!this.isProxyNode()) {
            return;
        }

        var loader = this.loader || this.attributes.loader || this.getOwnerTree().getLoader();

        loader.loadAndSelectProxyNode(this, function(nodes){
            this.isProxyLoaded = true;
        }, this);
    },

    /**
     *
     * @param proxyChilds
     * @param items
     * @return {Boolean}
     */
    compareProxyChildrenWithLoadedItems : function(proxyChilds, items) {

        if (proxyChilds.length !== items.length) {
            return false;
        }

        var proxyNode, item, attributes;
        for (var i = 0, len = proxyChilds.length; i < len; i++) {
            proxyNode = proxyChilds[i];
            attributes = proxyNode.attributes
            item = items[i];

            if (attributes.text !== item.name) {
                return false;
            }
            if (attributes.id !== item.id) {
                return false;
            }
            if (attributes.childCount !== item.childCount) {
                return false;
            }
        }

        return true;
    }


});
