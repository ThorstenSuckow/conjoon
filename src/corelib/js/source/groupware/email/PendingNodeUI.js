/**
 * conjoon
 * (c) 2007-2014 conjoon.org
 * licensing@conjoon.org
 *
 * conjoon
 * Copyright (C) 2014 Thorsten Suckow-Homberg/conjoon.org
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
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
 * @class com.conjoon.groupware.email.PendingNodeUI
 * @extends Ext.tree.TreeNodeUI
 *
 * A nodeUI implementation that's capable of representing pending items in a folder
 * in a visual appealing format.
 *
 */
com.conjoon.groupware.email.PendingNodeUI = function(node) {
    com.conjoon.groupware.email.PendingNodeUI.superclass.constructor.call(this, node);
};

Ext.extend(com.conjoon.groupware.email.PendingNodeUI, Ext.tree.TreeNodeUI, {

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
        com.conjoon.groupware.email.PendingNodeUI.superclass.initEvents.call(this);

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
     * Applies ui to this node to indicate that this node is a proxy node.
     *
     * @param {Boolean}
     */
    showProxy : function(isProxy)
    {
        if (isProxy) {
            Ext.fly(this.anchor).addClass('proxyNode');
        } else {
            Ext.fly(this.anchor).removeClass('proxyNode');
        }
    },

    /**
     * Renders the node either selectable or not selectable.
     *
     * @param {Boolean} selectable
     */
    setSelectable : function(selectable)
    {
        if (selectable) {
            Ext.fly(this.anchor).removeClass('notSelectable');
        } else {
            Ext.fly(this.anchor).addClass('notSelectable');
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
        com.conjoon.groupware.email.PendingNodeUI.superclass.renderElements.call(this,
                                                    n, a, targetNode, bulkRender);

        this.pendingNode = Ext.DomHelper.insertHtml('beforeEnd', this.textNode.parentNode,
                            ['<span class="com-conjoon-groupware-email-EmailTree-itemPending">',
                            (a.pendingCount > 0 ? '('+a.pendingCount+')' : '&#160'),
                            '</span>'].join(''));

        this.textNode.innerHTML = Ext.util.Format.htmlEncode(a.text);
        var type = a.type;

        var cls = ""

        if (a.pendingCount > 0 && (type != 'draft' && type != 'outbox')) {
            cls = 'com-conjoon-attr-fontWeight-bold';
        }
        if (!a.isSelectable) {
            cls = 'notSelectable';
        }

        if (cls != "") {
            Ext.fly(this.anchor).addClass(cls);
        }

        if (a.isProxy) {
            Ext.fly(this.anchor).addClass('proxyNode');
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
            Ext.fly(this.anchor).removeClass('com-conjoon-attr-fontWeight-bold');
            Ext.fly(this.pendingNode).update('&#160');
        } else {
            if (info !== true) {
                Ext.fly(this.anchor).addClass('com-conjoon-attr-fontWeight-bold');
            }
            Ext.fly(this.pendingNode).update('('+value+')');
        }

    }

});
