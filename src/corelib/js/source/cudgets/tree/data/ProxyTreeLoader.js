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

com.conjoon.cudgets.tree.data.ProxyTreeLoader = function(config){

    Ext.apply(this, config);

    this.addEvents({
        /**
         * Event gets fired when a node successfully was loaded.
         * The specified listener gets called with the following arguments:
         * @param {Ext.tree.TreeNode} The parent node to which the new node was
         *                            appended after load
         * @param {Ext.tree.TreeNode} The node that was loaded itself.
         *
         */
        'nodeloaded' : true
    });


    com.conjoon.cudgets.tree.data.ProxyTreeLoader.superclass.constructor.call(this);

    this.on('beforeload', this.onBeforeLoad, this);
};


Ext.extend(com.conjoon.cudgets.tree.data.ProxyTreeLoader, Ext.tree.TreeLoader, {

    /**
     * @param {Array} transIds keeps track of multiple trans ids
     */
    transIds : null,

    /**
     * @type {Boolean} true if currently a proxy node is loaded
     * from the server
     */
    proxyLoading : false,

    loadingNode : null,

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
     */
    processResponse : function(response, node, callback, scope)
    {
        var json = response.responseText;

        try {
            var o = response.responseData || (!json.items ? Ext.decode(json) : null);

            if (!o) {
                o = json.items;
                if (!o) {
                    throw('Failure');
                }
            }

            if (!this.proxyLoading) {
                node.beginUpdate();
                for(var i = 0, len = o.length; i < len; i++){
                    var n = this.createNode(o[i]);
                    if(n){
                        node.appendChild(n);
                        this.fireEvent('nodeloaded', node, n);
                    }
                }
                node.endUpdate();
            } else {
                this.proxyLoading = false
                this.runCallback(callback, scope || node, [o]);
                return;
            }

            this.proxyLoading = false
            this.runCallback(callback, scope || node, [node]);

        }catch(e){

            this.proxyLoading = false
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

    onBeforeLoad : function(treeLoader, node, callback)
    {
        this.loadingNode = node;
    },

    abort : function()
    {
        if(this.isLoading()){
            if (this.loadingNode) {
                this.loadingNode.childrenRendered = false;
                this.loadingNode.loaded           = false;
                this.loadingNode.loading          = false;
                if (this.loadingNode.getUI().showProcessing) {
                    this.loadingNode.getUI().showProcessing(false);
                }
            }
            this.loadingNode = null;
        }
        com.conjoon.cudgets.tree.data.ProxyTreeLoader.superclass.abort.call(this);
    },


    recoverFromProxyLoadFailure : function(failedNode) {
        failedNode.parentNode.removeChild(failedNode).destroy();
    },

    loadAndSelectProxyNode : function(node, callback, scope) {

        node.getUI().showProcessing(true);
        this.proxyLoading = node;
        var tmp = this.clearOnLoad;
        this.clearOnLoad = false;


        this.requestData(node, function(items) {

            node.getUI().showProcessing(false);

            if (node.compareProxyChildrenWithLoadedItems(node.childNodes, items) === false) {

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

            callback.call(scope, items);



        }, this);
        this.clearOnLoad = tmp;

    },

    /**
     * overriden to consider multiple ongoing request
     *
     */
    requestData : function(node, callback, scope) {

         if(this.fireEvent("beforeload", this, node, callback) !== false){

            if(this.directFn){
                var args = this.getParams(node);
                args.push(this.processDirectResponse.createDelegate(
                    this, [{callback: callback, node: node, scope: scope}], true)
                );
                this.directFn.apply(window, args);
            }else{
                if (this.transIds === null) {
                    this.transIds = {};
                }

                 this.transId = Ext.Ajax.request({
                    method:this.requestMethod,
                    url: this.dataUrl||this.url,
                    success: this.handleResponse,
                    failure: this.handleFailure,
                    scope: this,
                    argument: {callback: callback, node: node, scope: scope},
                    params: this.getParams(node)
                });

                this.transIds[this.transId.tId] = this.transId.tId;

            }
        }else{
            // if the load is cancelled, make sure we notify
            // the node that we are done
            this.runCallback(callback, scope || node, []);
        }
    },

    isLoading : function(){
        if (this.transIds === null) {
            return false;
        }
        for (var i in this.transIds) {
            return true;
        }

        return false;
    },

    abort : function(){
        if(this.isLoading() && this.transIds !== null){
            this.proxyLoading = false;
            for (var i in this.transIds) {
                Ext.Ajax.abort(this.transIds[i]);
            }
        }
    },

    handleResponse : function(response){

        if (this.transIds !== null) {
            delete this.transIds[response.tId];
        }

        com.conjoon.cudgets.tree.data.ProxyTreeLoader.superclass.handleResponse.call(this, response);
    },

    handleFailure : function(response){

        if (this.proxyLoading) {
            this.recoverFromProxyLoadFailure(this.proxyLoading);
        }

        this.proxyLoading = false;
        if (this.transIds !== null) {
            delete this.transIds[response.tId];
        }

        com.conjoon.cudgets.tree.data.ProxyTreeLoader.superclass.handleFailure.call(this, response);
    },

    /**
     *
     * @return {Boolean}
     */
    isProxyLoading : function() {
        return this.proxyLoading !== false;
    }

});
