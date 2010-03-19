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
 * {com.conjoon.groupware.email.options.folderMapping.OptionsContainer}
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 *
 * @class com.conjoon.groupware.email.options.folderMapping.listener.DefaultOptionsContainerListener
 *
 * @constructor
 */
com.conjoon.groupware.email.options.folderMapping.listener.DefaultOptionsContainerListener = function() {

};

com.conjoon.groupware.email.options.folderMapping.listener.DefaultOptionsContainerListener.prototype = {

    /**
     * @type {com.conjoon.groupware.email.options.folderMapping.OptionsContainer} container
     * The container this listener is bound to.
     */
    container : null,

// -------- api

    /**
     * Installs the listeners for the elements found in the container.
     *
     * @param {com.conjoon.groupware.email.options.folderMapping.OptionsContainer} container
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