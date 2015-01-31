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

Ext.namespace('com.conjoon.groupware.localCache.options');

/**
 * SettingsContainer holding the various option components for the
 * Local Cache options dialog.
 *
 * @class com.conjoon.groupware.localCache.options.SettingsContainer
 * @extends Ext.Container
 */
com.conjoon.groupware.localCache.options.SettingsContainer = Ext.extend(Ext.Container, {


    /**
     * @type {com.conjoon.groupware.localCache.options.ui.DefaultSettingsContainerUi}
     * The ui that manges this container. Defaults to
     * {com.conjoon.groupware.localCache.options.ui.DefaultSettingsContainerUi}
     */
    ui : null,

    /**
     * @type {com.conjoon.groupware.localCache.options.IntroductionContainer}
     * introductionContainer
     * The introductionContainer showing introductions to the local cache.
     */
    introductionContainer : null,

    /**
     * @type {com.conjoon.groupware.localCache.options.CachingContainer}
     * cachingContainer
     * The cachingContainer showing caching options for the local cache.
     */
    cachingContainer : null,

    /**
     * @type {Boolean} requestPending Set to true if there is currently
     * a server request initiated by this component pending.
     */
    requestPending : false,

    /**
     * @type {mixed} type "constant" for the setRequestPendingMethod. Used when
     * caching container's request is send to clear the cache
     */
    REQUEST_CLEAR : 1,

    /**
     * @type {mixed} type "constant" for the setRequestPendingMethod. Used when
     * this container's request is send to set teh caching options
     */
    REQUEST_SET : 2,

    /**
     * @type {mixed} type "constant" for the setRequestPendingMethod. Used when
     * this container's request is send to build the cache
     */
    REQUEST_BUILD : 3,

// -------- Ext.Window

    /**
     * Inits this component.
     *
     */
    initComponent : function()
    {
        this.addEvents(
            /**
             * @event beforeset
             * @param {com.conjoon.groupware.localCache.options.SettingsContainer}
             * this
             * @param {Array} values an array with key/value pairs that should
             * get written to the server.
             * Called whenever an attempt is made to change local cache
             * settings and save this setting on the server.
             */
            'beforeset',
            /**
             * @event setsuccess
             * Called whenever an attempt to save local cache settings on the
             * server was successfull, i.e. all values could be saved..
             * @param {com.conjoon.groupware.localCache.options.SettingsContainer}
             * this
             * @param {Object} response
             * @param {Array} updated list of updated key/value pairs
             * @param {Array} failed list or key/value pairs that could not be
             * saved, for this event, this will be an empty array
             */
            'setsuccess',
            /**
             * @event setfailure
             * Called whenever an attempt to save local cache settings on the
             * server failed..
             * @param {com.conjoon.groupware.localCache.options.SettingsContainer}
             * this
             * @param {Array} updated list of updated key/value pairs
             * @param {Array} failed list or key/value pairs that could not be
             * saved
             */
            'setfailure'
        );

        if (!this.ui) {
            this.ui = new com.conjoon.groupware.localCache.options.ui
                          .DefaultSettingsContainerUi();
        }

        this.ui.init(this);

        com.conjoon.groupware.localCache.options.SettingsContainer
        .superclass.initComponent.call(this);
    },

// -------- api

    /**
     * Returns true if there is currently a request initiated by this component
     * pending, otherwise false.
     *
     * @return {Boolean}
     */
    isRequestPending : function()
    {
        return this.requestPending;
    },

    /**
     * Returns the introductionContainer for this container.
     *
     * @return {com.conjoon.groupware.localCache.options.IntroductionContainer}
     */
    getIntroductionContainer : function()
    {
        if (!this.introductionContainer) {
            this.introductionContainer = this.ui.buildIntroductionContainer();
        }

        return this.introductionContainer;
    },

    /**
     * Returns the cachingContainer for this container.
     *
     * @return {com.conjoon.groupware.localCache.options.CachingContainer}
     */
    getCachingContainer : function()
    {
        if (!this.cachingContainer) {
            this.cachingContainer = this.ui.buildCachingContainer();
        }

        return this.cachingContainer;
    },

    /**
     * Masks this component an sets the requestPending
     *
     * @param {Boolean} isPending
     * @param {mixed} type
     */
    setRequestPending : function(isPending, type)
    {
        this.requestPending = isPending;
        if (isPending) {

            if (type === this.REQUEST_SET) {
                this.ui.maskContainer(com.conjoon.Gettext.gettext("Saving..."));
            } else if (type === this.REQUEST_BUILD) {
                this.ui.maskContainer(com.conjoon.Gettext.gettext("Building cache..."));
            } else if (type === this.REQUEST_CLEAR) {
                this.ui.maskContainer(com.conjoon.Gettext.gettext("Clearing cache..."));
            }
        } else {
            this.ui.unmaskContainer();
        }
    },

    /**
     * Returns true if there are any outstanding changes in the caching
     * container.
     *
     * @return {Boolean}
     */
    isCachingOptionDirty : function()
    {
        var cachingContainer = this.getCachingContainer(),
            mapping          = cachingContainer.getRegistryCheckboxMapping(),
            enableAll        = cachingContainer.getCacheAllCheckbox().getValue(),
            Registry         = com.conjoon.groupware.Registry,
            checked          = false,
            i                = null;

        for (var i in mapping) {
            checked = (enableAll ? true : mapping[i].getValue());

            if (Registry.get(i) != checked) {
                return true;
            }
        }

        return false;
    },

    /**
     * Saves the configuration.
     *
     * @return {Boolean} true if there were values which have to be saved
     * server-side, otherwise false
     */
    saveSettings : function()
    {
        var cachingContainer = this.getCachingContainer();
        var mapping          = cachingContainer.getRegistryCheckboxMapping();
        var values           = [];
        var enableAll        = cachingContainer.getCacheAllCheckbox().getValue();
        var allChecked       = true;
        var checked          = null;
        var Registry         = com.conjoon.groupware.Registry;

        for (var i in mapping) {
            checked = (enableAll ? true : mapping[i].getValue());
            if (enableAll && !mapping[i].getValue()) {
                mapping[i].setValue(true);
            }

            if (Registry.get(i) != checked) {
                values.push(
                    {key : i, value : checked}
                );
            }

            allChecked = !checked ? false : allChecked;
        }

        if (allChecked) {
            cachingContainer.getCacheAllCheckbox().setValue(true);
        }

        if (values.length > 0) {
            values.push({
                key   : 'client/applicationCache/last-changed',
                value : (new Date()).getTime()
            });
        }

        var serv = Registry.setValues({
            values: values,
            beforewrite : function(values) {
                this.fireEvent('beforeset', this, values);
            },
            success : function(provider, response, updated, failed) {
                this.fireEvent('setsuccess', this, provider, response, updated, failed);
            },
            failure : function(provider, response, updated, failed) {
                this.fireEvent('setfailure', this, provider, response, updated, failed);
            },
            scope : this
        });

        if (!serv) {
            cachingContainer.getDisableCacheCheckbox().setValue(false);
        }

        return serv;
    }

});

