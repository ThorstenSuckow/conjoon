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
 *
 * @class {conjoon.mail.comp.folderPanel.StatefulFolderPanel}
 */
Ext.defineClass('conjoon.mail.comp.folderPanel.StatefulFolderPanel', {

    extend : 'com.conjoon.groupware.email.EmailTree',

    /**
     * @type {conjoon.mail.comp.folderPanel.listener.ProxyTreeLoaderListener}
     */
    proxyTreeLoaderListener : null,

    /**
     * @type {conjoon.mail.data.mailFolder.SynchronizationQueue}
     */
    folderSyncQueue : null,

// -------- superclass
    /**
     * @type {Boolean}
     */
    stateful : true,

    /**
     * @inheritdoc
     */
    initComponent : function() {

        var me = this;

        if (!me.stateId) {
            throw new conjoon.state.MissingStateIdException("Missing stateId");
        }

        // state events gets initialiued once roo was loaded
        // see applyState
        me.stateEvents = [];

        /**
         * The loader responsible for loading nodes into the tree.
         * Events will be captured by the onNodeLoaded method.
         */
        me.treeLoader = Ext.createInstance('com.conjoon.groupware.email.EmailTreeLoader', {
            directFn : com.conjoon.groupware.provider.emailFolder.getFolder,
            baseAttrs : {
                uiProvider : com.conjoon.groupware.email.PendingNodeUI
            }
        });

        me.folderSyncQueue = Ext.createInstance('conjoon.mail.data.mailFolder.SynchronizationQueue', {
            proxyTreeLoader : me.treeLoader
        });

        conjoon.mail.comp.folderPanel.StatefulFolderPanel.superclass.initComponent.call(this);
    },

    /**
     * @inheritdoc
     */
    initEvents : function() {

        var me = this;

        conjoon.mail.comp.folderPanel.StatefulFolderPanel.superclass.initEvents.call(me);

        me.installListeners();
    },

    /**
     * @inheritdoc
     */
    getFolderMenu : function() {

        var me = this;

        if (!me.contextMenu) {
            me.contextMenu = Ext.createInstance('conjoon.mail.comp.folderPanel.FolderMenu', {
                folderService : me.getFolderService()
            });
        }

        return me.contextMenu;

    },

    /**
     * @inheritdoc
     */
    deleteFolder : function(clkNode)
    {
        if (!clkNode) {
            clkNode = this.clkNode;
        }

        var me = this,
            clkPath = clkNode.getPathAsArray('idForPath'),
            currP = clkPath[1],
            trashId = this.findPathFor(currP, 'trash'),
            rootNode = this.getNodeById(currP),
            parentNode = clkNode.parentNode,
            proxyToCheck;

        if (clkNode.getPath('type').indexOf('/trash') != -1) {

            var to = this.getNodeForPath('/' + trashId.join('/'));

            if (me.folderService.isProxy(to)) {

                // trashNode is still a proxy. reload it first
                this.folderSyncQueue.addJobForNodeAndEvent(
                    to.id, 'proxynodeload', {
                        id : clkNode.id,
                        fn : function(treeLoader, proxyNode, validState, synced) {

                            if (!validState && !synced) {
                                // show node again
                                clkNode.getUI().show();
                                return;
                            }

                            this.deleteFolder(clkNode);
                        },
                        scope : this
                    });

                // hide node
                clkNode.unselect();
                clkNode.getUI().hide();
                to.loadProxyNode();
                return;
            }

            var nodeId   = clkNode.id,
                nodePath = clkNode.getPathAsJson('idForPath');
            clkNode.remove();
            this.clkNode = null;

            Ext.Ajax.request({
                url    : './groupware/email.folder/delete.folder/format/json',
                params : {
                    path : nodePath,
                    id   : nodeId
                },
                disableCaching : true
            });

        } else {

            // load proxyNode first if node itself or parentnode is a proxy
            if (me.folderService.isProxy(clkNode)) {
                proxyToCheck = clkNode;
            }

            if (!proxyToCheck) {
                if (parentNode && me.folderService.isProxy(parentNode)) {
                    proxyToCheck = parentNode;
                }
            }

            if (proxyToCheck) {

                this.folderSyncQueue.addJobForNodeAndEvent(
                    proxyToCheck.id, 'proxynodeload', {
                        fn : function(treeLoader, proxyNode, validState, synced) {

                            if (!validState && !synced) {
                                return;
                            }
                            this.deleteFolder(clkNode);
                        },
                        scope : this
                    });

                proxyToCheck.loadProxyNode();

                return;
            }

            if (!trashId) {

                // check here if the root is a proxy node which needs to be loaded first
                if (me.folderService.isProxy(rootNode)) {

                    this.folderSyncQueue.addJobForNodeAndEvent(
                        rootNode.id, 'proxynodeload', {
                            fn : function(treeLoader, proxyNode, validState, synced) {

                                if (!validState && !synced) {
                                    conjoon.SystemMessage.warn({
                                        title : com.conjoon.Gettext.gettext("No trashbin found"),
                                        text  : com.conjoon.Gettext.gettext("Synchronizing the mail folder did not succeed, no trash bin is available. Please close all folders and reload the application.")
                                    });
                                }
                            },
                            scope : this
                    });

                    if (!rootNode.isProxyNodeLoading()) {
                        rootNode.loadProxyNode();
                    };

                    return;

                }

                if (rootNode.attributes.type == 'root' || rootNode.attributes.type == 'accounts_root') {
                    conjoon.SystemMessage.warn({
                        title : com.conjoon.Gettext.gettext("No trashbin found"),
                        text  : com.conjoon.Gettext.gettext("No trashbin for the selected folder found. Please close all folders, then reload the application")
                    });
                } else {
                    conjoon.SystemMessage.warn({
                        title : com.conjoon.Gettext.gettext("No trashbin found"),
                        text  : com.conjoon.Gettext.gettext("No trashbin for the selected folder found. Have you configured the account's folder mappings?")
                    });
                }

                return;
            }

            var to = this.getNodeForPath('/' + trashId.join('/'));

            if (me.folderService.isProxy(to)) {

                // trashNode is still a proxy. reload it first
                this.folderSyncQueue.addJobForNodeAndEvent(
                    to.id, 'proxynodeload', {
                        fn : function(treeLoader, proxyNode, validState, synced) {
                            if (validState || (!validState && synced)) {
                                this.deleteFolder(clkNode);
                            }
                        },
                        scope : this
                    });

                return;
            }

            if (!to) {
                conjoon.SystemMessage.warn({
                    title   : com.conjoon.Gettext.gettext("No trashbin found"),
                    text     : com.conjoon.Gettext.gettext("No trashbin for the current account found. Maybe it is not expanded?")
                });

                return;
            }

            clkNode.attributes.tmpPath = clkNode.getPathAsJson('idForPath');
            this.moveFolderToTrash(this, to, clkNode);
        }
    },

// -------- API

    /**
     * Installs the state events once the state of the tree was fully restored.
     * This method will also add a listener to the panel's "scroll" event to make sure
     * the scroll position is persisted.
     *
     */
    installStateEvents : function() {

        var me = this;

        me.stateEvents = [
            'expandnode', 'collapsenode', 'drop', 'append', 'remove', 'resize',
            'collapse', 'expand'
        ];

        me.mon(me.body, 'scroll', me.saveState, me, {delay : 1000});
        me.mon(me.getSelectionModel(), 'selectionchange', me.saveState, me, {delay : 100});
        me.initStateEvents();
    },

    /**
     *
     * @return {Object}
     */
    installListeners : function() {
        var me = this;

        me.getProxyTreeLoaderListener().init();
    },

    /**
     * Returns the instance for the treeloader listener used for this panel.
     *
     * @return {conjoon.mail.comp.folderPanel.listener.ProxyTreeLoaderListener}
     */
    getProxyTreeLoaderListener : function() {

        var me = this;

        if (me.proxyTreeLoaderListener) {
            return me.proxyTreeLoaderListener;
        }

        me.proxyTreeLoaderListener = Ext.createInstance(
            'conjoon.mail.comp.folderPanel.listener.ProxyTreeLoaderListener', {
            mailFolderPanel : me
        });

        return me.proxyTreeLoaderListener;
    },

// -------- state

    /**
     *
     * @return {Object}
     */
    getState : function() {

        var state, nodes = this.getRootNode().childNodes,
            paths = {}, node, selectedNode;

        var extractAttributes = function(node) {

            return {
                id : node.attributes.id,
                idForPath : node.attributes.idForPath,
                name : node.attributes.text,
                isChildAllowed : node.attributes.isChildAllowed,
                isLocked : node.attributes.isLocked,
                type : node.attributes.type,
                childCount : node.attributes.childCount,
                pendingCount : node.attributes.pendingCount,
                isSelectable : node.attributes.isSelectable ? 1 : 0
            };
        };

        var getPaths = function(nodes, ignoreExpand) {

            var node = null, map = {};

            for (var i = 0, len = nodes.length; i < len; i++) {
                node = nodes[i];

                if (ignoreExpand === true || (node.isExpanded() && node.childNodes.length != 0)) {

                    var childNodes = node.childNodes, children = {}, cn, u, lenu;

                    for (u = 0, lenu = childNodes.length; node.isExpanded() && u < lenu; u++) {
                        cn = extractAttributes(childNodes[u]);
                        if (childNodes[u].isExpanded()) {
                            cn.children = getPaths(childNodes[u].childNodes, true);
                            cn.isProxy = true;
                        }
                        children[cn.id] = cn;
                    }

                    map[node.id] = extractAttributes(node);
                    map[node.id].isProxy = node.isExpanded() && lenu > 0;
                    map[node.id].children = node.isExpanded() ? children : null;
                }
            }

            return map;
        };

        selectedNode = this.getSelectionModel().getSelectedNode();

        state = {
            proxyNodes     : Ext.util.JSON.encode(getPaths(nodes)),
            scrollTop      : this.body.dom.scrollTop,
            collapsed      : this.collapsed,
            width          : this.getWidth(),
            selectedNodeId : selectedNode
                             ? selectedNode.id
                             : null
        };

        return state;
    },


    /**
     *
     * @param state
     */
    applyState : function(state) {

        var me = this,
            proxyNodes = state && state.proxyNodes
                ? Ext.util.JSON.decode(state.proxyNodes)
                : {},
            func = function(treeLoader, config, parentNode) {
                if (proxyNodes[config.id]) {
                    config.isProxy = true;
                }
            },
            nodeToSelect = null;

        if (state.width) {
            this.width = state.width;
        }

        if (state.collapsed) {
            this.collapsed = true;
        }

        this.treeLoader.on('beforecreatenode', func);

        me.root.on('expand', function() {

            this.treeLoader.un('beforecreatenode', func);

            var initProxyNodes = function(nodes, proxyNodes) {

                for (var i in nodes) {

                    var node = nodes[i];

                    if (proxyNodes[node.id]) {

                        var cn = proxyNodes[node.id].children;

                        if (node.attributes.childCount) {
                            node.isProxy = true;
                            node.loaded = false;
                        }

                        if (cn) {
                            node.loaded = true;
                            node.suspendEvents();
                            node.beginUpdate();
                            for(var a in cn){
                                me.treeLoader.appendToProxyNode(node, cn[a]);
                            }
                            node.endUpdate();
                            node.expand(false, false);
                            node.resumeEvents();
                            initProxyNodes(node.childNodes, proxyNodes[node.id].children);
                        }
                    }
                }
            };

            var nodes = me.getRootNode().childNodes, tmp = {};
            for (var i = 0, len = nodes.length; i < len; i++) {
                tmp[nodes[i].id] = nodes[i];
            }

            initProxyNodes(tmp, proxyNodes);

            if (state.selectedNodeId) {
                nodeToSelect = me.getNodeById(state.selectedNodeId);
                if (nodeToSelect) {
                    me.getSelectionModel().select(nodeToSelect);
                }
            }

            if (state.scrollTop) {
                this.body.dom.scrollTop = state.scrollTop;
            }

            me.installStateEvents();

        }, this);

    }

});
