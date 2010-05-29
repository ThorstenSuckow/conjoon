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

Ext.namespace('com.conjoon.cudgets.grid.data');

/**
 * Uses com.conjoon.cudgets.grid.data.FileRecord
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
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