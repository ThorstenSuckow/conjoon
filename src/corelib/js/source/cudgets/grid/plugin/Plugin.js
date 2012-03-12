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
 * The base class for all grid plugins.
 * A grid plugin has a listener that receives events from the grid and it's
 * components a plugin is bound to, and should call only methods from the
 * implementing plugin class upon receiving those events.
 *
 * @class com.conjoon.cudgets.grid.plugin.Plugin
 * @extends {Ext.util.Observable}
 *
 * @constructor
 * @param {Object} config The config object
 * @abstract
 */
com.conjoon.cudgets.grid.plugin.Plugin = function(config) {

    config = config || {};

    Ext.apply(this, config);

    com.conjoon.cudgets.grid.plugin.Plugin.superclass.constructor.call(this);
};

Ext.extend(com.conjoon.cudgets.grid.plugin.Plugin, Ext.util.Observable, {

    /**
     * @cfg {com.conjoon.cudgets.grid.plugin.listener.Listener} actionListener
     * The actionListener for the grid the plugin is bound to.
     * @protected
     */
    actionListener : null,

    /**
     * The {Ext.grid.GridPanel} this plugin is bound to.
     * @type {Ext.grid.GridPanel}
     * @protected
     */
    grid : null,

    /**
     * Inits this plugin.
     * Method is API-only. Will be called automatically from the grid this
     * plugin is bound to.
     *
     * @param {Ext.grid.GridPanel} grid
     */
    init : function(grid)
    {
        if (this.grid) {
            return;
        }

        this.grid = grid;

        this.actionListener.init(this);
    },

    /**
     * Returns the grid this plugin is bound to.
     *
     * @return {Ext.grid.GridPanel}
     */
    getGrid : function()
    {
        return this.grid;
    }



});