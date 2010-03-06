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

Ext.namespace('com.conjoon.groupware.localCache.options.listener');

/**
 * An  base class that provides the interface for listeners for
 * {com.conjoon.groupware.localCache.options.SettingsContainer}
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 *
 * @class com.conjoon.groupware.localCache.options.listener.DefaultSettingsContainerListener
 *
 * @constructor
 */
com.conjoon.groupware.localCache.options.listener.DefaultSettingsContainerListener = function() {

};

com.conjoon.groupware.localCache.options.listener.DefaultSettingsContainerListener.prototype = {

    /**
     * @type {com.conjoon.groupware.localCache.options.SettingsContainer} container The
     * container this listener is bound to.
     */
    container : null,

// -------- api

    /**
     * Installs the listeners for the elements found in the container.
     *
     * @param {com.conjoon.groupware.localCache.options.SettingsContainer} container
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

        container.on('beforeset',  this.onBeforeSet,  this);
        container.on('setsuccess', this.onSetSuccess, this);
        container.on('setfailure', this.onSetFailure, this);

        var Api = com.conjoon.cudgets.localCache.Api;
        Api.onBeforeClear(this.onCachingContainerBeforeClear,   this);
        Api.onClearSuccess(this.onCachingContainerClearSuccess, this);
        Api.onClearFailure(this.onCachingContainerClearFailure, this);

    },

// -------- helper

// ------- listeners

    /**
     * Listener for this container's setsuccess event.
     *
     * @param {com.conjoon.groupware.localCache.options.SettingsContainer}
     * container
     * @param {Object} provider
     * @param {Object} response
     * @param {Array} updated
     */
    onSetSuccess : function(container, response, updated, failed)
    {
        this.container.setRequestPending(false);
        this.container.getCachingContainer().getFileSettingsForm().installStartEditListener();
    },

    /**
     * Listener for this container's setfailure event.
     *
     * @param {com.conjoon.groupware.localCache.options.SettingsContainer}
     * container
     * @param {Object} provider
     * @param {Object} response
     * @param {Array} updated
     * @param {Array} failed
     */
    onSetFailure : function(container, response, updated, failed)
    {
        com.conjoon.groupware.ResponseInspector.handleFailure(response);

        this.container.setRequestPending(false);
        this.container.getCachingContainer().setCheckboxValuesFromRegistry();
        this.container.getCachingContainer().getFileSettingsForm().installStartEditListener();
    },

    /**
     * Listener for the settingsContainer's "beforeset" event. Will call the
     * setRequestPending() method of the container.
     *
     * @param {com.conjoon.groupware.localCache.options.SettingsContainer}
     * settingsContainer
     */
    onBeforeSet : function(settingsContainer)
    {
        this.container.setRequestPending(true, this.container.REQUEST_SET);
    },

    /**
     * Listener for the cachingContainer's "beforeclear" event. Will call the
     * setRequestPending() method of the container.
     *
     * @param {com.conjoon.groupware.localCache.options.CachingContainer}
     * cachingContainer
     */
    onCachingContainerBeforeClear : function(cachingContainer)
    {
        this.container.setRequestPending(true, this.container.REQUEST_CLEAR);
    },

    /**
     * Listener for the cachingContainer's "beforeclear" event. Will call the
     * setRequestPending() method of the container.
     *
     * @param {com.conjoon.groupware.localCache.options.CachingContainer}
     * cachingContainer
     */
    onCachingContainerClearSuccess : function(cachingContainer)
    {
        this.container.setRequestPending(false);
    },

    /**
     * Listener for the cachingContainer's "clearfailure" event
     *
     * @param {com.conjoon.groupware.localCache.options.CachingContainer}
     * cachingContainer
     * @param {Object} response
     */
    onCachingContainerClearFailure : function(cachingContainer, response)
    {
        com.conjoon.groupware.ResponseInspector.handleFailure(response);

        this.container.setRequestPending(false);
    }

};