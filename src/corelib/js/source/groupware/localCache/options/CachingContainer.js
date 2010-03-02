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
 * CachingContainer showing caching options for Local Cache.
 *
 * @class com.conjoon.groupware.localCache.options.CachingContainer
 * @extends Ext.Container
 */
com.conjoon.groupware.localCache.options.CachingContainer = Ext.extend(Ext.Container, {

    /**
     * @cfg {com.conjoon.groupware.localCache.options.SettingsContainer}
     * settingsContainer The settingsContainer that holds this card as one of
     * it child components.
     */
    settingsContainer : null,

    /**
     * @type {com.conjoon.groupware.localCache.options.ui.DefaultCachingContainerUi}
     * The ui that manges this container. Defaults to
     * {com.conjoon.groupware.localCache.options.ui.DefaultCachingContainerUi}
     */
    ui : null,

    /**
     * @type {com.conjoon.cudgets.settings.Card} fileSettingsForm The form for the file settings.
     */
    fileSettingsForm : null,

    /**
     * @type {com.conjoon.cudgets.settings.Card} troubleShootingForm The form for
     * troubleshoting settings.
     */
    troubleShootingForm : null,

    /**
     * @type {Ext.form.Checkbox} cacheAllCheckbox
     */
    cacheAllCheckbox : null,

    /**
     * @type {Ext.form.Checkbox} cacheImagesCheckbox
     */
    cacheImagesCheckbox : null,

    /**
     * @type {Ext.form.Checkbox} cacheJavascriptCheckbox
     */
    cacheJavascriptCheckbox : null,

    /**
     * @type {Ext.form.Checkbox} cacheStylesheetsCheckbox
     */
    cacheStylesheetsCheckbox : null,

    /**
     * @type {Ext.form.Checkbox} cacheSoundsCheckbox
     */
    cacheSoundsCheckbox : null,

    /**
     * @type {Ext.form.Checkbox} cacheHtmlCheckbox
     */
    cacheHtmlCheckbox : null,

    /**
     * @type {Ext.form.Checkbox} cacheFlashCheckbox
     */
    cacheFlashCheckbox : null,

    /**
     * @type {Ext.form.Checkbox} rebuildCacheCheckbox
     */
    rebuildCacheCheckbox : null,

    /**
     * @type {Ext.form.Checkbox} disableCacheCheckbox
     */
    disableCacheCheckbox : null,

// -------- Ext.Window

    /**
     * Inits this component.
     *
     */
    initComponent : function()
    {
        if (!this.ui) {
            this.ui = new com.conjoon.groupware.localCache.options.ui
                          .DefaultCachingContainerUi();
        }

        this.ui.init(this);

        com.conjoon.groupware.localCache.options.CachingContainer
        .superclass.initComponent.call(this);
    },

// -------- api

    /**
     * Returns the fileSettingsForm for this container.
     *
     * @return {com.conjoon.cudgets.settings.Card}
     */
    getFileSettingsForm : function()
    {
        if (!this.fileSettingsForm) {
            this.fileSettingsForm = this.ui.buildFileSettingsForm();
        }

        return this.fileSettingsForm;
    },

    /**
     * Returns the troubleShootingForm for this container.
     *
     * @return {com.conjoon.cudgets.settings.Card}
     */
    getTroubleShootingForm : function()
    {
        if (!this.troubleShootingForm) {
            this.troubleShootingForm = this.ui.buildTroubleShootingForm();
        }

        return this.troubleShootingForm;
    },

    /**
     * Builds the checkbox for the "Cache all" option.
     *
     * @return {Ext.form.Checkbox}
     */
    getCacheAllCheckbox : function()
    {
        if (!this.cacheAllCheckbox) {
            this.cacheAllCheckbox = this.ui.buildCacheAllCheckbox();
        }

        return this.cacheAllCheckbox;
    },

    /**
     * Returns the checkbox for the "cache images" option.
     *
     * @return {Ext.form.Checkbox}
     */
    getCacheImagesCheckbox : function()
    {
        if (!this.cacheImagesCheckbox) {
            this.cacheImagesCheckbox = this.ui.buildCacheImagesCheckbox();
        }

        return this.cacheImagesCheckbox;
    },

    /**
     * Returns the checkbox for the "cache javascript" option.
     *
     * @return {Ext.form.Checkbox}
     */
    getCacheJavascriptCheckbox : function()
    {
        if (!this.cacheJavascriptCheckbox) {
            this.cacheJavascriptCheckbox = this.ui.buildCacheJavascriptCheckbox();
        }

        return this.cacheJavascriptCheckbox;
    },

    /**
     * Returns the checkbox for the "cache stylesheets" option.
     *
     * @return {Ext.form.Checkbox}
     */
    getCacheStylesheetsCheckbox : function()
    {
        if (!this.cacheStylesheetsCheckbox) {
            this.cacheStylesheetsCheckbox = this.ui.buildCacheStylesheetsCheckbox();
        }

        return this.cacheStylesheetsCheckbox;
    },

    /**
     * Returns the checkbox for the "cache sounds" option.
     *
     * @return {Ext.form.Checkbox}
     */
    getCacheSoundsCheckbox : function()
    {
        if (!this.cacheSoundsCheckbox) {
            this.cacheSoundsCheckbox = this.ui.buildCacheSoundsCheckbox();
        }

        return this.cacheSoundsCheckbox;
    },

    /**
     * Returns the checkbox for the "cache HTML" option.
     *
     * @return {Ext.form.Checkbox}
     */
    getCacheHtmlCheckbox : function()
    {
        if (!this.cacheHtmlCheckbox) {
            this.cacheHtmlCheckbox = this.ui.buildCacheHtmlCheckbox();
        }

        return this.cacheHtmlCheckbox;
    },

    /**
     * Returns the checkbox for the "cache Flash" option.
     *
     * @return {Ext.form.Checkbox}
     */
    getCacheFlashCheckbox : function()
    {
        if (!this.cacheFlashCheckbox) {
            this.cacheFlashCheckbox = this.ui.buildCacheFlashCheckbox();
        }

        return this.cacheFlashCheckbox;
    },

    /**
     * Enables or disables all cache option/store related checkboxes
     * based on the passed argument.
     *
     * @param {Boolean} enable True to enable all checkboxes, otherwise
     * false
     */
    enableAllCacheCheckboxes : function(enable)
    {
        this.getCacheImagesCheckbox().setDisabled(!enable);
        this.getCacheJavascriptCheckbox().setDisabled(!enable);
        this.getCacheStylesheetsCheckbox().setDisabled(!enable);
        this.getCacheSoundsCheckbox().setDisabled(!enable);
        this.getCacheHtmlCheckbox().setDisabled(!enable);
        this.getCacheFlashCheckbox().setDisabled(!enable);
    },

    /**
     * Returns the checkbox for the "rebuild cache" option.
     *
     * @return {Ext.form.Checkbox}
     */
    getRebuildCacheCheckbox : function()
    {
        if (!this.rebuildCacheCheckbox) {
            this.rebuildCacheCheckbox = this.ui.buildRebuildCacheCheckbox();
        }

        return this.rebuildCacheCheckbox;
    },

    /**
     * Returns the checkbox for the "disable cache" option.
     *
     * @return {Ext.form.Checkbox}
     */
    getDisableCacheCheckbox : function()
    {
        if (!this.disableCacheCheckbox) {
            this.disableCacheCheckbox = this.ui.buildDisableCacheCheckbox();
        }

        return this.disableCacheCheckbox;
    },

    /**
     * Returns a mapping of the checkboxes representing registry entries,
     * whereas the key is the registry key, and the value is the
     * checkbox component.
     *
     * @return {Array}
     */
    getRegistryCheckboxMapping : function()
    {
        return {
            '/client/applicationCache/cache-flash' :
                this.getCacheFlashCheckbox(),
            '/client/applicationCache/cache-images' :
                this.getCacheImagesCheckbox(),
            '/client/applicationCache/cache-javascript' :
                this.getCacheJavascriptCheckbox(),
            '/client/applicationCache/cache-sounds' :
                this.getCacheSoundsCheckbox(),
            '/client/applicationCache/cache-html' :
                this.getCacheHtmlCheckbox(),
            '/client/applicationCache/cache-stylesheets' :
                this.getCacheStylesheetsCheckbox()
        };

    },

    /**
     * Automatically checks/unchecks checkbox values based on
     * registry settings.
     *
     */
    setCheckboxValuesFromRegistry : function()
    {
        var registry = com.conjoon.groupware.Registry;

        var mappings = this.getRegistryCheckboxMapping();

        var all = true;
        var vl  = null;
        for (var i in mappings) {
            vl = registry.get(i);
            mappings[i].setValue(vl);
            if (!vl) {
                all = false;
            }
        }

        this.getCacheAllCheckbox().setValue(all);
    }

});

