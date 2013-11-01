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
 * @class {conjoon.mail.ui.folderPanel.StatefulFolderPanel}
 */
Ext.defineClass('conjoon.mail.ui.folderPanel.StatefulFolderPanel', {

    extend : 'com.conjoon.groupware.email.EmailTree',

    /**
     * @type {conjoon.mail.ui.folderPanel.listener.ProxyTreeLoaderListener}
     */
    proxyTreeLoaderListener : null,

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


        /**
         * The loader responsible for loading nodes into the tree.
         * Events will be captured by the onNodeLoaded method.
         */
        this.treeLoader = new com.conjoon.groupware.email.EmailTreeLoader({
            directFn : com.conjoon.groupware.provider.emailFolder.getFolder,
            baseAttrs : {
                uiProvider : com.conjoon.groupware.email.PendingNodeUI
            }
        });


        conjoon.mail.ui.folderPanel.StatefulFolderPanel.superclass.initComponent.call(this);
    },

    /**
     * @inheritdoc
     */
    initEvents : function() {

        var me = this;

        conjoon.mail.ui.folderPanel.StatefulFolderPanel.superclass.initEvents.call(me);

        me.installListeners();
    },

// -------- API

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
     * @return {conjoon.mail.ui.folderPanel.listener.ProxyTreeLoaderListener}
     */
    getProxyTreeLoaderListener : function() {

        var me = this;

        if (me.proxyTreeLoaderListener) {
            return me.proxyTreeLoaderListener;
        }

        me.proxyTreeLoaderListener = Ext.createInstance(
            'conjoon.mail.ui.folderPanel.listener.ProxyTreeLoaderListener', {
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
            paths = {}, node;

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

                    var childNodes = node.childNodes, children = {}, cn;
                    for (var u = 0, lenu = childNodes.length; u < lenu; u++) {

                        cn = extractAttributes(childNodes[u]);
                        if (childNodes[u].isExpanded()) {
                            cn.children = getPaths(childNodes[u].childNodes, true);
                            cn.isProxy = true;
                        }
                        children[cn.id] = cn;
                    }

                    map[node.id] = extractAttributes(node);
                    map[node.id].isProxy = lenu > 0;
                    map[node.id].children = children;
                }
            }

            return map;
        };

        state = {
            proxyNodes : Ext.util.JSON.encode(getPaths(nodes))
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
            };

        this.treeLoader.on('beforecreatenode', func);

        me.root.on('expand', function() {

            this.treeLoader.un('beforecreatenode', func);

            var initProxyNodes = function(nodes, proxyNodes) {

                for (var i in nodes) {

                    var node = nodes[i];

                    if (proxyNodes[node.id] && proxyNodes[node.id].children) {

                        var cn = proxyNodes[node.id].children;

                        node.beginUpdate();
                        for(var a in cn){
                            var newNode = node.appendChild(me.treeLoader.createNode(cn[a]));
                        }

                        node.endUpdate();

                        if (node.attributes.childCount) {
                            node.isProxy = true;
                        }
                        node.loaded = true;
                        node.suspendEvents();
                        node.expand(false, false);
                        node.resumeEvents();

                        initProxyNodes(node.childNodes, proxyNodes[node.id].children);

                    }
                }
            };

            var nodes = me.getRootNode().childNodes, tmp = {};
            for (var i = 0, len = nodes.length; i < len; i++) {
                tmp[nodes[i].id] = nodes[i];
            }

            initProxyNodes(tmp, proxyNodes);

        }, this);

    }

});
