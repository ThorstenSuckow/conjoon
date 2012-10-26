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

Ext.namespace('com.conjoon.cudgets.grid.listener');

/**
 * An  base class that provides the interface for listeners for
 * {com.conjoon.cudgets.grid.FilePanelContextMenu}
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 *
 * @class com.conjoon.cudgets.grid.listener.DefaultFilePanelContextMenuListener
 *
 * @constructor
 */
com.conjoon.cudgets.grid.listener.DefaultFilePanelContextMenuListener = function() {

};

com.conjoon.cudgets.grid.listener.DefaultFilePanelContextMenuListener.prototype = {

    /**
     * @type {com.conjoon.cudgets.grid.FilePanelContextMenu} menu The menu
     * this listener is bound to.
     */
    menu : null,

    /**
     * @type {String} clsId
     */
    clsId : '9b2b9b78-e30d-4635-9087-851b37c573e3',


// -------- api

    /**
     * Installs the listeners for the elements found in the menu.
     *
     * @param {com.conjoon.cudgets.grid.FilePanelContextMenu} menu The menu
     * this listener is bound to.
     *
     * @packageprotected
     */
    init : function(menu)
    {
        if (this.menu) {
            return;
        }

        this.menu = menu;


    }

// -------- helper

// ------- listeners


};