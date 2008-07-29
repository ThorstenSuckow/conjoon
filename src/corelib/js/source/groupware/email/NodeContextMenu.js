Ext.namespace('de.intrabuild.groupware.email');

/**
 *
 */

de.intrabuild.groupware.email.NodeContextMenu = function() {

    /**
     * The menu represented by this singleton
     * @param {Ext.menu.Menu}
     */
    var menu = null;

    /**
     * The last clicked node that was associated in showing this menu.
     */
    var clkNode = null;

    /**
     * Shorthands for menu entries
     */
    var openItem   = null;
    var newItem    = null;
    var renameItem = null;
    var deleteItem = null;

    /**
     * Creates this component
     */
    var initComponent = function()
    {
        var menu = new Ext.menu.Menu({
            items: [{
                id   : 'de.intrabuild.groupware.email.EmailTree.nodeContextMenu.newItem',
                text : de.intrabuild.Gettext.gettext("New folder")
              },{
                id   : 'de.intrabuild.groupware.email.EmailTree.nodeContextMenu.renameItem',
                text : de.intrabuild.Gettext.gettext("Rename folder")
              }, '-',{
                id   : 'de.intrabuild.groupware.email.EmailTree.nodeContextMenu.deleteItem',
                text : de.intrabuild.Gettext.gettext("Delete...")
              }]
        });

        var items = menu.items;

        newItem    = items.get(0);
        renameItem = items.get(1);
        deleteItem = items.get(3);

        return menu;
    };


    return {

        /**
         * Returns the menu ecapsulated by this singleton.
         */
        getMenu : function()
        {
            if (menu === null) {
                menu = initComponent();
            }

            menu.on('hide',       this.onHide,       this);
            menu.on('beforeshow', this.onBeforeShow, this);

            return menu;
        },

// ----------------------------- Listeners -------------------------------------
        /**
         * Callback for the onbeforeshow. Enables/disables menu entries
         * based on the attributes of <tt>clkNode</tt>.
         */
        onBeforeShow : function()
        {
            var attrs = clkNode.attributes;

            var lock = clkNode.disabled;

            if (!attrs.allowChildren  || lock) {
                newItem.setDisabled(true);
            }

            if (attrs.isLocked || lock) {
                renameItem.setDisabled(true);
                deleteItem.setDisabled(true);
            }

        },

        /**
         * Callback for when the context menu was hidden.
         * Resets the last active node that was associated in showing the menu
         * to null.
         */
        onHide : function()
        {
            clkNode = null;

            newItem.setDisabled(false);
            renameItem.setDisabled(false);
            deleteItem.setDisabled(false);
        },

        /**
         * Shows this component.
         * If the node is the root node, the menu won't be shown.
         *
         * @param {Ext.tree.TreeNode} The node for which to show the menu
         * @param {Ext.EventObject}   The {Ext.EventObject} that originated this
         *                            method call
         */
        show : function(node, eventObject)
        {
            if (node.isRoot) {
                return;
            }

            var menu = this.getMenu();

            clkNode = node;

            menu.showAt(eventObject.getXY());
        },

        /**
         * Hides this component.
         * Does nothing if the menu was not shown yet.
         *
         */
        hide : function()
        {
            if (menu == null) {
                return;
            }

            menu.hide();
        }




    };


}();