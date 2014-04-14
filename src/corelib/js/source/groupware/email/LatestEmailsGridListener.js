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
 * @inheritdoc
 */
com.conjoon.groupware.email.LatestEmailsGridListener
    = Ext.extend(com.conjoon.groupware.util.PreviewListener, {


    /**
     *  @inheritdoc
     */
    isRecordIndexInStoreEqualToRowIndex : function(record, rowIndex, store) {
        return store.indexOf(record) + store.bufferRange[0] === rowIndex
    },

    /**
     *  @inheritdoc
     */
    onCellDblClick : function(grid, rowIndex, columnIndex, eventObject)
    {
        var emailItem = grid.getStore().getAt(rowIndex);

        var lr = this.preview.getLastRecord();

        if (lr && lr.id === emailItem.id) {
            com.conjoon.groupware.email.EmailViewBaton.showEmail(lr, {
                autoLoad : false
            }, true);
        } else {
            com.conjoon.groupware.email.EmailViewBaton.showEmail(emailItem);
        }

    }

});
