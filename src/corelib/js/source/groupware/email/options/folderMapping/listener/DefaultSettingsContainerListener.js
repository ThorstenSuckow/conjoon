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

Ext.namespace('com.conjoon.groupware.email.options.folderMapping.listener');

/**
 * An  base class that provides the interface for listeners for
 * {com.conjoon.groupware.email.options.folderMapping.SettingsContainer}
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 *
 * @class com.conjoon.groupware.email.options.folderMapping.listener.DefaultSettingsContainerListener
 *
 * @constructor
 */
com.conjoon.groupware.email.options.folderMapping.listener.DefaultSettingsContainerListener = function() {

};

com.conjoon.groupware.email.options.folderMapping.listener.DefaultSettingsContainerListener.prototype = {

    /**
     * @type {com.conjoon.groupware.email.options.folderMapping.SettingsContainer} container
     * The container this listener is bound to.
     */
    container : null,

// -------- api

    /**
     * Installs the listeners for the elements found in the container.
     *
     * @param {com.conjoon.groupware.email.options.folderMapping.SettigsContainer} container
     * The settings container this listener is bound to.
     *
     * @packageprotected
     */
    init : function(container)
    {
        if (this.container) {
            return;
        }

        this.container = container;
    }

// -------- helper

// ------- listeners


};