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

Ext.namespace('com.conjoon.cudgets.grid.data');

/**
 * Uses com.conjoon.cudgets.grid.data.FileRecord
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 *
 * @class com.conjoon.cudgets.grid.data.DefaultFilePanelColumnModel
 * @extends Ext.grid.ColumnModel
 */
com.conjoon.cudgets.grid.data.DefaultFilePanelColumnModel = function(config) {

    config = config || {};

    this.columnRenderer = com.conjoon.cudgets.util.Registry.get(
        'com.conjoon.cudgets.grid.ui.DefaultFilePanelColumnRenderer',
        new com.conjoon.cudgets.grid.ui.DefaultFilePanelColumnRenderer()
    );


    Ext.applyIf(config, {
        columns : this.getColumns()
    });


    com.conjoon.cudgets.grid.data.DefaultFilePanelColumnModel.superclass.constructor.call(this, config);
}

Ext.extend(com.conjoon.cudgets.grid.data.DefaultFilePanelColumnModel, Ext.grid.ColumnModel, {

    /**
     * method will be called once during instantiating an instance of this
     * class.
     *
     */
    getColumns : function()
    {
        if (this.config && this.config.columns) {
            return this.config.columns;
        }

        return this.getColumnsImpl();
    },

    /**
     * Override to return custom set of columns.
     *
     * @return {Array} An array with configured columns
     *
     * @protected
     */
    getColumnsImpl : function()
    {
        return [
            new Ext.grid.Column({
                header    : 'Name',
                dataIndex : 'name',
                renderer  : this.columnRenderer.nameColumnRenderer,
                editable  : true,
                editor    : new Ext.form.TextField()
            })
        ];
    },




});