/**
 * conjoon
 * (c) 2002-2010 siteartwork.de/conjoon.org
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

Ext.namespace('com.conjoon.cudgets.grid.ui');

/**
 * Returns the FilePanels Default Column Renderer.
 *
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 *
 * @class {com.conjoon.cudgets.grid.ui.DefaultFilePanelColumnRenderer}
 */
com.conjoon.cudgets.grid.ui.DefaultFilePanelColumnRenderer = function() {

};

com.conjoon.cudgets.grid.ui.DefaultFilePanelColumnRenderer.prototype = {


    /**
     * The renderer for the FilePanel's name column.
     *
     * @param {Mixed} value
     * @param {Object} metaData
     * @param {com.conjoon.cudgets.data.FileRecord} record
     * @param {Number} rowIndex
     * @param {Number} colIndex
     * @param {Ext.data.Store} store
     */
    nameColumnRenderer : function(value, metaData, record, rowIndex, colIndex, store)
    {
        var FileRecord = com.conjoon.cudgets.data.FileRecord;

        metaData.css = 'row';

        switch (record.get('state')) {
            case FileRecord.STATE_UPLOADING:
                metaData.css = ' row uploading';
                return value;

            case FileRecord.STATE_DOWNLOADING:
                metaData.css = ' row downloading';
                return value;

            case FileRecord.STATE_INVALID:
                metaData.css = ' row invalid';
                return value;
        }

        switch(record.get('location')) {
            case FileRecord.LOCATION_LOCAL:
                metaData.css = ' row local';
                return value;

            case FileRecord.LOCATION_REMOTE:
                metaData.css = ' row '+com.conjoon.cudgets.util.MimeIconFactory.getIconCls(record.get('mimeType'));
                return value;
        }

        return value;
    }

};