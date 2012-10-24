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

Ext.namespace('com.conjoon.cudgets.grid.plugin.dataPreview.listener');

com.conjoon.cudgets.grid.plugin.dataPreview.listener.SlideWindowListener = Ext.extend(
    com.conjoon.cudgets.grid.plugin.dataPreview.listener.DataPreviewListener, {




    /**
     * Called manually when new previewComponent is created
     */
    installListenerForPreviewWindow : function(previewWindow)
    {
        previewWindow.mon(previewWindow, 'beforeclose', this.onBeforeClose, this);
        previewWindow.mon(previewWindow, 'move',        this.onMove,        this);

        previewWindow.on('render', function() {
            previewWindow.mon(previewWindow.header, 'dblclick', function(){
                this.onMove();
            }, this);
        }, this, {single : true});
    },

    /**
     * This will take care of never destroying the component. It gets simply
     * hidden and will be reusable.
     */
    onBeforeClose : function()
    {
        this.plugin.hidePreview(true);
        return false;
    },

    onMove : function()
    {
        var ui         = this.plugin.ui;
        var lastRecord = ui.getLastRecord();

        if (lastRecord) {
            this.plugin.fireEvent('extendedrequest', this, lastRecord.copy());
        }

        ui.previewComponent.close();
    }

});