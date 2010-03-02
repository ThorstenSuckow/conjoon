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
 * {com.conjoon.groupware.localCache.options.CachingContainer}
 *
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 *
 * @class com.conjoon.groupware.localCache.options.listener.DefaultCachingContainerListener
 *
 * @constructor
 */
com.conjoon.groupware.localCache.options.listener.DefaultCachingContainerListener = function() {

};

com.conjoon.groupware.localCache.options.listener.DefaultCachingContainerListener.prototype = {

    /**
     * @type {com.conjoon.groupware.localCache.options.CachingContainer} container The
     * container this listener is bound to.
     */
    container : null,

// -------- api

    /**
     * Installs the listeners for the elements found in the container.
     *
     * @param {com.conjoon.groupware.localCache.options.CachingContainer} container
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

        container.mon(
            container.getCacheAllCheckbox(), 'check',
            this.onCacheAllCheck, this
        );

        container.mon(
            container.getRebuildCacheCheckbox(), 'check',
            this.onRebuildCacheCheck, this
        );

        container.mon(
            container.getDisableCacheCheckbox(), 'check',
            this.onDisableCacheCheck, this
        );

        this.container.on('render', this.onContainerRender, this);
    },

// -------- helper


// ------- listeners

    /**
     * Listener for the containers cacheAllCheckbox "check" event.
     *
     * @param {Ext.form.Checkbox} checkbox
     * @param {Boolean} checked
     */
    onCacheAllCheck : function(checkbox, checked)
    {
        this.container.enableAllCacheCheckboxes(!checked);
    },

    /**
     * Listener for the containers rebuildCacheCheckbox "check" event.
     *
     * @param {Ext.form.Checkbox} checkbox
     * @param {Boolean} checked
     */
    onRebuildCacheCheck : function(checkbox, checked)
    {
        var container = this.container;
        var dcb = container.getDisableCacheCheckbox();
        dcb.suspendEvents();
        container.getDisableCacheCheckbox().setValue(false);
        dcb.resumeEvents();
    },

    /**
     * Listener for the containers disableCacheCheckbox "check" event.
     *
     * @param {Ext.form.Checkbox} checkbox
     * @param {Boolean} checked
     */
    onDisableCacheCheck : function(checkbox, checked)
    {
        var container = this.container;
        var rcb = container.getRebuildCacheCheckbox();
        rcb.suspendEvents();
        container.getRebuildCacheCheckbox().setValue(false);
        rcb.resumeEvents();
    },

    /**
     * Listener for the "render" event of this container.
     * Will tell the fileSettingsForm to install the startEditListener
     * on its form fields and check/uncheck the checkboxes according to the
     * registry settings.
     *
     * @param {Ext.Container} container The rendered container.
     */
    onContainerRender : function(container)
    {
        this.container.setCheckboxValuesFromRegistry();
        this.container.getFileSettingsForm().installStartEditListener();
    }

};