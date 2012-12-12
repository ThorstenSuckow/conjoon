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

Ext.namespace('com.conjoon.groupware.email.account.view');

/**
 * @class com.conjoon.groupware.email.account.view.MappingNodeUI
 * @extends Ext.tree.TreeNodeUI
 *
 */
com.conjoon.groupware.email.account.view.MappingNodeUi = Ext.extend(Ext.tree.TreeNodeUI, {



    /**
     *
     */
    renderElements : function(n, a, targetNode, bulkRender)
    {

        com.conjoon.groupware.email.account.view.MappingNodeUi.superclass.renderElements.call(this,
            n, a, targetNode, bulkRender
        );

        if (this.checkbox) {
            this.checkbox.style.margin = '0 5px 0 1px';
            this.checkbox.parentNode.insertBefore(
                this.checkbox, this.checkbox.previousSibling
            );
        }

        var cls = "";

        if (!a.isSelectable) {
            cls = 'notSelectable';
        }

        if (cls != "") {
            Ext.fly(this.anchor).addClass(cls);
        }


    }

});