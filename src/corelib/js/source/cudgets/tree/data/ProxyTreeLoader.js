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

if (Ext.version != '3.4.0') {
    throw(
        "Ext.version " + Ext.version +
        " detected, please check ProxyTreeLoader.js (transId -> transIds, multiple requests)"
    );
}

Ext.namespace('com.conjoon.cudgets.tree.data');

/**
 * Overrides TreeLoader to read out response.value from the responded JSON
 * and apply custom look & feel to the nodes.
 */

com.conjoon.cudgets.tree.data.ProxyTreeLoader = function(config) {

    Ext.apply(this, config);

    this.addEvents({
        /**
         * Event gets fired when a node successfully was loaded.
         * The specified listener gets called with the following arguments:
         * @param {Ext.tree.TreeNode} The parent node to which the new node was
         *                            appended after load
         * @param {Ext.tree.TreeNode} The node that was loaded itself.
         */
        'nodeloaded' : true,

        /**
         * Event gets fired before a proxy node was loaded.
         * Return false to cancel loading the node
         *
         *
         * @param {com.conjoon.cudgets.tree.data.ProxyTreeLoader} this
         * @param {com.conjoon.cudgets.tree.data.ProxyTreeNode} The node that should
         * get loaded.
         */
        'beforeproxynodeload' : true,

        /**
         * Event gets fired once a proxy node was loaded
         *
         * @param {com.conjoon.cudgets.tree.data.ProxyTreeLoader} this
         * @param {com.conjoon.cudgets.tree.data.ProxyTreeNode} The node that was
         * loaded
         */
        'proxynodeload' : true,

        /**
         * Event gets fired if loading a proxy node failed
         *
         * @param {com.conjoon.cudgets.tree.data.ProxyTreeLoader} this
         * @param {com.conjoon.cudgets.tree.data.ProxyTreeNode} The node that was
         * not properly loaded
         * @param {Object} response The response object
         */
        'proxynodeloadfailure' : true,

        /**
         * This event gets fired before a complete subtree was synced
         * against the backend.
         * Return false to cancel syncing a subtree.
         * The passed arguments to the listeners are:
         *
         * @param {com.conjoon.cudgets.tree.data.ProxyTreeLoader} this
         * @param {com.conjoon.cudgets.tree.data.ProxyTreeNode} The node that
         * triggered syncing the subtree with the backend
         */
        'beforesubtreesync' : true,

        /**
         * This event gets fired once a complete subtree was synced
         * against the backend.
         * The passed arguments to the listeners are:
         *
         * @param {com.conjoon.cudgets.tree.data.ProxyTreeLoader} this
         * @param {com.conjoon.cudgets.tree.data.ProxyTreeNode} The node that
         * triggered syncing the subtree with the backend
         * @param {Boolean} tree changed set to true if any nodes of the tree
         * were not in sync with the backend
         */
        'subtreesync' : true,

        /**
         * Event gets fired before a node gets created.
         *
         * @param {com.conjoon.cudgets.tree.data.ProxyTreeLoader} this
         * @param {Object} object properties of the node which becomes created
         * @param {Ext.tree.TreeNode} The parent node of the node that is about
         * to get created
         */
        'beforecreatenode' : true
    });


    com.conjoon.cudgets.tree.data.ProxyTreeLoader.superclass.constructor.call(this);

    this.on('beforeload',    this.onBeforeLoad,    this);
    this.on('load',          this.onLoad,          this);
    this.on('loadexception', this.onLoadException, this);
};


Ext.extend(com.conjoon.cudgets.tree.data.ProxyTreeLoader, Ext.tree.TreeLoader, {

    /**
     * @param {Object} transIds keeps track of multiple trans ids
     */
    transIds : null,

    /**
     * @type {Object} keeps track of loading proxynodes
     */
    proxyLoading : false,

    /**
     * Keeps track of all nodes which children are currently
     * being loaded.
     * @type {Object}
     */
    loadingNodes : null,

    /**
     *  @inheritdoc
     */
    createNode : function(attr)
    {
        return (
            attr.isProxy
                ? new com.conjoon.cudgets.tree.data.ProxyTreeNode(attr)
                 : (attr.childCount > 0
                   ? new com.conjoon.cudgets.tree.AsyncTreeNode(attr)
                   : new com.conjoon.cudgets.tree.TreeNode(attr)));
    },

    /**
     * We need to take care of the format of the returned json encoded responseText
     * and look for the response.value attribute therin, which will contain
     * all treenodes to render based on the request.
     *
     * While appending nodes, the events of the tree will be suspended. This
     * is because the onbefore event checks if a folder is being appended to the
     * trash. If any node gets read out of the db, the loading of the nodes would
     * trigger the beforeevent and prompt the user if he really want to append
     * the node to a trash-bins node.
     *
     * @param response
     * @param node
     * @param callback
     * @param scope
     * @param proxyConfig optional configuration if the node was loaded from a proxy
     */
    processResponse : function(response, node, callback, scope, proxyConfig)
    {
        var json = response.responseText;

        try {
            var o = response.responseData ||
                (!json.items ? Ext.decode(json) : null);

            if (!o) {
                o = json.items;
                if (!o) {
                    throw('Failure');
                }
            }

            if (!proxyConfig) {
                // regular node where we assume the node's children should be
                // appended
                node.beginUpdate();
                for(var i = 0, len = o.length; i < len; i++){
                    this.fireEvent('beforecreatenode', this, o[i], node);
                    var n = this.createNode(o[i]);
                    if(n){
                        node.appendChild(n);
                        this.fireEvent('nodeloaded', node, n);
                    }
                }
                node.endUpdate();
            } else {

                // call the listener with the proxyConfig
                this.runCallback(callback, scope || node, [o], proxyConfig);

                return;
            }

            this.runCallback(callback, scope || node, [node], proxyConfig);

        } catch(e) {

            this.handleFailure(response);

        }
    },

    /**
     * Overrides parent implementation by adding the path to the
     * params list.
     *
     */
    getParams: function(node)
    {
        var o = com.conjoon.cudgets.tree.data.ProxyTreeLoader.superclass.getParams.call(this, node);

        if(this.directFn){

            return [{
                id   : o[0],
                path : node.getPathAsJson('idForPath')
            }];

        }else{
            o.id = o.node;
            delete o.node;
            o.path = node.getPathAsJson('idForPath');
            return o;
        }
    },

    /**
     * The callback as specified by the node which should be loaded.
     *
     * @param node
     * @param nodeCallback
     * @param scope
     */
    loadProxyNode : function(node, nodeCallback, scope) {

        var ProxyTreeNode = com.conjoon.cudgets.tree.data.ProxyTreeNode,
            requestId,
            proxyConfig = {};


        if (this.fireEvent('beforeproxynodeload', this, node) === false) {
            return;
        }

       /* if (this.fireEvent('beforesubtreesync', this, node) === false) {
            return;
        };*/

        requestId = this.requestData(node, function(items) {

            var parentNode = node.parentNode;


            nodeCallback.call(scope, items);

            this.fireEvent('proxynodeload', this, node);

            var childNodes = node.childNodes;
                obj = {},
                validState = true;

            for (var i = 0, len = childNodes.length; i < len; i ++) {

                obj = items[i]
                      ? Ext.apply({text : items[i].name}, items[i])
                      : {};

                if (!items[i] || !childNodes[i].equalsTo(obj)) {
                    validState = false;
                    break;
                }
            }

            if (!validState) {
                node.collapse(false, false);
                while(node.firstChild){
                    node.removeChild(node.firstChild).destroy();
                }
                node.beginUpdate();
                for(var i = 0, len = items.length; i < len; i++){
                    var n = this.createNode(items[i]);
                    if(n){
                        node.appendChild(n);
                        this.fireEvent('nodeloaded', node, n);
                    }
                }
                node.endUpdate();
                node.expand(false, false);
            }

            /*if ((node.parentNode instanceof ProxyTreeNode) && node.parentNode.isProxyNode()) {

                node.parentNode.loadProxyNode();

            } else {

                this.fireEvent('subtreesync', this, this.treeSyncTriggerNode, this.subtreeDirty);

            }*/

        }, this, proxyConfig);

        if (!this.proxyLoading) {
            this.proxyLoading = {};
        }

        this.proxyLoading[requestId] = node;


    },

    /**
     * Overriden to consider multiple ongoing request and allow for passing a config
     * object which gets passed to the callback.
     * If proxyConfig is passed, the requeted data will be treated as if the
     * request comes from a proxy node.
     *
     * @param node
     * @param callback
     * @param scope
     * @param proxyConfig
     *
     * @return returns a unique id which makes it easy to identifies the request
     * later on. The id is found in the response.argument.requestId property.
     */
    requestData : function(node, callback, scope, proxyConfig) {

        if (this.fireEvent("beforeload", this, node, callback) !== false) {

            var args = this.getParams(node),
                requestId = Ext.id();
                argumentConfig = {
                    callback : callback,
                    node : node,
                    scope : scope,
                    requestId : requestId,
                    proxyConfig : proxyConfig
                };

            if (this.transIds === null) {
                this.transIds = {};
            }

            if (this.directFn) {

                args.push(this.processDirectResponse.createDelegate(
                    this, [argumentConfig], true)
                );

                this.transIds[requestId] = null;

                this.directFn.apply(window, args);

            } else {

                this.transId = Ext.Ajax.request({
                    method:this.requestMethod,
                    url: this.dataUrl||this.url,
                    success: this.handleResponse,
                    failure: this.handleFailure,
                    scope: this,
                    argument: argumentConfig,
                    params: this.getParams(node)
                });

                this.transIds[requestId] = this.transId.tId;

            }

            return requestId;

        } else {
            // if the load is cancelled, make sure we notify
            // the node that we are done
            this.runCallback(callback, scope || node, [], proxyConfig);
        }

        return null;
    },


    /**
     * Returns true if the loader is currently busy with loading any node.
     *
     * @return {Boolean}
     */
    isLoading : function(){
        if (this.transIds === null) {
            return false;
        }
        for (var i in this.transIds) {
            return true;
        }

        return false;
    },

    /**
     * Overridden to cancel _all_ currently ongoing requests
     */
    abort : function() {

        if(this.transIds){
            for (var i in this.transIds) {
                if (this.transIds[i] === null) {
                    // directFn
                    continue;
                }
                Ext.Ajax.abort(this.transIds[i]);
            }
        }

        this.transIds     = null;
        this.proxyLoading = null;
        this.loadingNodes = null;

    },


    /**
     * Handles the response of loading a node.
     *
     * @param response
     */
    handleResponse : function(response){

        this.clearRequestIdsForResponse(response);

        var a = response.argument;
        this.processResponse(response, a.node, a.callback, a.scope, a.proxyConfig);
        this.fireEvent("load", this, a.node, response);
    },

    /**
     * Default failure handler. If the failed node is a proxy node,
     * the proxynodeloadfailure event will be triggered
     *
     * @param response
     */
    handleFailure : function(response){

        var proxyNode = this.getProxyNodeForResponse(response);

        if (proxyNode) {
            this.fireEvent('proxynodeloadfailure', this, proxyNode, response);
        }

        this.clearRequestIdsForResponse(response);

        com.conjoon.cudgets.tree.data.ProxyTreeLoader.superclass.handleFailure.call(this, response);
    },

    /**
     * Checks whether any request is still busy to load a proxy node.
     * Returns true if there is currently any proxy loading, otherwise false.
     *
     * @return {Boolean}
     */
    isProxyLoading : function() {

        if (this.proxyLoading) {
            for (var i in this.proxyLoading) {
                return true;
            }
        }

        return false;

    },

// -------- request helper

    /**
     * Returns the proxy node which might have triggered a request and which response
     * is now available.
     *
     * @param response
     * @return {*}
     */
    getProxyNodeForResponse : function(response) {

        var node = null,
            requestId = this.getRequestIdForResponse(response);

        if (requestId && this.proxyLoading && this.proxyLoading[requestId]) {
            node = this.proxyLoading[requestId];
        }

        return node;
    },

    /**
     * Returns the request id for the specified response
     *
     * @param response
     */
    getRequestIdForResponse : function(response) {

        if (response && response.argument && response.argument.requestId) {
            return response.argument.requestId;
        }

        return null;
    },

    /**
     * Clears all existing request id caches
     *
     */
    clearRequestIdsForResponse : function(response) {

        var requestId = this.getRequestIdForResponse(response);

        if (requestId) {

            if (this.transIds) {
                delete this.transIds[requestId];
            }

            if (this.proxyLoading) {
                delete this.proxyLoading[requestId];
            }

        }

    },

// -------- listeners

    onBeforeLoad : function(treeLoader, node, callback)
    {
        if (!this.loadingNodes) {
            this.loadingNodes = {};
        }

        this.loadingNodes[node.id] = node;
    },

    onLoad : function(treeLoader, node, response)
    {
        if (this.loadingNodes && this.loadingNodes[node.id]) {
            delete this.loadingNodes[node.id];
        }
    },

    onLoadException : function(treeLoader, node, response)
    {
        if (this.loadingNodes && this.loadingNodes[response.tId]) {
            delete this.loadingNodes[node.id];
        }
    }

});
