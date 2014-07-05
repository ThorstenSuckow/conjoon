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

Ext.namespace('com.conjoon.cudgets.grid.ui');

/**
 * Returns the FilePanels Default Column Renderer.
 *
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
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