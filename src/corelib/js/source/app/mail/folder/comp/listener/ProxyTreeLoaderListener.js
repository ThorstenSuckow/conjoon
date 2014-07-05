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
 * Listener for the ProxyTreeLoader of the account mail folder panel of conjoon.
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 *
 * @class {conjoon.mail.folder.comp.listener.ProxyTreeLoaderListener}
 */
Ext.defineClass('conjoon.mail.folder.comp.listener.ProxyTreeLoaderListener', {

    /**
     * Creates a new instance.
     */
    constructor : function(config) {

        Ext.apply(this, config);

    },

    /**
     * @type {conjoon.mail.folder.comp.StatefulFolderPanel} mailFolderPanel
     */
    mailFolderPanel : null,

    /**
     * @type {Boolean}
     */
    isInit : false,

// -------- api

    /**
     * Installs the listeners for the mailFolderPanel
     */
    init : function()
    {
        var me = this,
            mailFolderPanel = me.mailFolderPanel,
            treeLoader      = mailFolderPanel.treeLoader;

        if (me.isInit) {
            return;
        }

        mailFolderPanel.mon(treeLoader, 'beforeproxynodeload',  me.onBeforeProxyNodeLoad,  me);
        mailFolderPanel.mon(treeLoader, 'proxynodeload',        me.onProxyNodeLoad,        me);
        mailFolderPanel.mon(treeLoader, 'proxynodeloadfailure', me.onProxyNodeLoadFailure, me);

    },

    /**
     * Listener for the beforeproxynodeload event.
     * Publish this event to the Messagebus.
     *
     * @param treeLoader
     * @param node
     */
    onBeforeProxyNodeLoad : function(treeLoader, node) {

        Ext.ux.util.MessageBus.publish('conjoon.mail.MailFolder.beforeproxynodeload', {});

    },

    /**
     * Listener for the proxynodeload event.
     * Publish this event to the Messagebus.
     *
     * @param treeLoader
     * @param node
     * @param validState
     * @param synced
     */
    onProxyNodeLoad : function(treeLoader, node, validState, synced) {

        Ext.ux.util.MessageBus.publish('conjoon.mail.MailFolder.proxynodeload', {});

        if (validState || (!validState && synced)) {
            node.getUI().showProxy(false);
        }

        var me = this,
            parentNode = node.parentNode,
            isProxy = parentNode
                      ? (parentNode instanceof cudgets.tree.data.ProxyTreeNode) &&
                        parentNode.isProxyNode()
                        ? true
                        : false
                       : false;

        if (isProxy) {
            parentNode.loadProxyNode();
        }

    },

    /**
     * Listener for the proxynodeloadfailure event.
     * Publish this event to the Messagebus.
     *
     * @param treeLoader
     * @param node
     * @param {Object} response
     *
     */
    onProxyNodeLoadFailure : function() {

        Ext.ux.util.MessageBus.publish('conjoon.mail.MailFolder.proxynodeloadfailure', {});
    }

});
