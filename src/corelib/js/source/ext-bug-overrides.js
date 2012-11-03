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