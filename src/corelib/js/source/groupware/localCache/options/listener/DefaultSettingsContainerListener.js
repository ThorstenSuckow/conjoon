/**
 * conjoon
 * (c) 2007-2015 conjoon.org
 * licensing@conjoon.org
 *
 * conjoon
 * Copyright (C) 2014 Thorsten Suckow-Homberg/conjoon.org
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
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
 * @author Thorsten Suckow-Homberg <tsuckow@conjoon.org>
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
        Api.onBeforeClear(this.onLocalCacheApiBeforeClear,   this);
        Api.onClearSuccess(this.onLocalCacheApiClearSuccess, this);
        Api.onClearFailure(this.onLocalCacheApiClearFailure, this);
        Api.onBuild(this.onLocalCacheApiBuild,               this);
        Api.onBuildSuccess(this.onLocalCacheApiBuildSuccess, this);
        Api.onBuildFailure(this.onLocalCacheApiBuildFailure, this);

        container.on('destroy', this.onContainerDestroy, this);

    },

// -------- helper

// ------- listeners

    /**
     * Listener for the destroy event of the container.
     *
     * @param {Ext.Container} container
     */
    onContainerDestroy : function(container)
    {
        var Api = com.conjoon.cudgets.localCache.Api;
        Api.unBeforeClear(this.onLocalCacheApiBeforeClear,   this);
        Api.unClearSuccess(this.onLocalCacheApiClearSuccess, this);
        Api.unClearFailure(this.onLocalCacheApiClearFailure, this);
        Api.unBuild(this.onLocalCacheApiBuild,               this);
        Api.unBuildSuccess(this.onLocalCacheApiBuildSuccess, this);
        Api.unBuildFailure(this.onLocalCacheApiBuildFailure, this);
    },

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
        this.container.getCachingContainer().getDisableCacheCheckbox().setValue(false);
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
     * Listener for the local cache Api's "beforeclear" event. Will call the
     * setRequestPending() method of the container.
     *
     * @param {com.conjoon.cudgets.localCache.Adapter} adapter
     */
    onLocalCacheApiBeforeClear : function(adapter)
    {
        this.container.setRequestPending(true, this.container.REQUEST_CLEAR);
    },

    /**
     * Listener for the local cache Api's "clearsuccess" event. Will call the
     * setRequestPending() method of the container.
     *
     * @param {com.conjoon.cudgets.localCache.Adapter} adapter
     */
    onLocalCacheApiClearSuccess : function(adapter)
    {
        this.container.setRequestPending(false);
        if (this.container.getCachingContainer().getDisableCacheCheckbox().
            getValue()) {
                this.container.getCachingContainer().unsetCheckboxes();
                this.container.saveSettings();
        } else if (this.container.getCachingContainer().getRebuildCacheCheckbox().
            getValue()) {
                this.container.getCachingContainer().rebuildCache();
        }
    },

    /**
     * Listener for the local cache Api's "clearfailure" event
     *
     * @param {com.conjoon.cudgets.localCache.Adapter} adapter
     */
    onLocalCacheApiClearFailure : function(adapter)
    {
        this.container.setRequestPending(false);
    },

    /**
     * Listener for the local cache Api's "build" event. Will call the
     * setRequestPending() method of the container.
     *
     * @param {com.conjoon.cudgets.localCache.Adapter} adapter
     */
    onLocalCacheApiBuild : function(adapter)
    {
        this.container.setRequestPending(true, this.container.REQUEST_BUILD);
    },

    /**
     * Listener for the local cache Api's "buildsuccess" event. Will call the
     * setRequestPending() method of the container.
     *
     * @param {com.conjoon.cudgets.localCache.Adapter} adapter
     */
    onLocalCacheApiBuildSuccess : function(adapter)
    {
        this.container.setRequestPending(false);
        this.container.getCachingContainer().getRebuildCacheCheckbox().setValue(
            false
        );
    },

    /**
     * Listener for the local cache Api's "buildfailure" event
     *
     * @param {com.conjoon.cudgets.localCache.Adapter} adapter
     */
    onLocalCacheApiBuildFailure : function(adapter)
    {
        this.container.getCachingContainer().getRebuildCacheCheckbox().setValue(
            false
        );
        this.container.setRequestPending(false);
    }

};