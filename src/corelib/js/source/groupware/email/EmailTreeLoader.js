/**
 * conjoon
 * (c) 2002-2010 siteartwork.de/conjoon.org
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

Ext.namespace('com.conjoon.groupware.email');

/**
 * Overrides TreeLoader to read out response.value from the responded JSON
 * and apply custom look & feel to the nodes.
 */

com.conjoon.groupware.email.EmailTreeLoader = function(config){

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


    com.conjoon.groupware.email.EmailTreeLoader.superclass.constructor.call(this);

    this.on('loadexception', this.onLoadException, this);
    this.on('beforeload', this.onBeforeLoad, this);
};


Ext.extend(com.conjoon.groupware.email.EmailTreeLoader, Ext.tree.TreeLoader, {

    loadingNode : null,

    /**
     * We get the children count via the attribute "childs" after the request
     * finishes and the json encoded responseText has been eval'd.
     * Every node thats child count equals to 0 is a folder and not a leaf, but
     * we don't want to paint the "plus" icon that indicates expanding the node
     * (since nothing will happen upon expanding). Thus, we tell the method to
     * render a TreeNode and NOT a AsyncTreeNode, which will make the "plus"
     * icon dissapear, but we can still drop on the node, if we wish.
     *
     * Secondly, we will check the attribute "type" which will either be one of
     * the following:
     *
     *  <ul>
     *   <li>folder</li>
     *   <li>inbox</li>
     *   <li>spam</li>
     *   <li>outbox</li>
     *   <li>sent</li>
     *   <li>trash</li>
     *  </ul>
     *
     * and set the attributes iconCls, allowDrag, isTarget and allowChildren
     * accordingly.
     *
     */
    createNode : function(attr)
    {
        Ext.apply(attr, {
            allowChildren : parseInt(attr.isChildAllowed),
            pendingCount  : parseInt(attr.pendingCount),
            childCount    : parseInt(attr.childCount),
            isLocked      : parseInt(attr.isLocked) ? true : false,
            text          : attr.name,
            isSelectable  : parseInt(attr.isSelectable) ? true : false
        });

        delete attr.name;

        switch (attr.type) {
            case 'root':
            case 'accounts_root':
                attr.iconCls       = 'com-conjoon-groupware-email-EmailTree-rootIcon',
                attr.draggable     = false;
                attr.isTarget      = false;
                attr.allowChildren = false;
                // root folders always have at leas 1 sub folder
                attr.childCount    = 1;
                attr.pendingCount  = 0;
            break;
            case 'folder':
                attr.iconCls = 'com-conjoon-groupware-email-EmailTree-folderIcon';
            break;
            case 'inbox':
                attr.allowDrag = false;
                attr.iconCls   = 'com-conjoon-groupware-email-EmailTree-inboxIcon';
            break;
            case 'spam':
                attr.allowDrag     = false;
                attr.iconCls = 'com-conjoon-groupware-email-EmailTree-spamIcon';
            break;
            case 'outbox':
                attr.allowDrag     = false;
                attr.allowChildren = false;
                attr.isTarget      = false;
                attr.iconCls       = 'com-conjoon-groupware-email-EmailTree-outboxIcon';
            break;
            case 'draft':
                attr.allowDrag     = false;
                attr.allowChildren = false;
                attr.isTarget      = false;
                attr.iconCls       = 'com-conjoon-groupware-email-EmailTree-draftIcon';
            break;
            case 'sent':
                attr.allowDrag     = false;
                attr.allowChildren = false;
                attr.isTarget      = false;
                attr.iconCls       = 'com-conjoon-groupware-email-EmailTree-sentIcon';
            break;
            case 'trash':
                attr.allowDrag     = false;
                attr.iconCls       = 'com-conjoon-groupware-email-EmailTree-trashIcon';
            break;
        }

        //attr.pending = parseInt(attr.pending);

        if(this.baseAttrs){
            Ext.applyIf(attr, this.baseAttrs);
        }
        if(attr.childCount > 0  !== false){
            attr.loader = this;
        }
        if(typeof attr.uiProvider == 'string'){
           attr.uiProvider = this.uiProviders[attr.uiProvider] || eval(attr.uiProvider);
        }


        return(attr.childCount > 0 ? new Ext.tree.AsyncTreeNode(attr) :
                                 new Ext.tree.TreeNode(attr));
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
    processResponse : function(response, node, callback)
    {
        var json = response.responseText;

        try {
            var o = eval("("+json+")");
            /**
             * This line is compliant to the conjoon's json encoded
             *responseText
             */
            o = o.items;
            node.beginUpdate();
            for(var i = 0, len = o.length; i < len; i++){
                var n = this.createNode(o[i]);
                if(n){
                    node.appendChild(n);
                    this.fireEvent('nodeloaded', node, n);
                }
            }
            node.endUpdate();
            if(typeof callback == "function"){
                callback(this, node);
            }
        }catch(e){
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
        if(this.directFn){
            throw(
                "com.conjoon.groupware.email.EmailTreeLoader.getParams() - "
                +"directFn not supported yet"
            );
        }else{
            o = com.conjoon.groupware.email.EmailTreeLoader.superclass.getParams.call(this, node);
            o.id   = o.node;
            delete o.node;
            o.path = node.getPath('idForPath');
            return o;
        }
    },



    onBeforeLoad : function(treeLoader, node, callback)
    {
        this.abort();
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
        com.conjoon.groupware.email.EmailTreeLoader.superclass.abort.call(this);
    },


    onLoadException : function(treeLoader, node, response)
    {
        com.conjoon.groupware.ResponseInspector.handleFailure(response, {
            onLogin: {
                fn : function(){
                    this.load(node);
                },
                scope : this
            }
        });

        if (node.getUI().showProcessing) {
            node.getUI().showProcessing(false);
        }
    }

});