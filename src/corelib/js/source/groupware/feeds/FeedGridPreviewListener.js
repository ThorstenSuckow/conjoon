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

Ext.namespace('com.conjoon.groupware.feeds');

/**
 * @inheritdoc
 */
com.conjoon.groupware.feeds.FeedGridPreviewListener
    = Ext.extend(com.conjoon.groupware.util.PreviewListener, {


    /**
     * @inheritdoc
     */
    onCellDblClick : function(grid, rowIndex, columnIndex, eventObject)
    {
        this.cellClickActive = true;
        this.preview.hide(true);

        var feedItem = grid.getStore().getAt(rowIndex);
        com.conjoon.groupware.feeds.FeedViewBaton.showFeed(feedItem, true);

    }

});