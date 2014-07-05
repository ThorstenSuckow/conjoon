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

/**
 * Provides common override functionality for Ext components with bugs
 * that have not yet been fixed in a maintenance release.
 */

/*@REMOVE@*/
if (Ext.version != '3.4.0') {
    throw(
        "Check override for " +
        "Ext.grid.GridView.handleHdMenuClickDefault " +
        "in ext-bug-overrides.js"
    );
}
/*@REMOVE@*/

/**
 * @bug 3.4 we override this since in Ext 3.4 "false" is not returned
 * in case if the last remaining column is about to be "checked hidden"
 */
Ext.override(Ext.grid.GridView, {

    handleHdMenuClickDefault: function(item) {
        var colModel = this.cm,
            itemId   = item.getItemId(),
            index    = colModel.getIndexById(itemId.substr(4));

        if (index != -1) {
            if (item.checked && colModel.getColumnsBy(this.isHideableColumn, this).length <= 1) {
                this.onDenyColumnHide();
                // THIS! return false!!! Missing in 3.4
                return false;
            }
            colModel.setHidden(index, item.checked);
        }
    }

});