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

    /**
     * Whether the proxy was loaded
     * @type {Boolean}
     */
    isProxyLoaded : false,

    /**
     * Whether the proxy is currently loading
     * @type {Boolean}
     */
    isProxyLoading : false,

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
     * returns true if this n
     *
     * @return {Boolean}
     */
    isProxyNodeLoading : function() {
        return this.isProxyNode() &&
               this.isProxyLoading !== false;
    },


    /**
     *
     */
    loadProxyNode : function(config) {

        if (!this.isProxyNode() || this.isProxyNodeLoading()) {
            return;
        }

        var loader = this.loader || this.attributes.loader ||
            this.getOwnerTree().getLoader();

        this.isProxyLoading = loader.loadProxyNode(this, function(items, validSate, synced) {

            if (validState || (!validSate && synced)) {
                this.isProxyLoaded = true;
            }

            this.isProxyLoading = false;

            if (config && config.success && (typeof config.success) == 'function') {
                config.success.apply(config.scope || window, [this, items, validState, synced]);
            }


            this.onProxyNodeLoad(items, validState, synced)

        }, this);

        this.isProxyLoading === null
                                ? false
                                : this.isProxyLoading;

    },

    /**
     *
     * @param items
     * @param {Boolean} validState
     * @param {Boolean} synced
     *
     * @private
     */
    onProxyNodeLoad : function(children, validState, synced) {


    }



});

Ext.namespace('cudgets.tree.data');
cudgets.tree.data.ProxyTreeNode = com.conjoon.cudgets.tree.data.ProxyTreeNode;
