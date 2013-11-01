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
     */
    loadProxyNode : function() {

        if (!this.isProxyNode()) {
            return;
        }

        var loader = this.loader || this.attributes.loader ||
            this.getOwnerTree().getLoader();

        loader.loadProxyNode(this, function(items, validSate) {

            this.onProxyNodeLoad(items, validState)

        }, this);
    },

    /**
     *
     * @param items
     *
     * @private
     */
    onProxyNodeLoad : function(children, validState) {
        this.isProxyLoaded = true;
    }



});
