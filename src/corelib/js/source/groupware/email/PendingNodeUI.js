/**
 * conjoon
 * (c) 2002-2009 siteartwork.de/conjoon.org
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

Ext.namespace('de.intrabuild.groupware.email');

/**
 * @class de.intrabuild.groupware.email.PendingNodeUI
 * @extends Ext.tree.TreeNodeUI
 *
 * A nodeUI implementation that's capable of representing pending items in a folder
 * in a visual appealing format.
 *
 */
de.intrabuild.groupware.email.PendingNodeUI = function(node) {
    de.intrabuild.groupware.email.PendingNodeUI.superclass.constructor.call(this, node);
};

Ext.extend(de.intrabuild.groupware.email.PendingNodeUI, Ext.tree.TreeNodeUI, {

    /**
     * The extra DOM-node for displaying pending item-count.
     */
    pendingNode : null,

    // private
    onTextChange : function(node, text, oldText){
        if(this.rendered){
            this.textNode.innerHTML = Ext.util.Format.htmlEncode(text.trim());
        }
    },

    /**
     *
     */
    initEvents : function()
    {
        de.intrabuild.groupware.email.PendingNodeUI.superclass.initEvents.call(this);

        this.node.addEvents({'mousedown' : true});

        Ext.fly(this.iconNode).on('mousedown',    this.onMouseDown, this);
        Ext.fly(this.textNode).on('mousedown',    this.onMouseDown, this);
        Ext.fly(this.pendingNode).on('mousedown', this.onMouseDown, this);
    },

    /**
     *
     */
    onMouseDown : function(e)
    {
        this.fireEvent("mousedown", this.node, e);
    },


    /**
     * Renders this node in a busy state, i.e. disabled and the laoding icon
     * showing instead of it's originated ui icon.
     */
    showProcessing : function(process)
    {
        if (process) {
            this.addClass("x-tree-node-loading");
            this.anchor.disabled = true;
        } else {
            this.removeClass("x-tree-node-loading");
            this.anchor.disabled = false;
        }
    },

    /**
     * Overwrites the derived implementation by calling the parent function and
     * then appending another <tt>span</tt>-Tag to this node for displaying
     * pending items in the folder represented by this node.
     * It does also add the css-class that formats the display of the inner text
     * appended to the DOM-node, so it does not have to be updated when the
     * text changes (no pending items will be represented by a <tt>&#160;</tt>
     * on which font-based css-styles have no affect).
     *
     */
    renderElements : function(n, a, targetNode, bulkRender)
    {
        de.intrabuild.groupware.email.PendingNodeUI.superclass.renderElements.call(this,
                                                    n, a, targetNode, bulkRender);

        this.pendingNode = Ext.DomHelper.insertHtml('beforeEnd', this.textNode.parentNode,
                            ['<span class="de-intrabuild-groupware-email-EmailTree-itemPending">',
                            (a.pendingCount > 0 ? '('+a.pendingCount+')' : '&#160'),
                            '</span>'].join(''));

        this.textNode.innerHTML = Ext.util.Format.htmlEncode(a.text);
        var type = a.type;
        if (a.pendingCount > 0 && (type != 'draft' && type != 'outbox')) {
            Ext.fly(this.anchor).addClass('de-intrabuild-attr-fontWeight-bold');
        }
    },

    /**
     * Updates the node to display the value passed as the remaining pending items
     * in the folder represented by this node.
     *
     * @param {Number} The amount of pending items stored in the folder represented
     *                 by this node.
     */
    updatePending : function(value, info)
    {
        if (!this.pendingNode) {
            return;
        }

        if (value <= 0) {
            Ext.fly(this.anchor).removeClass('de-intrabuild-attr-fontWeight-bold');
            Ext.fly(this.pendingNode).update('&#160');
        } else {
            if (info !== true) {
                Ext.fly(this.anchor).addClass('de-intrabuild-attr-fontWeight-bold');
            }
            Ext.fly(this.pendingNode).update('('+value+')');
        }

    }

});