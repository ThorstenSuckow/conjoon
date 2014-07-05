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
            return null;
        }

        var loader = this.loader || this.attributes.loader ||
            this.getOwnerTree().getLoader(),
            retVal = null;

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

        retVal = this.isProxyLoading || null;

        this.isProxyLoading === null
                                ? false
                                : this.isProxyLoading;

        return retVal;
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
