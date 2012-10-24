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

Ext.namespace('com.conjoon.cudgets.grid.plugin.listener');

/**
 * The listener class for the com.conjoon.cudgets.grid.plugin namespace.
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 *
 * @class com.conjoon.cudgets.grid.plugin.listener.Listener
 *
 * @constructor
 */
com.conjoon.cudgets.grid.plugin.listener.Listener = function(config) {

    config = config || {};

    Ext.apply(this, config);

};

com.conjoon.cudgets.grid.plugin.listener.Listener.prototype = {

    /**
     * The plugin this listener-class is bound to.
     * @type {com.conjoon.cudgets.grid.plugin.Plugin} plugin
     * @protected
     */
    plugin : null,

// -------- api
    /**
     * Installs the listeners for the grid.
     *
     * @param {com.conjoon.cudgets.grid.plugin.PreviewWindow} plugin The plugin
     * this listener-class is bound to.
     *
     * @packageprotected
     */
    init : function(plugin)
    {
        if (this.plugin) {
            throw(
                "You may not call \"init()\" of a Listener-class "
                +"more than once."
            )
        }

        this.plugin  = plugin;
    }

};