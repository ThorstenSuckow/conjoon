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
     */
    setRequestPending : function(isPending)
    {
        this.requestPending = isPending;
        if (isPending) {
            this.ui.maskContainer(com.conjoon.Gettext.gettext("Saving..."));
        } else {
            this.ui.unmaskContainer();
        }
    },

    /**
     * Saves the configuration.
     *
     */
    saveSettings : function()
    {
        var cachingContainer = this.getCachingContainer();
        var mapping          = cachingContainer.getRegistryCheckboxMapping();
        var values           = [];
        var enableAll        = cachingContainer.getCacheAllCheckbox().getValue();
        var allChecked       = true;
        var checked          = null;
        for (var i in mapping) {
            checked = (enableAll ? true : mapping[i].getValue());
            if (enableAll && !mapping[i].getValue()) {
                mapping[i].setValue(true);
            }
            values.push(
                {key : i, value : checked}
            );

            allChecked = !checked ? false : allChecked;
        }

        if (allChecked) {
            cachingContainer.getCacheAllCheckbox().setValue(true);
        }

        com.conjoon.groupware.Registry.setValues({
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

    }

});

