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