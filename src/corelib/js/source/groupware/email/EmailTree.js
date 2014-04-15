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
* Controller for the emailpanels tree, preview and grid.
*
*/
com.conjoon.groupware.email.EmailTree = Ext.extend(Ext.tree.TreePanel, {

    /**
     * @type {conjoon.mail.folder.data.FolderService}
     */
    folderService : null,

    /**
     * Shorthands for the none-editable folders. They get assigned in the
     * <tt>onNodeLoaded</tt>-method.
     */
    folderInbox  : null,
    folderOutbox : null,
    folderSent   : null,
    folderSpam   : null,
    folderTrash  : null,

    /**
     *
     *
     * @param {Array} path
     *
     * @return {String}
     *
     * @throws {cudget.base.InvalidArgumentException}
     */
    assemblePath : function(path)
    {
        if ((Object.prototype.toString.call(path)).toLowerCase() !== '[object array]') {
            throw new cudgets.base.InvalidArgumentException("path is not of type Array");
        }

        var res = "";

        if (path[0] == 'root') {
            res = '/' + path.join('/');
        } else {
            res = '/root/' + path.join('/');
        }

        return res;
    },

    /**
     *
     * @param {Array|String}
     * @return {*}
     *
     * @throws {cudget.base.InvalidArgumentException}
     */
    getNodeForPath :function(path)
    {
        var me = this;

        if ((Object.prototype.toString.call(path)).toLowerCase() === '[object array]') {
            path = me.assemblePath(path);
        } else if ((typeof path).toLowerCase() !== 'string') {
            throw new cudgets.base.InvalidArgumentException("path must be an array or a string");
        }

        var node = this.root.findChildBy(function(node) {
            if (node.getPath('idForPath') == path) {
                return true;
            }
        }, this, true);

        return node;
    },

    findPathFor : function(id, type)
    {
        type = type.toLowerCase();

        var _store = com.conjoon.groupware.email.AccountStore.getInstance();

        var accounts = _store.getRange();

        for (var i = 0, len = accounts.length; i < len; i++) {
            var account = accounts[i];
            if (account.get('protocol') == 'IMAP') {
                var mappings = account.get('folderMappings');

                for (var a = 0, lena = mappings.length; a < lena; a++) {
                    var mapping = mappings[a];
                    if (mapping.path[1] == id && type == mapping.type.toLowerCase()) {
                        return mapping.path;
                    }
                }

            }
        }

        switch (true) {
            case (this.folderInbox && this.folderInbox.getPathAsArray('idForPath')[1] == id && type == 'inbox'):
                return this.folderInbox.getPathAsArray('idForPath');
            case (this.folderOutbox && this.folderOutbox.getPathAsArray('idForPath')[1] == id && type == 'outbox'):
                return this.folderOutbox.getPathAsArray('idForPath');
            case (this.folderSent && this.folderSent.getPathAsArray('idForPath')[1] == id && type == 'sent'):
                return this.folderSent.getPathAsArray('idForPath');
            case (this.folderSpam && this.folderSpam.getPathAsArray('idForPath')[1] == id && type == 'junk'):
                return this.folderSpam.getPathAsArray('idForPath');
            case (this.folderTrash && this.folderTrash.getPathAsArray('idForPath')[1] == id && type == 'trash'):
                return this.folderTrash.getPathAsArray('idForPath');
            case (this.folderDraft && this.folderDraft.getPathAsArray('idForPath')[1] == id && type == 'draft'):
                return this.folderDraft.getPathAsArray('idForPath');
        }

        return null;
    },

    isPathOfType : function(path, type)
    {
        type = type.toLowerCase();

        var _store = com.conjoon.groupware.email.AccountStore.getInstance();
        var _folderMappingCache = [];

        var myPath = path[0] == 'root'
                     ? '/' + path.join('/')
                     : '/root/' + path.join('/');

        var accounts = _store.getRange();

        for (var i = 0, len = accounts.length; i < len; i++) {
            var account = accounts[i];
            if (account.get('protocol') == 'IMAP') {
                var mappings = account.get('folderMappings');

                for (var a = 0, lena = mappings.length; a < lena; a++) {
                    var mapping = mappings[a];

                    _folderMappingCache['/' + (mapping.path).join('/')]
                        = (mapping.type).toLowerCase();
                }

            }
        }

        switch (true) {
            case (this.folderInbox && this.folderInbox.getPath('idForPath') == myPath && type == 'inbox'):
                return true;
            case (this.folderOutbox && this.folderOutbox.getPath('idForPath') == myPath && type == 'outbox'):
                return true;
            case (this.folderSent && this.folderSent.getPath('idForPath') == myPath && type == 'sent'):
                return true;
            case (this.folderSpam && this.folderSpam.getPath('idForPath') == myPath && type == 'junk'):
                return true;
            case (this.folderTrash && this.folderTrash.getPath('idForPath') == myPath && type == 'trash'):
                return true;
            case (this.folderDraft && this.folderDraft.getPath('idForPath') == myPath && type == 'draft'):
                return true;
        }

        return _folderMappingCache[myPath] == type;
    },


    /**
     * Helper function for checking if the passed node id points to the same
     * path as specified in the path parts.
     *
     * @param {String} nodeId
     * @param {Array} path
     *
     * return {Boolean}
     */
    idEqualsPath : function(nodeId, path)
    {
        var node = this.getNodeById(nodeId);

        if (!node) {
            return false;
        }

        var sourcePath = node.getPath('idForPath');

        if (typeof path == 'string' || path instanceof String) {
            path = path.split('/');
        }

        if (path) {

            var targetPath = path.join('/');

            if (targetPath.indexOf('/root') !== 0) {
                targetPath = '/root/' + targetPath;
            }

            return sourcePath == targetPath;
        }

        return false;
    },

    /**
     * Helper function for checking if the passed path parts point to the same
     * path as specified in the second path parts.
     *
     * @param {Array} oldFolderPath
     * @param {Array} newFolderPath
     *
     * return {Boolean}
     */
    pathEqualsPath : function(oldFolderPath, newFolderPath)
    {
        if (!oldFolderPath || !newFolderPath) {
            return false;
        }

        return oldFolderPath.join() == newFolderPath.join();
    },

    isFolderOfType : function(nodeId, type)
    {

        type = type.toLowerCase();

        var _store = com.conjoon.groupware.email.AccountStore.getInstance();
        var _folderMappingCache = [];

        var accounts = _store.getRange();

        for (var i = 0, len = accounts.length; i < len; i++) {
            var account = accounts[i];
            if (account.get('protocol') == 'IMAP') {
                var mappings = account.get('folderMappings');

                for (var a = 0, lena = mappings.length; a < lena; a++) {
                    var mapping = mappings[a];

                    _folderMappingCache['/' + (mapping.path).join('/')]
                        = (mapping.type).toLowerCase();
                }

            }
        }

        switch (true) {
            case (this.folderInbox && this.folderInbox.id == nodeId && type == 'inbox'):
                return true;
            case (this.folderOutbox && this.folderOutbox.id == nodeId && type == 'outbox'):
                return true;
            case (this.folderSent && this.folderSent.id == nodeId && type == 'sent'):
                return true;
            case (this.folderSpam && this.folderSpam.id == nodeId && type == 'junk'):
                return true;
            case (this.folderTrash && this.folderTrash.id == nodeId && type == 'trash'):
                return true;
            case (this.folderDraft && this.folderDraft.id == nodeId && type == 'draft'):
                return true;
        }

        var n = this.getNodeById(nodeId);

        if (!n) {
            return;
        }

        return _folderMappingCache[n.getPath('idForPath')] == type;

    },

    /**
     * @type {Object}
     */
    taskQueue : null,

    /**
     * A simple storage for nodes which are being edited. This is needed for
     * newly created nodes, since a new node will be written in to the database
     * _after_ the editing process has finished. Multiple requests may pend
     * and wait for completion. This storage serves for later identifying the
     * saved nodes and alter attributes as needed upon a successfull/ failed
     * XMLHttpRequest.
     *
     */
    editingNodesStorage : null,

    /**
     * The last selected node in this tree.
     */
    clkNode : null,

    /**
     * The last node that failed to load.
     */
    lastFailedNode : null,

    /**
     * A {Ext.util.TaskRunner} that will check if the editor is busy if any
     * changes in the tree have to be made that may result in erroneous rendering
     * of the editor.
     * @see {onRequestFailure}
     */
    taskRunner : null,

    /**
     * @type {HTMLElement} infoText An html element holding information about current
     * loading state and loading errors if, and only if the root node is either being loaded,
     * or loading the root node failed due to 0 child nodes or a server error. Once
     * The root node has been loaded and it has child nodes, this element will be destroyed
     * since it's not needed anymore.
     */
    infoText : null,

    /**
     * The default value for an editing text field, if a new node is created.
     */
    anonymousNodeText : "New folder",

    /**
     * The context menu for this tree. Will be lazyly instantiated
     * in createContextMenu
     */
    contextMenu : null,

    /**
     * The root node for the email tree.
     * @param {Ext.tree.TreeNode}
     */
    root : null,

    /**
     * The store for keeping track of read/ unread messages.
     */
    pendingItemStore : null,

    initComponent : function()
    {
        var me = this;


        me.addEvents(
            /**
             * @event beforedragstart
             * Fires before a node in this tree is being dragged. Return false to cancel
             * dragging the node.
             * @param {Tree} tree The owner tree
             * @param {Object} data The drag data
             * @param {Event} The Event object
             */
            'beforedragstart'
        );

        this.contextMenu = this.getFolderMenu();

        this.taskQueue = {};

        me.folderService = me.getFolderService();

        this.root = new com.conjoon.cudgets.tree.AsyncTreeNode({
            id            : 'root',
            idForPath     : 'root',
            iconCls       : 'com-conjoon-groupware-email-EmailTree-rootIcon',
            draggable     : false,
            isTarget      : false,
            allowChildren : false,
            expanded      : true,
            type          : 'root'
        });

        this.pendingItemStore = new Ext.data.SimpleStore({
            reader      : new Ext.data.ArrayReader(
                              com.conjoon.groupware.email.PendingNodeItemRecord),
            fields : [{name : 'pending', type : 'int'}]
        });

        var store = com.conjoon.groupware.email.AccountStore.getInstance();

        Ext.ux.util.MessageBus.subscribe(
            'com.conjoon.groupware.email.account.added',
            this._onAccountAdded,
            this
        );

        /**
         * The top toolbar for the tree panel
         * @param {Ext.Toolbar}
         */
        this.tbar = [new Ext.Toolbar.Button({
            iconCls : 'com-conjoon-groupware-email-EmailTree-toolbar-expandButton-icon',
            tooltip : com.conjoon.Gettext.gettext("Show all folders"),
            handler : this._onExpandAllClick,
            scope   : this
          }),'-', new Ext.Toolbar.Button({
            iconCls : 'com-conjoon-groupware-email-EmailTree-toolbar-collapseButton-icon',
            tooltip : com.conjoon.Gettext.gettext("Hide all folders"),
            handler : function(){ this.root.collapse(true); },
            scope   : this
        })];

        Ext.apply(this, {
            bodyStyle       : 'background-color:#FFFFFF',
            rootVisible     : false,
            autoScroll      : true,
            cls             : 'com-conjoon-groupware-email-EmailTree',
            minSize         : 175,
            maxSize         : 500,
            lines           : false,
            useArrows       : true,
            ddGroup         : 'com.conjoon.groupware-email-Email',
            enableDD        : true,
            containerScroll : true,
            ddAppendOnly    : true,
            loader          : this.treeLoader,
            animate         : false,
            header          : false
        });

        // this.on('nodedragover', function(overEvent){return overEvent.point == 'append';});

        this.nodeEditor = new com.conjoon.groupware.email.NodeEditor(this);

        this.on('render', this.onTreeRender, this, {single : true});

        com.conjoon.groupware.email.EmailTree.superclass.initComponent.call(this);
    },

    /**
     * Retuns the context menu used for this folder panel's folders.
     */
    getFolderMenu : function() {
        throw("getFolderMenu is abstract and needs to be overridden");
    },

    initEvents : function()
    {
        var me = this;

        com.conjoon.groupware.email.EmailTree.superclass.initEvents.call(me);

        // register the listeners
        me.mon(me.treeLoader, 'nodeloaded', me.onNodeLoaded, me);
        me.mon(me.treeLoader, 'loadexception', me._onTreeLoaderException, me);
        me.mon(me.treeLoader, 'beforeload', me._onTreeLoaderBeforeLoad, me);
        me.mon(me.treeLoader, 'load', me._onTreeLoaderLoad, me);



        Ext.ux.util.MessageBus.subscribe(
            'com.conjoon.groupware.email.account.added',
            me._onAccountAdded,
            me
        );

        me.mon(me.getFolderMenu(), 'itemclick', me.contextMenuItemClicked, me);
        me.on('contextmenu', me.onContextMenu, me);
        me.on('mousedown', me.onMouseDown, me);
        me.on('click', me.onPanelClick, me);

        me.on('beforecollapsenode', me.onBeforeCollapseNode, me);
        me.on('beforeexpandnode', me.onBeforeExpandNode, me);

        me.on('beforemovenode', me.onBeforeFolderMove, me);
        me.on('beforeappend', me.onBeforeFolderAppend, me);
        me.on('movenode', me.onFolderMove, me);

        me.on('nodedragover', me.onNodeDragOver, me);
        me.on('beforenodedrop', me.onBeforeNodeDrop, me);
        me.on('beforedragstart', me.onBeforeDragStart, me);

        // override dragZone BUT! only once we have called the original
        // Tree.initEvents()
        if (!me.dragZone) {
            throw new cudgets.base.RuntimeException("No dragZone available");
        }
        me.dragZone.onBeforeDrag = function(data, e) {
            return me.fireEvent('beforedragstart', me, data, e);
        };

        me.on('destroy', function(){this.pendingItemStore.destroy();}, me);

        me.mon(me.getSelectionModel(), 'beforeselect', me.onBeforeNodeSelect, me);

        me.mon(me.pendingItemStore, 'update', me.updatePendingNodes, me);
    },


//------------------------- Node related methods -------------------------------

    /**
     * Starts editing the node that is currently selected.
     *
     * This method will do nothing if the currently selected node is part
     * of a proxy subtree.
     */
    startNodeEdit : function(mode)
    {
        var me = this;

        if (me.folderService.isPartOfProxySubtree(me.clkNode)) {
            return;
        }

        this.nodeEditor.triggerEdit(this.clkNode, mode);
        this.nodeEditor.field.selectText();
    },

    /**
     * Returns the folder service used with this panel.
     *
     * @return {conjoon.mail.folder.data.FolderService}
     */
    getFolderService : function() {
        var me = this;

        if (!me.folderService) {

            me.folderService = Ext.createInstance(
                'conjoon.mail.folder.data.FolderService', {
                folderPanel : me
            });

        }

        return me.folderService;
    },


    /**
     * Moves the selected folder into the trash bin as a direct child of it.
     * If the folder is already a child of the trashbin, the node gets removed
     * and a XMLHttpRequest initiated for taking serverside-actions.
     * If the node gets permanently deleted from the tree, we won't save the state of it
     * for a rollback if anything fails. Since the node is in the trashbin, we will
     * handle this operation as a none-critical one and depend on a later restore when
     * the tree get's reloaded.
     *
     * This method will do nothing if the selected node is part of a proxy subtree.
     *
     * @param {Object} The folder to delete. If not specified, the clkNode property will
     * will be used.
     *
     * @throws {conjoon.mail.folder.base.FolderNotFoundException} if any requested folder
     * to operate on was not found
     *
     * @abstract
     */
    deleteFolder : function(folder) {
        throw("deleteFolder() in EmailTree is abstract");
    },

    /**
     * Since the tree consist of AsyncTreeNodes, we can not immediately append
     * a new node if a AsyncTreeNode has not yet been loaded. This is why we
     * add the expand listener and let editing start as soon as the nodes contents
     * have been fully loaded.
     * Note that in very rare cases this could lead to misleading the user, for
     * example if loading the nodes children takes too long and the user starts
     * to edit another, none asynchronous node, where the editor gets shown as
     * soon as the node was appended. If he is in editing process, the editor may
     * switch to the other node. In this rare case, appending a new node will be
     * stopped when the node expands, leaving the editor at the currently being
     * edited node.
     *
     * This emthod will do nothign is the selected node is part of a proxy subtree.
     *
     */
    prepareAppend : function()
    {
        var me = this,
            node = this.clkNode;

        if (me.folderService.isPartOfProxySubtree(node)) {
            return;
        }

        if (this.clkNode.isExpanded()) {
            this.appendAnonymouseNode(this.clkNode);
            return;
        }
        this.clkNode.on('expand', this.appendAnonymouseNode, this);
        this.clkNode.expand();
    },

    /**
     * Gets called while in editing mode and the editor tells us that the new
     * value for the node was invalid.
     *
     * @param {Ext.tree.TreeEditor}
     * @param mixed value
     * @param mixed startValue
     */
    valueInvalid : function(editor, value, startValue)
    {
        var msg = Ext.MessageBox;

        /**
         * @ext-bug beta1 Modal meesage box does not overlay the editor field
         */
        this.nodeEditor.el.dom.style.zIndex = 100;
        this.nodeEditor.el.prev().dom.style.zIndex = 99;

        msg.show({
            title    : com.conjoon.Gettext.gettext("Invalid folder name"),
            msg      : com.conjoon.Gettext.gettext("The folder name does already exist or is invalid. Please chose another folder name"),
            buttons  : msg.OK,
            fn       : function(){
                           this.nodeEditor.resetEdit(value, startValue);
            },
            scope    : this,
            icon     : msg.WARNING,
            cls      :'com-conjoon-msgbox-warning',
            width    : 400
        });
    },

    /**
     * When the tree's editor is finished with editing/moving/appending a new node to
     * the tree, he sends his request to this tree. The passed argument is an
     * anonymous object with the following fields:
     *
     * mode      - the kind of data manipulation. 'edit' for renaming a node, 'add'
     *             for adding a new node to the tree
     * parent    - the id of the node the edited node belongs to (it's parent node)
     * newParent - the id of the new parent if the mode equals to 'move'
     * child - the actual node that was edited. It's configuration is stored in an
     *         anonymous object with the following fields:
     *         id - the id of the edited node. If this node was created, the
     *              response value of the server will contain it's new db related
     *              id; the node's id has to be changed to this value after the
     *              response returned successfully.
     *         value - the new text value of the node
     *         startValue - the original value of the node. If the request to
     *                      save the changes fails, the node can either be removed
     *                      from the tree (if it was newly appended) or it's text
     *                      value can be reverted to this value (if it was edited)
     *
     *
     */
    saveNode : function(nodeConfig)
    {
        var me = this,
            node = this.getNodeById(nodeConfig.child.id),
            params    = {},
            url       = "";

        node.getUI().showProcessing(true);
        node.disable();

        if (!me.editingNodesStorage) {
            me.editingNodesStorage = {};
        } else if (me.editingNodesStorage[nodeConfig.child.id]) {
            throw("com.conjoon.groupware.email.EmailTree::saveNode - cannot "+
                  "execute request since the editing node was already in the queue.")
        }

        me.editingNodesStorage[nodeConfig.child.id] = {
            mode       : nodeConfig.mode,
            parent     : nodeConfig.parent,
            newParent  : nodeConfig.newParent,
            value      : nodeConfig.child.value,
            startValue : nodeConfig.child.startValue
        };

        switch (nodeConfig.mode) {
            case 'move':
                url    = './groupware/email.folder/move.folder/format/json';
                params = {
                    parentId   : nodeConfig.newParent,
                    parentPath : nodeConfig.newParentPath,
                    id         : nodeConfig.child.id,
                    mode       : nodeConfig.mode,
                    path       : node.attributes.tmpPath
                };
                delete node.attributes.tmpPath;
            break;

            case 'edit':
                url    = './groupware/email.folder/rename.folder/format/json';
                params = {
                    parentId : nodeConfig.parent,
                    id       : nodeConfig.child.id,
                    name     : nodeConfig.child.value,
                    mode       : nodeConfig.mode,
                    path     : node.getPathAsJson('idForPath')
                };
            break;

            case 'add':
                url = './groupware/email.folder/add.folder/format/json';
                params = {
                    parentId : nodeConfig.parent,
                    // this property is actually needed if we need to
                    // restore a previously state, so do not remove it!
                    id   : nodeConfig.child.id,
                    name : nodeConfig.child.value,
                    mode : nodeConfig.mode,
                    path : node.getPathAsJson('idForPath')
                };
            break;
        }

        Ext.Ajax.request({
            url            : url,
            params         : params,
            success        : me.onTreeEditSuccess,
            failure        : me.onRequestFailure,
            scope          : me,
            disableCaching : true
        });
    },

    /**
     * Checks wether a node name is available and compares the node's requested
     * value with those of it's siblings. It does also take pending requests into
     * account which may fail, thus checking if the requested name is already
     * being requested by another node.
     * Returns <tt>true</tt> if the requested name is available, <tt>false</tt>
     * on failure.
     *
     * @param {Ext.tree.TreeNode}
     * @param {String}
     *
     * @return {Boolean} value
     */
    isNodeNameAvailable : function(parentNode, node, value)
    {
        var childs = parentNode.childNodes;
        var value  = value.toLowerCase().trim();

        var pend = null;

        for (var i = 0, max_i = childs.length; i < max_i; i++) {
            if (childs[i].id == node.id) {
                continue;
            }
            if (childs[i].text.trim().toLowerCase() == value) {
                return false;
            }

            if (this.editingNodesStorage) {
                pend = this.editingNodesStorage[childs[i].id];

                if (pend && (pend.value.toLowerCase() == value || pend.startValue.toLowerCase() == value)) {
                    return false;
                }
            }
        }

        return true;
    },


    /**
     * Moves a folder into the trash or any child of the trashbin
     *
     */
    moveFolderToTrash : function (tree, parentNode, node)
    {
        // when the node gets moved within the trash, do not show the message.
        // this can be rarely caused by when the user works in the trash and
        // reorders his folders in there.
        var oldParent = node.parentNode;

        var msg   = Ext.MessageBox;

        msg.show({
            title   : com.conjoon.Gettext.gettext("Confirm - Delete folder"),
            msg     : com.conjoon.Gettext.gettext("The selected folder and all its contents will be moved into the trash bin. Are you sure you want to continue?"),
            buttons : msg.YESNO,
            fn      : function(btn){if (btn == 'yes') {
                          this.proxyAppend(tree, node, oldParent, parentNode);
                      }
            },
            scope   : this,
            icon    : msg.QUESTION,
            scope   : this,
            cls     :'com-conjoon-msgbox-question',
            width   : 375
        });

    },

    /**
     * A proxy method for translating append events into move events.  This is
     * needed when a node gets moved, but the drop is interruped by a dialog prompt.
     * Methods can refer to this if they want to catch up the to the previously
     * interrupted move event.
     *
     *
     */
    proxyAppend : function(tree, node, oldParent, parentNode)
    {
        if (!parentNode.isExpanded()) {
            node.disable();
            node.getUI().showProcessing(true);
            parentNode.expand(
                false,
                false,
                (function(tree, node, oldParent, parentNode){
                    node.enable();
                    node.getUI().showProcessing(false);
                    this.proxyAppend(tree, node, oldParent, parentNode);
                }).createDelegate(this, [tree, node, oldParent, parentNode])
            );
        } else if (this.fireEvent('beforemovenode', tree, node, oldParent, parentNode) !== false) {
                this.suspendEvents();
                parentNode.appendChild(node);
                node.getUI().highlight();
                this.resumeEvents();
                this.fireEvent('movenode', this, node, oldParent, parentNode);
        }
    },

    /**
     *
     */
    updatePendingNodes : function(store, record, operation)
    {
        if (operation == 'edit') {

            var node = this.getNodeForPath(record.id);
            if (node) {
                if (record.data.pending < 0) {
                    record.data.pending = 0;
                }
                node.getUI().updatePending(
                    record.data.pending,
                    (node.attributes.type == 'draft'
                        || node.attributes.type == 'outbox'));
                //node.getUI().updatePending(record.data.pending);
            }
        }
        /*var modified = this.pendingItemStore.getModifiedRecords();
        var node     = null;
        for (var i = 0, max_i = modified.length; i < max_i; i++) {
            node = this.getNodeById(modified[i].id);
            if (node) {
                node.getUI().updatePending(modified[i].data.pending);
            }
        } */
    },

// ----------------------------- Listeners -------------------------------------

    /**
     * Listener for the tree's beforedragstart event.
     * Returns false if the node is part of a proxy subtree.
     * Otherwise, drag allowed/disallowed will be handled by the
     * node's attributes "draggable"/"dragAllowed" which will be set in the
     * TreeLoader.
     *
     * @param {Tree} tree the owner tree
     * @param {Object} data
     * @param {Event} e
     */
    onBeforeDragStart : function(tree, data, e) {

        var me = this;

        return me.folderService.isPartOfProxySubtree(data.node) === false;
    },

    /**
     *
     * @param dropEvent
     * @return {Boolean}
     */
    onBeforeNodeDrop : function(dropEvent)
    {
        if (dropEvent.target.disabled) {
            return false;
        }

        var source = dropEvent.source;
        if (source instanceof Ext.ux.grid.livegrid.DragZone) {
            // Cancel the event and fire the nodedrop event
            // if the source was selections from the email grid
            this.fireEvent("nodedrop", dropEvent);
            return false;
        }

        // temp store the path
        var node = dropEvent.data.node;
        node.attributes.tmpPath = node.getPathAsJson('idForPath');

        return true;
    },

    /**
     * tree - The TreePanel
     * target - The node being targeted for the drop
     * data - The drag data from the drag source
     * point - The point of the drop - append, above or below
     * source - The drag source
     * rawEvent - Raw mouse event
     * dropNode - Drop node(s) provided by the source.
     * cancel - Set this to true to signal drop not allowed.
     */
    onNodeDragOver : function(dragOverEvent)
    {
        var me = this,
            source = dragOverEvent.source;

        if (me.folderService.isPartOfProxySubtree(dragOverEvent.target)) {
            me.folderService.loadNextParentProxyNode(dragOverEvent.target);

            return false;
        }

        if (dragOverEvent.target.disabled) {
            return false;
        }

        var targetPath = dragOverEvent.target.getPathAsArray('idForPath'),
            sourcePath;


        if (source instanceof Ext.ux.grid.livegrid.DragZone) {

            var sourceNode = dragOverEvent.data.grid.controller.treePanel.clkNode;

            // this should not happen except for very slow connections
            // where it takes some time to get the response from the server
            // i.e. by the time the grid was loaded a "select" should
            // also have triggered the load of the proxy node which might
            // not be available yet
            if (me.folderService.isPartOfProxySubtree(sourceNode)) {
                me.folderService.loadNextParentProxyNode(sourceNode);
                return false;
            }

            sourcePath = sourceNode.getPathAsArray('idForPath');

            if (sourcePath[1] != targetPath[1]) {
                return false;
            }

            /**
             * @ticket CN-825
             */
            if (Ext.util.JSON.encode(sourcePath) === Ext.util.JSON.encode(targetPath)) {
                return false;
            }

            return source.isDropValid === true;
        }

        sourcePath = dragOverEvent.data.node.getPathAsArray('idForPath');


        if (sourcePath[1] != targetPath[1]) {
            return false;
        }

        return true;
    },

    /**
     * Gets called before a folder is moved to a new location. Checks the node
     * names and cancels operation if a folder with an equal name of the folder
     * to append is already a child of the parent node.
     *
     */
    onBeforeFolderAppend : function(tree, parentNode, node)
    {
        // first off, check if oldParent equals to new Parent
        if ((node.parentNode && (node.parentNode.id == parentNode.id))) {
            return false;
        }

        // secondly, check if the folder moves to the trash.
        // this check will only happen if the node is not already in the
        // trashbin
        try {
            if (node.getPath('type').indexOf('/trash') == -1 &&
                parentNode.getPath('type').indexOf('/trash') != -1) {
                this.moveFolderToTrash(tree, parentNode, node);
                return false;
            }
        } catch(e) {
            // ignore
        }
    },

    onBeforeFolderMove : function(tree, node, oldParent, newParent, index)
    {
        if (newParent.disabled) {
            return false;
        }
        // check if no node with this name is in the new parent's node
        // repository
        if (!this.isNodeNameAvailable(newParent, node, node.text)) {
            var msg   = Ext.MessageBox;
            msg.show({
                title   : com.conjoon.Gettext.gettext("Warning - Move folder"),
                msg     : com.conjoon.Gettext.gettext("A folder with the same name does already exist in the target folder. Please specify another name"),
                buttons : msg.OK,
                icon    : msg.WARNING,
                scope   : this,
                cls     :'com-conjoon-msgbox-warning',
                width   : 400
            });

            return false;
        }
    },

    /**
     * Gets called after the node has been moved to a new location. If the node
     * was moved to the trash and has not yet been in the trash, the node deleted
     * event gets called recursively for each child node.
     *
     */
    onFolderMove : function(tree, node, oldParent, newParent, index)
    {
        // gets called upon tree rendering. Skip if newParent and index
        // equal to undefined
        if (newParent == undefined && index == undefined) {
            return;
        }

        var nodeConfig = {
            mode          : 'move',
            parent        : oldParent.id,
            newParent     : newParent.id,
            newParentPath : newParent.getPathAsJson('idForPath'),
            child     : {
                id         : node.id,
                value      : node.text,
                startValue : node.text
            }
        };

        if (!node.attributes.tmpPath) {
            throw("tmpPath must be specified!");
            // tmp store the old path of the node.
            // needed to move from one path to another successfully
            // this is done in the beforedrop event, but if the node gets
            // manually moved when moving a node to the trash,
            // this might be needed.
            //var p = oldParent.getPathAsArray('idForPath');
            //node.attributes.tmpPath = Ext.util.JSON.encode(p);
        }

        this.saveNode(nodeConfig);
    },

    /**
     * The global callback if editing the tree fails.
     *
     * Note, that if the reset State is about to revert changes, and any editor
     * is visible, a TaskRunner will be invoked to check if the state can be
     * reverted. As soon as the editor hides, the state for the failed component
     * will be undone.
     */
    onRequestFailure : function(response, parameters)
    {
        if (this.nodeEditor.isVisible() && !this.taskQueue[parameters.params.id]) {
            if (this.taskRunner == null) {
                 this.taskRunner = new Ext.util.TaskRunner();
            }
            var task = {
                run      : this.onRequestFailure,
                interval : 500,
                scope    : this,
                args     : [response, parameters]
            }
            this.taskQueue[parameters.params.id] = true;
            var runner = new Ext.util.TaskRunner();
            this.taskRunner.start(task);
            return;
        } else  if (this.nodeEditor.isVisible() && this.taskQueue[parameters.params.id]) {
            return;
        } else if (this.taskRunner != null) {
            this.taskQueue[parameters.params.id] = false;
            this.taskRunner.stopAll();
        }

        var mode  = this.editingNodesStorage[parameters.params.id].mode;

        var msgAdd = "";

        switch (mode) {
            case 'move':
                msgAdd = com.conjoon.Gettext.gettext("Error - Move folder");
            break;

            case 'edit':
                msgAdd = com.conjoon.Gettext.gettext("Error - Rename folder");
            break;

            case 'add':
                msgAdd = com.conjoon.Gettext.gettext("Error - Add folder");
            break;
        }

        this.resetState(parameters.params.id, true);

        com.conjoon.groupware.ResponseInspector.handleFailure(response);
    },

    /**
     * Callback for a server request to either move, add or edit a folder.
     *
     * @param response
     * @param parameters
     *
     * @return {Boolean} true if moving succeeded, otherwise false
     */
    onTreeEditSuccess : function(response, parameters) {

        // shorthands
        var me = this,
            json = com.conjoon.util.Json,
            msg  = Ext.MessageBox,
            values;

        // the method on the server is intended to always return true on success,
        // and an error if anything failed.
        if (json.isError(response.responseText)) {
            me.onRequestFailure(response, parameters);
            return false;
        }

        values = json.getResponseValues(response.responseText);

        me.resetState(parameters.params.id, false, values.folder);

        return true;
    },

    /**
     * Resets the state of a node after successfull/ failed edit/add.
     *
     */
    resetState : function(nodeId, failure, newId)
    {
        var me = this,
            mode = this.editingNodesStorage[nodeId].mode,
            parentId = this.editingNodesStorage[nodeId].parent,
            node = this.getNodeById(nodeId),
            conf = failure ? null : {
                mode : mode,
                oldParentId : parentId,
                node : node,
                nodeId : nodeId,
                newId : newId.id,
                idForPath : newId.idForPath,
                pendingCount : newId.pendingCount,
                isSelectable : newId.isSelectable
            };

        node.getUI().showProcessing(false);
        node.enable();

        switch (mode) {

            case 'move':
                if (failure) {
                    // remove the node sliently
                    var parentNode = this.getNodeById(parentId);
                    this.suspendEvents();
                    parentNode.suspendEvents();
                    parentNode.appendChild(node);
                    parentNode.resumeEvents();
                    this.resumeEvents();
                } else {
                    if (Ext.isObject(newId)) {
                        this.changeNodeAttributes(conf);
                    }
                }
            break;

            case 'edit':
                if (failure) {
                    // revert silent
                    node.suspendEvents();
                    node.setText(this.editingNodesStorage[nodeId].startValue);
                    node.resumeEvents();
                } else {
                    if (Ext.isObject(newId)) {
                        this.changeNodeAttributes(conf);
                    }
                }
            break;

            case 'add':
                if (failure) {
                    // remove the node sliently
                    var parentNode = this.getNodeById(parentId);
                    this.suspendEvents();
                    parentNode.suspendEvents();
                    parentNode.removeChild(node);
                    parentNode.resumeEvents();
                    this.resumeEvents();
                } else {
                    if (Ext.isObject(newId)) {
                        this.changeNodeAttributes(conf);
                    }
                }
            break;

        }
        this.requestId = null;

        delete this.editingNodesStorage[nodeId];

    },

    /**
     * Changes all node attributes based on the specified arguments.
     * Does also re-create an entry in pendingItemStore and update the child node
     * count of the parent node target attributes.
     *
     * @param {Object} conf with the following properties:
     *  - String} mode
     *  - {String} oldParentId
     *  - {Ext.tree.Node} node
     *  - {String} oldId
     *  - {String} newId
     *  - {String} idForPath
     *  - {Number} pendingCount
     *  -{Number} isSelectable
     *
     * @throws {conjoon.mail.folder.base.ParentFolderNotFoundException} if no
     * parentfolder for the specified folder was found.
     */
    changeNodeAttributes : function(conf) {

        var me = this,
            node = conf.node,
            parentNode = node.parentNode,
            mode = conf.mode,
            oldParentId = conf.oldParentId,
            oldId = conf.nodeId,
            newId = conf.newId,
            idForPath = conf.idForPath,
            pendingCount = conf.pendingCount,
            isSelectable = conf.isSelectable,
            oldParentNode = me.getNodeById(oldParentId);

        // get old parentNode
        if (mode != 'add') {
            if (!oldParentNode) {
                throw new conjoon.mail.folder.base.ParentFolderNotFoundException(
                    "Expected old parent folder was not found"
                );
            }

            oldParentNode.attributes.childCount = oldParentNode.childNodes.length;
        }

        if (!parentNode) {
            throw new conjoon.mail.folder.base.ParentFolderNotFoundException(
                "Expected parent folder was not found"
            );
        }

        parentNode.attributes.childCount = parentNode.childNodes.length;

        // folder move or (remote)folder renamed
        if (oldId != newId) {
            Ext.fly(node.getUI().elNode).set({'ext:tree-node-id' : newId});
            Ext.fly(node.getUI().elNode).set({'id'               : newId});
            node.id = newId;
        }

        Ext.apply(node.attributes, {
            idForPath : idForPath,
            id : newId,
            isSelectable : isSelectable,
            isChildAllowed : 1,
            isLocked : false,
            type : 'folder'
        });

        node.getUI().setSelectable(isSelectable);

        if (oldId != newId) {
            var tmp = this.nodeHash[oldId];
            this.nodeHash[newId] = tmp;
            delete this.nodeHash[oldId];

            var oldRec = this.pendingItemStore.getById(oldId);

            if (oldRec) {
                this.pendingItemStore.remove(oldRec);
            }

            this.pendingItemStore.add(
                new com.conjoon.groupware.email.PendingNodeItemRecord({
                    pending : pendingCount
                }, node.getPath('idForPath'))
            );
        }
    },

    /**
     * Adds a new node to the expanded parent node and shows the editor for the
     * new node. The default value of the editor field will be computed before
     * the editor shows.
     * If this method gets called on an asynchronous node and expanding the node
     * takes too long (network connection issues), a new request to edit another
     * node may cancel the current, still in queue existing editing process.
     * Appending a new node will also cancel if the user deselected the parent
     * node, i.e. the node for which this method was called.
     *
     * @param {Ext.tree.TreeNode} The parent node to which a new node should be
     *                            appended
     */
    appendAnonymouseNode : function(parent)
    {
        parent.un('expand', this.appendAnonymouseNode, this);

        if (this.nodeEditor.isVisible() || !parent.isSelected()) {
            // means that we took too long to enter this method
            // and another editing process has started, or the user deselected
            // this node and clicked another one. We will skip adding a
            // child to the parent node, since the user most likely forgot
            // about this one, anyway, and selecting anything else he does not
            // focus on might lead to confusion.
            return;
        }

        // if a node with the default text already exists, we alter the node's text
        // slightly to stay unique.
        var childs      = parent.childNodes;
        var text        = "";
        var pos         = -1;
        var occurence   = 0;
        var defaultText = this.anonymousNodeText.toLowerCase().trim();
        for (var i = 0, max_i = childs.length; i < max_i; i++) {
            text = childs[i].text.toLowerCase();
            pos = text.lastIndexOf('(');
            if ((pos != -1 && (text.substr(0, pos).trim() == defaultText))
                || text.trim() == defaultText) {
                occurence++;
            }
        }

        var node = parent.appendChild(new com.conjoon.cudgets.tree.TreeNode({
            text          : this.anonymousNodeText +
                            ((occurence > 0) ? ' ('+occurence+')' : ''),
            pendingCount  : 0,
            childCount    : 0,
            allowChildren : true,
            isLocked      : false,
            type          : 'folder',
            iconCls       : 'com-conjoon-groupware-email-EmailTree-folderIcon',
            uiProvider    : com.conjoon.groupware.email.PendingNodeUI
        }));

        if (!this.appendingNodesStorage) {
            this.appendingNodesStorage = {};
        }

        node.select();
        this.clkNode = node;

        this.startNodeEdit(this.nodeEditor.SAVE);

    },

    /**
     * Listener for item clicks of the context menu of the tree.
     *
     * @param {Ext.menu.BaseItem}
     * @param {Ext.EventObject}
     */
    contextMenuItemClicked : function(item, eventObject)
    {
        var id = item.id;

        switch (id) {

            case 'com.conjoon.groupware.email.EmailTree.nodeContextMenu.deleteItem':
                this.contextMenu.hide();
                this.deleteFolder();
            return;

            case 'com.conjoon.groupware.email.EmailTree.nodeContextMenu.renameItem':
                // hides the contextmenu immediately. If not called before editing the
                // node, the editor may hide itself when the mouse moves quickly
                // over the other menu items
                this.contextMenu.hide();
                this.startNodeEdit(this.nodeEditor.EDIT);
            return;

            case 'com.conjoon.groupware.email.EmailTree.nodeContextMenu.newItem':
                this.contextMenu.hide();
                this.prepareAppend();
            return;

            default:
                return;
        }
    },

    /**
     * Listener for when the component was rendered.
     * Uninstalls the node editors beforeNodeClick so editing the nodes must be
     * called by user.
     */
    onTreeRender : function()
    {
        // uninstalls the node editors beforeNodeClick so editing the nodes must be
        // called by user
        this.un('beforeclick', this.nodeEditor.beforeNodeClick, this.nodeEditor);
    },

    /**
     * Callback for the click event.
     *
     * @param {Ext.tree.TreeNode}
     * @param {Ext.EventObject}
     */
    onPanelClick : function(node, eventObject)
    {
        if (this.nodeEditor.isEditPending()) {
            eventObject.stopEvent();
            return false;
        }
    },

    /**
     * Callback for the beforeexpandnode event.
     *
     * @param {Ext.tree.TreeNode}
     * @param {Boolean}
     * @param {Boolean}
     */
    onBeforeExpandNode : function(node, deep, anim)
    {
        if (this.nodeEditor.isEditPending()) {
            return false;
        }
    },

    /**
     * Callback for the beforecollapsenode event.
     *
     * @param {Ext.tree.TreeNode}
     * @param {Boolean}
     * @param {Boolean}
     */
    onBeforeCollapseNode : function(node, deep, anim)
    {
        if (this.nodeEditor.isEditPending()) {
            return false;
        }
    },

    /**
     * Listener for a nodes beforeselect event. Returns falls and cancels the select
     * event if the node is a proxy node which was not fully loaded yet.
     *
     * @param selModel
     * @param newNode
     * @param oldNode
     *
     * @return {Boolean}
     */
    onBeforeNodeSelect : function(selModel, newNode, oldNode) {

        var me = this,
            ProxyTreeNode = com.conjoon.cudgets.tree.data.ProxyTreeNode,
            parentNode  = newNode.parentNode,
            isParentProxy = parentNode
                          ? (parentNode instanceof ProxyTreeNode) && parentNode.isProxyNode()
                          : false,
            isNodeProxy = (newNode instanceof ProxyTreeNode) &&
                          newNode.isProxyNode(),
            node = isNodeProxy
                   ? newNode
                   : (isParentProxy
                      ? parentNode
                      : null);

        if (node) {
            node.loadProxyNode();
        }

    },

/**
     * Callback for the mousedown event.
     * This listener allows for a more appealing visual feedback of selecting nodes
     * in the tree, as the node gets selected when the mousedown occurs.
     * Note, that while this listener selects the node, all events of the node get
     * suspended, until the selection has finished.
     *
     * @param {Ext.tree.TreeNode}
     * @param {Ext.EventObject}
     */
    onMouseDown : function(node, eventObject)
    {
        if (this.nodeEditor.isEditPending()) {
            eventObject.stopEvent();
            return false;
        }

        var isProxy = (node instanceof com.conjoon.cudgets.tree.data.ProxyTreeNode) &&
                      node.isProxyNode();

        if (node && !node.isSelected() && !isProxy) {
            node.suspendEvents();
            node.select();
            this.clkNode = node;
            node.resumeEvents();
        }

        this.contextMenu.hide();

        return true;
    },

    /**
     * Callback fo the oncontextmenu event.
     * Selects the passed node <tt>node</tt> while suspending all its events,
     * then shows the contextmenu (@link {com.conjoon.groupware.email.NodeContextMenu})
     * for the node. The menu will not be shown if the selected node is the root node.
     *
     * @param {Ext.tree.TreeNode}
     * @param {Ext.EventObject}
     */
    onContextMenu : function(node, eventObject)
    {
        eventObject.stopEvent();

        if (this.nodeEditor.isEditPending()) {
            return false;
        }

        if (node.isRoot) {
            return;
        }

        node.suspendEvents();
        node.select();
        this.clkNode = node;
        this.contextMenu.showForFolderAt(node, eventObject.getXY());
        node.resumeEvents();
    },

    /**
     * The listener for the "expand all" button in the toolbar.
     * Expands all nodes or tries to reload the root if initial loading failed
     * or returned no child-nodes.
     * If the root node has child nodes, but the previous loading of a node failed,
     * the method will look up the last clicked node and reload it. If no last clicked
     * node was found, the method will automatically reload the root node or try to load
     * the lastFailed node.
     *
     */
    _onExpandAllClick : function()
    {
        if (!this.root.hasChildNodes()) {
            // if no child nodes are found, reload the root node.
            this.root.reload();
        } else {

            // if there is currently a node selected, load this
            if (this.clkNode) {
                if (this.lastFailedNode == this.clkNode) {
                    this.clkNode.reload();
                } else {
                    this.clkNode.expand(true);
                }
            } else {
                // no node selected, check if there is a lastFailedNode
                if (this.lastFailedNode) {
                    this.lastFailedNode.reload();
                } else {
                    // expand the root
                    this.root.expand(true);
                }
            }
        }
     },

    /**
     * Listener for the treeloader loadexception event.
     * If there are currently no nodes rendered in the panel, a message will
     * be shown to inform the user that loading the nodes failed.
     *
     * @param {Ext.tree.TreeLoader} treeLoader The treeLoader that triggered the event
     * @param {String} node The node that was requested
     * @param {Object} response The response as returned by the server
     */
    _onTreeLoaderException : function(treeLoader, node, response)
    {
        this.lastFailedNode = node;

        if (node == this.root && !this.root.firstChild) {
            this.showPanelInfo('root_failed');
        }

    },

    /**
     * Listener for the "beforeload" event of the treeloader. Will be
     * removed once the treeloader has successfully loaded child nodes
     * for the root node.
     *
     * @param {Ext.tree.TreeLoader) treeLoader
     * @param {Ext.tree.TreeNode) node
     * @param {Function) callback
     */
    _onTreeLoaderBeforeLoad : function(treeLoader, node, callback)
    {
        if (node == this.root) {
            this.showPanelInfo('root_loading');
        }

    },

    /**
     * Listener for the treeloader's "load" event. Will be removed once the root
     * node has successfully loaded it's children.
     *
     * @param {Ext.tree.TreeLoader) treeLoader
     * @param {Ext.tree.TreeNode) node
     * @param {Object) response
     *
     */
    _onTreeLoaderLoad : function(treeLoader, node, response)
    {
        var resp = com.conjoon.groupware.ResponseInspector.isSuccess(response);

        if (!resp) {
            return;
        }

        if (node == this.root) {
            if (!node.firstChild) {
                this.showPanelInfo('root_empty');
            } else if (this.infoText) {
               Ext.fly(this.infoText).un('click', this._onExpandAllClick, this);
               this.innerCt.dom.removeChild(this.infoText);
               this.treeLoader.un('beforeload', this._onTreeLoaderBeforeLoad, this);
               this.treeLoader.un('load',       this._onTreeLoaderLoad,       this);
               this.infoText = null;

            }
        }

    },

    /**
     * Similiar to the emptyText config in a GridPanel, this method will show
     * an information message in the treepanel.
     *
     * @param {String} type The type of info to show, can be any of "root_failed"
     * (root node failed to load), "root_empty" (root node has no child nodes) or
     * "root_loading" (root node currently loads its children)
     *
     * @private
     */
    showPanelInfo : function(type)
    {
        if (!this.innerCt) {
            return;
        }
        if (!this.infoText) {
            this.infoText = document.createElement('div');
        }

        this.infoText.className = 'infoText';
        Ext.fly(this.infoText).un('click', this._onExpandAllClick, this);
        this.innerCt.dom.appendChild(this.infoText);

        switch (type) {
            case 'root_failed':
                Ext.fly(this.infoText).on('click', this._onExpandAllClick, this);
                Ext.fly(this.infoText).addClass('rootFailed');
                Ext.fly(this.infoText).update(
                    com.conjoon.Gettext.gettext("An error occured while trying to load your email folders. Click here to try again.")
                );
            break;

            case 'root_loading':
                Ext.fly(this.infoText).addClass('rootLoading');
                Ext.fly(this.infoText).update(
                    com.conjoon.Gettext.gettext("Loading...")
                );
            break;

            case 'root_empty':
                Ext.fly(this.infoText).addClass('rootEmpty');
                Ext.fly(this.infoText).on('click', this._onExpandAllClick, this);
                Ext.fly(this.infoText).update(
                    com.conjoon.Gettext.gettext("There are currently no email folders available. Make sure you have added an email-account first. Click here to reload your email-folders.")
                );
            break;
        }

    },

    /**
     * Callback for the treeLoader-event "nodeloaded"
     *
     * @param {Ext.tree.TreeNode} The parent node to which the newly loaded node
     *                            was added
     * @param {Ext.tree.TreeNode} The loaded node itself
     */
    onNodeLoaded : function(parent, node)
    {
        if (parent == this.lastFailedNode) {
            this.lastFailedNode = null;
        }

        var attr = node.attributes;

        if (parent.attributes.type == 'accounts_root') {

            var arr = node.getPathAsArray();

            switch (attr.type) {
                case 'inbox':
                    this.folderInbox = node;
                break;
                case 'spam':
                    this.folderSpam = node;
                break;
                case 'outbox':
                    this.folderOutbox = node;
                break;
                case 'sent':
                    this.folderSent = node;
                break;
                case 'draft':
                    this.folderDraft = node;
                break;
                case 'trash':
                    this.folderTrash = node;
                break;
            }
        }

        // remove any found record first, since nodeload event
        // can be triggered more than one time

        var oldInd = this.pendingItemStore.findExact('id', node.getPath('idForPath'));
        if (oldInd !== -1) {
            this.pendingItemStore.removeAt(oldInd);
        }
        this.pendingItemStore.add(new com.conjoon.groupware.email.PendingNodeItemRecord({
            pending : parseInt(attr.pendingCount)
        }, node.getPath('idForPath')));

    },

    /**
     * Listener for the com.conjoon.groupware.email.account.added message.
     * Tries to reload this tree if no email accounts where configured
     * on initial load. As soon as accounts are available, the panel will
     * try to build the tree.
     *
     * @param {String} subject
     * @param {Object} message
     */
    _onAccountAdded : function(subject, message)
    {
        if (!this.rendered) {
            return;
        }

        if (this.root.firstChild == null) {
            this.root.reload();
        } else {

            var childs  = this.root.childNodes;
            var folder  = message.rootFolder;
            var account = message.account;

            // for now, only IMAP will be considered.
            // do a check first and throw an exception, this can be removed
            // later on when full support for multiple folder hierarchies is given
            for (var i = 0, len = childs.length; i < len; i++) {
                if (childs[i].id == folder.id) {
                    // assume multiple accounts in one folder
                    return;
                }
            }

            // check first if there are already IMAP folders, but no
            // accounts_root
            if (folder.type == 'accounts_root') {
                var node = this.treeLoader.createNode(folder);
                this.root.appendChild(node);
                return;
            }

            // for now, only IMAP will be considered.
            if (account.get('protocol') === 'IMAP') {
                // append a new node!
                var node = this.treeLoader.createNode(folder);
                this.root.appendChild(node);
                return;
            }

            // this should not happen
            throw(
                "com.conjoon.groupware.email.EmailTree._onAccountAdd() - "
                + "could not add folder for new account"
            );
        }
    }

});
