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
 * Listener for the ProxyTreeLoader of the account mail folder panel of conjoon.
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 *
 * @class conjoon.mail.ui.folderPanel.listener.ProxyTreeLoaderListener
 */
Ext.defineClass('conjoon.mail.ui.folderPanel.listener.ProxyTreeLoaderListener', {

    /**
     * Creates a new instance.
     */
    constructor : function(config) {

        Ext.apply(this, config);

    },

    /**
     * @type {conjoon.mail.ui.StatefulFolderPanel} mailFolderPanel
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
