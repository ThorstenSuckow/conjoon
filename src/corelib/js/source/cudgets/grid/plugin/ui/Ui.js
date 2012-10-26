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

Ext.namespace('com.conjoon.cudgets.grid.plugin.ui');

/**
 * Base class for ui implementations in com.conjoon.cudgets.grid.plugin.
 *
 *
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
 *
 * @class com.conjoon.cudgets.grid.plugin.ui.Ui
 */
com.conjoon.cudgets.grid.plugin.ui.Ui = function(config) {

    config = config || {};

    Ext.apply(this, config);
};

com.conjoon.cudgets.grid.plugin.ui.Ui.prototype = {

    /**
     * @cfg {com.conjoon.cudgets.grid.plugin.listener.Listener} actionListener
     * The actionListener for the ui mmanaged by this class.
     */
    actionListener : null,

    /**
     * @type {com.conjoon.cudgets.grid.plugin.Plugin} plugin The plugin this
     * ui is bound to.
     */
    plugin : null,

    /**
     * Inits the layout of the dialog.
     * gets called from the initComponent's "initComponent()" method.
     *
     * @param {Ext.Dialog} dialog The dialog this ui will manage.
     */
    init : function(plugin)
    {
        if (this.plugin) {
            throw("You may not call \"init()\" of a Ui-class more than once.")
        }

        this.plugin = plugin;

        this.paintUi();
        this.installListeners();
    },

    /**
     * Installs the listeners for this ui.
     * @protected
     */
    installListeners : function()
    {
        this.actionListener.init(this.plugin);
    },

    /**
     * Override with necessary components needed to show up when ui gets
     * initialized.
     * @protected
     */
    paintUi : Ext.emptyFn


};