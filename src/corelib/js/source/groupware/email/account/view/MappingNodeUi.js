/**
 * conjoon
 * (c) 2007-2015 conjoon.org
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