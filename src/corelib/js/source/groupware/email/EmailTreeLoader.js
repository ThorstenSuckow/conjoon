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
Ext.namespace('com.conjoon.groupware.email');

/**
 * Tree loader for email folder.
 */
com.conjoon.groupware.email.EmailTreeLoader = Ext.extend(com.conjoon.cudgets.tree.data.ProxyTreeLoader, {

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
            allowChildren : parseInt(attr.isChildAllowed, 10),
            pendingCount  : parseInt(attr.pendingCount, 10),
            childCount    : parseInt(attr.childCount, 10),
            isLocked      : parseInt(attr.isLocked, 10) ? true : false,
            text          : attr.name,
            isSelectable  : parseInt(attr.isSelectable, 10) ? true : false
        });

        delete attr.name;

        switch (attr.type) {
            case 'root':
            case 'root_remote':
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
            case 'junk':
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

        return com.conjoon.groupware.email.EmailTreeLoader.superclass.createNode.call(this, attr);
    },

    /**
     * Exception handler for proper fallback if loading threw an exception.
     *
     * @param treeLoader
     * @param node
     * @param response
     */
    onLoadException : function(treeLoader, node, response)
    {
        com.conjoon.groupware.email.EmailTreeLoader.superclass.onLoadException.apply(this, arguments);

        com.conjoon.groupware.ResponseInspector.handleFailure(response, {
            onLogin: {
                fn : function(){
                    this.load(node);
                },
                scope : this
            }
        });

        if (node && node.getUI() && node.getUI().showProcessing) {
            node.getUI().showProcessing(false);
        }
    },

    /**
     * Overriden to adjust UI of node when aborting all ongoing requests.
     *
     *
     */
    abort : function() {

        if (this.loadingNodes) {
            for (var i in this.loadingNodes) {
                this.loadingNodes[i].childrenRendered = false;
                this.loadingNodes[i].loaded           = false;
                this.loadingNodes[i].loading          = false;
                if (this.loadingNodes[i].getUI().showProcessing) {
                    this.loadingNodes[i].getUI().showProcessing(false);
                }
            }
        }

        com.conjoon.groupware.email.EmailTreeLoader.superclass.abort.apply(this, arguments);
    }

});
