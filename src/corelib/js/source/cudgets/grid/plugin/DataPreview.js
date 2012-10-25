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

Ext.namespace('com.conjoon.cudgets.grid.plugin');

/**
 * A plugin which is used to be attached to gridpanels which should show a
 * preview of the data of a selected grid entry.
 *
 * The plugin is capable of firing the following events:
 * - previewLoaded - fired when the preview was successfully loaded into the
 *                   bound ui
 * -
 *
 * @class com.conjoon.cudgets.grid.plugin.DataPreview
 * @extends {com.conjoon.cudgets.grid.plugin.Plugin}
 *
 * @constructor
 * @param {Object} config The config object
 * @abstract
 */
com.conjoon.cudgets.grid.plugin.DataPreview = function(config) {


    this.addEvents(
        /**
         * Fired when data has been fully rendered in the preview component
         * and is ready to be inspected by the client.
         * @event previewloaded
         * @param this
         * @param {Ext.data.Record} a copy(!) of the record that represents the
         * data being previewed
         */
        'previewloaded',
        /**
         * An event fired whenever a client indicates that a more detailed view
         * on the data is wished. This can be implemented by using buttons that
         * trigger this event, or any other logic. The event is here to help
         * delegating any process involved in preparing views for detailed
         * information on the data.
         * @event extendedrequest
         * @param this
         */
        'extendedrequest'
    );

    com.conjoon.cudgets.grid.plugin.DataPreview.superclass.constructor.call(
        this, config
    );
}

Ext.extend(com.conjoon.cudgets.grid.plugin.DataPreview,
    com.conjoon.cudgets.grid.plugin.Plugin, {

    /**
     * @cfg {com.conjoon.cudgets.grid.plugin.dataPreview.ui.PreviewRenderer} ui
     * The com.conjoon.cudgets.grid.plugin.dataPreview.ui.PreviewRenderer used
     * for previewing data.
     * @type {com.conjoon.cudgets.grid.plugin.dataPreview.ui.PreviewRenderer}
     * @protected
     */
    ui : null,

    /**
     * @inheritdoc
     */
    init : function(grid)
    {
        com.conjoon.cudgets.grid.plugin.DataPreview
            .superclass.init.call(this, grid);

        this.ui.init(this);
    },

    /**
     * Hides the preview panel.
     * Returns <tt>false</tt> to prevents bubbling the <tt>close</tt> event
     * to the Ext.Window based on the passed argument <tt>preventBubbling</tt>.
     *
     * @param {boolean} <tt>true</tt> to make the preview window disappear at
     * once, otherwise false to give room for animation effects etc.
     */
    hidePreview : function(disappear)
    {
        this.ui.clearPreview(disappear);
    },

    /**
     * Shows the preview panel using a slide-in animation effect.
     * The preview will not been shown if ctrl or shift was pressed while
     * calling this method.
     *
     * @param {Ext.data.Record} The record for which the preview should be
     * shown
     */
    showPreview : function(record)
    {
        this.ui.showPreviewForId(record.id);
    },

    /**
     * Checks whether a preview window exists for the specified record and
     * will return true if this is the case, otherwise false.
     *
     * @param {Ext.data.Record} record
     *
     * @return {Boolean} true if the preview window exists for the specified
     * record, otherwise false
     */
    isPreviewShownForRecord : function(record)
    {
        return record.get('id') === this.ui.getActivePreviewId();
    }



});